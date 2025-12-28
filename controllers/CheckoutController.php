<?php
/**
 * Checkout Controller
 */

class CheckoutController extends Controller
{
    private Cart $cartModel;
    private Order $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
    }

    /**
     * Checkout page
     */
    public function index(): void
    {
        $cart = $this->getCart();
        $cartData = $this->cartModel->getTotals($cart['id']);

        if (empty($cartData['items'])) {
            $this->redirect('cart', 'Your cart is empty', 'warning');
            return;
        }

        // Get user addresses if logged in
        $addresses = [];
        if (Session::isLoggedIn()) {
            $userModel = new User();
            $user = $userModel->getWithAddresses(Session::getUserId());
            $addresses = $user['addresses'] ?? [];
        }

        // Get available couriers
        $courierModel = new Courier();
        $couriers = $courierModel->getActive();

        $data = [
            'pageTitle' => 'Checkout - ' . SITE_NAME,
            'cart' => $cartData,
            'addresses' => $addresses,
            'couriers' => $couriers,
            'paymentMethods' => PaymentGateway::getAvailableMethods()
        ];

        $this->view('checkout/index', $data);
    }

    /**
     * Process checkout
     */
    public function process(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('checkout');
            return;
        }

        $cart = $this->getCart();
        $cartData = $this->cartModel->getTotals($cart['id']);

        if (empty($cartData['items'])) {
            $this->redirect('cart', 'Your cart is empty', 'warning');
            return;
        }

        // Prepare order data
        $orderData = [
            'store_id' => Session::get('current_store_id', 1),
            'user_id' => Session::getUserId(),
            'payment_method' => $this->post('payment_method', 'cod'),

            // Shipping address
            'shipping_name' => $this->post('shipping_name'),
            'shipping_phone' => $this->post('shipping_phone'),
            'shipping_address_line1' => $this->post('shipping_address'),
            'shipping_address_line2' => $this->post('shipping_address2'),
            'shipping_city' => $this->post('shipping_city'),
            'shipping_state' => $this->post('shipping_state'),
            'shipping_postal_code' => $this->post('shipping_postal'),
            'shipping_country' => $this->post('shipping_country', 'United States'),

            // Billing (same as shipping for now)
            'billing_name' => $this->post('shipping_name'),
            'billing_phone' => $this->post('shipping_phone'),
            'billing_address_line1' => $this->post('shipping_address'),
            'billing_address_line2' => $this->post('shipping_address2'),
            'billing_city' => $this->post('shipping_city'),
            'billing_state' => $this->post('shipping_state'),
            'billing_postal_code' => $this->post('shipping_postal'),
            'billing_country' => $this->post('shipping_country', 'United States'),

            'notes' => $this->post('notes')
        ];

        try {
            // Create order
            $orderId = $this->orderModel->createFromCart($cartData, $orderData);

            // Get order details
            $order = $this->orderModel->find($orderId);

            // Process payment
            $paymentGateway = new PaymentGateway($orderData['payment_method']);
            $paymentData = [
                'card_number' => $this->post('card_number'),
                'card_expiry' => $this->post('card_expiry'),
                'card_cvv' => $this->post('card_cvv'),
                'paypal_email' => $this->post('paypal_email'),
                'method' => $orderData['payment_method']
            ];

            $paymentResult = $paymentGateway->processPayment($orderId, $paymentData);

            // Create shipment
            $courierId = (int) $this->post('courier_id', 1);
            $courierService = new CourierService();
            $courierService->createShipment($orderId, $courierId);

            // Clear cart
            $this->cartModel->clearCart($cart['id']);

            // Store order in session for confirmation page
            Session::set('last_order_id', $orderId);

            $this->redirect('order/success/' . $order['order_number']);

        } catch (Exception $e) {
            logMessage('Checkout error: ' . $e->getMessage(), 'error');
            $this->redirect('checkout', 'An error occurred. Please try again.', 'error');
        }
    }

    /**
     * Order success page
     */
    public function success(string $orderNumber): void
    {
        $order = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order) {
            $this->redirect('', 'Order not found', 'error');
            return;
        }

        // Verify user owns this order (if logged in)
        if (Session::isLoggedIn() && $order['user_id'] && $order['user_id'] !== Session::getUserId()) {
            $this->redirect('', 'Order not found', 'error');
            return;
        }

        $data = [
            'pageTitle' => 'Order Confirmed - ' . SITE_NAME,
            'order' => $order
        ];

        $this->view('checkout/success', $data);
    }

    /**
     * Track order
     */
    public function track(string $orderNumber): void
    {
        $order = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order) {
            $this->redirect('', 'Order not found', 'error');
            return;
        }

        $data = [
            'pageTitle' => 'Track Order - ' . SITE_NAME,
            'order' => $order
        ];

        $this->view('checkout/track', $data);
    }

    /**
     * Get cart
     */
    private function getCart(): array
    {
        $userId = Session::getUserId();
        $sessionId = session_id();

        return $this->cartModel->getOrCreate($userId, $sessionId);
    }
}
