<?php
/**
 * Mock Payment Gateway
 * Simulates payment processing for testing
 */

class PaymentGateway
{
    private Database $db;
    private string $gateway;

    // Available gateways
    const GATEWAY_STRIPE = 'stripe';
    const GATEWAY_PAYPAL = 'paypal';
    const GATEWAY_COD = 'cod';

    public function __construct(string $gateway = 'stripe')
    {
        $this->db = new Database();
        $this->gateway = $gateway;
    }

    /**
     * Process payment
     */
    public function processPayment(int $orderId, array $paymentData): array
    {
        // Simulate processing delay
        usleep(500000); // 0.5 seconds

        $order = $this->db->fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);

        if (!$order) {
            return $this->errorResponse('Order not found');
        }

        // Create payment record
        $paymentModel = new Payment();
        $paymentId = $paymentModel->createPayment(
            $orderId,
            $this->gateway,
            $order['total_amount'],
            $paymentData['method'] ?? 'card'
        );

        // Simulate different gateways
        return match($this->gateway) {
            self::GATEWAY_STRIPE => $this->processStripe($paymentId, $paymentData, $order),
            self::GATEWAY_PAYPAL => $this->processPaypal($paymentId, $paymentData, $order),
            self::GATEWAY_COD => $this->processCOD($paymentId, $order),
            default => $this->errorResponse('Unknown gateway')
        };
    }

    /**
     * Simulate Stripe payment
     */
    private function processStripe(int $paymentId, array $data, array $order): array
    {
        // Simulate card validation
        $cardNumber = $data['card_number'] ?? '';

        // Test card numbers
        // 4242424242424242 - Success
        // 4000000000000002 - Decline
        // Any other 16 digit - Random success/fail

        if ($cardNumber === '4000000000000002') {
            $this->updatePaymentFailed($paymentId, $order['id'], 'Card declined');
            return $this->errorResponse('Card was declined. Please try another card.');
        }

        if ($cardNumber === '4242424242424242' || $this->randomSuccess()) {
            $transactionId = 'ch_' . randomString(24);
            $this->updatePaymentSuccess($paymentId, $order['id'], $transactionId);

            return $this->successResponse([
                'transaction_id' => $transactionId,
                'gateway' => 'stripe',
                'amount' => $order['total_amount'],
                'message' => 'Payment successful'
            ]);
        }

        $this->updatePaymentFailed($paymentId, $order['id'], 'Payment failed');
        return $this->errorResponse('Payment failed. Please try again.');
    }

    /**
     * Simulate PayPal payment
     */
    private function processPaypal(int $paymentId, array $data, array $order): array
    {
        // Simulate PayPal email validation
        $email = $data['paypal_email'] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse('Invalid PayPal email');
        }

        if ($this->randomSuccess(90)) {
            $transactionId = 'PAY-' . strtoupper(randomString(20));
            $this->updatePaymentSuccess($paymentId, $order['id'], $transactionId);

            return $this->successResponse([
                'transaction_id' => $transactionId,
                'gateway' => 'paypal',
                'amount' => $order['total_amount'],
                'payer_email' => $email,
                'message' => 'PayPal payment successful'
            ]);
        }

        $this->updatePaymentFailed($paymentId, $order['id'], 'PayPal payment failed');
        return $this->errorResponse('PayPal payment failed. Please try again.');
    }

    /**
     * Process Cash on Delivery
     */
    private function processCOD(int $paymentId, array $order): array
    {
        $paymentModel = new Payment();
        $paymentModel->updatePaymentStatus($paymentId, 'pending', null, [
            'method' => 'cod',
            'message' => 'Cash on Delivery - Payment pending'
        ]);

        return $this->successResponse([
            'gateway' => 'cod',
            'amount' => $order['total_amount'],
            'status' => 'pending',
            'message' => 'Order placed. Pay on delivery.'
        ]);
    }

    /**
     * Update payment success
     */
    private function updatePaymentSuccess(int $paymentId, int $orderId, string $transactionId): void
    {
        $paymentModel = new Payment();
        $paymentModel->updatePaymentStatus($paymentId, 'completed', $transactionId, [
            'success' => true,
            'processed_at' => date('Y-m-d H:i:s')
        ]);

        $orderModel = new Order();
        $orderModel->updatePaymentStatus($orderId, 'paid');
        $orderModel->updateStatus($orderId, 'processing');
    }

    /**
     * Update payment failed
     */
    private function updatePaymentFailed(int $paymentId, int $orderId, string $reason): void
    {
        $paymentModel = new Payment();
        $paymentModel->updatePaymentStatus($paymentId, 'failed', null, [
            'success' => false,
            'error' => $reason,
            'processed_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Random success (for simulation)
     */
    private function randomSuccess(int $successRate = 85): bool
    {
        return rand(1, 100) <= $successRate;
    }

    /**
     * Success response
     */
    private function successResponse(array $data): array
    {
        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * Error response
     */
    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => $message
        ];
    }

    /**
     * Refund payment
     */
    public function refund(int $paymentId): array
    {
        $payment = $this->db->fetch("SELECT * FROM payments WHERE id = ?", [$paymentId]);

        if (!$payment) {
            return $this->errorResponse('Payment not found');
        }

        if ($payment['status'] !== 'completed') {
            return $this->errorResponse('Can only refund completed payments');
        }

        $paymentModel = new Payment();
        $paymentModel->updatePaymentStatus($paymentId, 'refunded', null, [
            'refund_id' => 're_' . randomString(20),
            'refunded_at' => date('Y-m-d H:i:s')
        ]);

        $orderModel = new Order();
        $orderModel->updatePaymentStatus($payment['order_id'], 'refunded');
        $orderModel->updateStatus($payment['order_id'], 'refunded');

        return $this->successResponse(['message' => 'Payment refunded successfully']);
    }

    /**
     * Get available payment methods
     */
    public static function getAvailableMethods(): array
    {
        return [
            [
                'id' => 'stripe',
                'name' => 'Credit/Debit Card',
                'description' => 'Pay securely with your card',
                'icon' => 'fa-credit-card'
            ],
            [
                'id' => 'paypal',
                'name' => 'PayPal',
                'description' => 'Pay with your PayPal account',
                'icon' => 'fa-paypal'
            ],
            [
                'id' => 'cod',
                'name' => 'Cash on Delivery',
                'description' => 'Pay when you receive your order',
                'icon' => 'fa-money-bill'
            ]
        ];
    }
}
