<?php
/**
 * Payment Model
 */

class Payment extends Model
{
    protected string $table = 'payments';
    protected array $fillable = [
        'order_id', 'transaction_id', 'gateway', 'method',
        'amount', 'currency', 'status', 'gateway_response'
    ];

    /**
     * Create payment record
     */
    public function createPayment(int $orderId, string $gateway, float $amount, string $method = 'card'): int
    {
        return $this->db->insert('payments', [
            'order_id' => $orderId,
            'gateway' => $gateway,
            'method' => $method,
            'amount' => $amount,
            'currency' => CURRENCY_CODE,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $paymentId, string $status, ?string $transactionId = null, ?array $response = null): bool
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($transactionId) {
            $data['transaction_id'] = $transactionId;
        }

        if ($response) {
            $data['gateway_response'] = json_encode($response);
        }

        return $this->db->update('payments', $data, 'id = ?', [$paymentId]) > 0;
    }

    /**
     * Get payment by order
     */
    public function getByOrder(int $orderId): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1",
            [$orderId]
        );
    }

    /**
     * Get payment stats
     */
    public function getStats(int $storeId = 1): array
    {
        return [
            'total_transactions' => $this->db->count('payments', '1'),
            'completed' => $this->db->count('payments', "status = 'completed'"),
            'pending' => $this->db->count('payments', "status = 'pending'"),
            'failed' => $this->db->count('payments', "status = 'failed'"),
            'refunded' => $this->db->count('payments', "status = 'refunded'")
        ];
    }

    /**
     * Get recent payments
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, o.order_number
             FROM payments p
             JOIN orders o ON p.order_id = o.id
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }
}
