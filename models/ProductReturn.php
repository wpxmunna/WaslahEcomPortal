<?php
/**
 * Product Return Model
 * Handles product returns and stock restoration
 */

class ProductReturn extends Model
{
    protected string $table = 'returns';
    protected array $fillable = [
        'store_id', 'order_id', 'return_number', 'reason', 'reason_details',
        'refund_amount', 'refund_status', 'admin_notes', 'returned_at'
    ];

    /**
     * Generate unique return number
     */
    public function generateReturnNumber(): string
    {
        $prefix = 'RTN';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return $prefix . $date . $random;
    }

    /**
     * Create return from order
     * Copies all order items and restores stock
     */
    public function createFromOrder(int $orderId, array $returnData): int
    {
        $this->db->getConnection()->beginTransaction();

        try {
            // Get order details
            $order = $this->db->fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
            if (!$order) {
                throw new Exception('Order not found');
            }

            // Check if return already exists for this order
            $existing = $this->db->fetch("SELECT id FROM returns WHERE order_id = ?", [$orderId]);
            if ($existing) {
                throw new Exception('Return already exists for this order');
            }

            // Generate return number
            $returnData['return_number'] = $this->generateReturnNumber();
            $returnData['store_id'] = $order['store_id'];
            $returnData['order_id'] = $orderId;
            $returnData['returned_at'] = date('Y-m-d H:i:s');
            $returnData['created_at'] = date('Y-m-d H:i:s');

            // Determine refund status based on payment method
            if ($order['payment_method'] === 'cod' || $order['payment_status'] !== 'paid') {
                $returnData['refund_status'] = 'not_required';
                $returnData['refund_amount'] = 0;
            } else {
                $returnData['refund_status'] = 'pending';
                $returnData['refund_amount'] = $order['total_amount'];
            }

            // Create return record
            $returnId = $this->db->insert('returns', $returnData);

            // Get order items and copy to return_items
            $orderItems = $this->db->fetchAll(
                "SELECT * FROM order_items WHERE order_id = ?",
                [$orderId]
            );

            foreach ($orderItems as $item) {
                // Insert return item
                $this->db->insert('return_items', [
                    'return_id' => $returnId,
                    'order_item_id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'product_name' => $item['product_name'],
                    'variant_info' => $item['variant_info'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'stock_restored' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                // Restore stock
                $this->restoreItemStock($item);
            }

            // Update order status to 'refunded' (closest available status for returns)
            $this->db->update('orders', [
                'status' => 'refunded',
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$orderId]);

            $this->db->getConnection()->commit();
            return $returnId;

        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * Restore stock for a single item
     */
    private function restoreItemStock(array $item): void
    {
        // Restore to variant stock if variant exists
        if ($item['variant_id']) {
            $this->db->query(
                "UPDATE product_variants SET stock_quantity = stock_quantity + ? WHERE id = ?",
                [$item['quantity'], $item['variant_id']]
            );
        }

        // Always restore to main product stock
        $this->db->query(
            "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?",
            [$item['quantity'], $item['product_id']]
        );
    }

    /**
     * Get return with full details
     */
    public function getWithDetails(int $returnId): ?array
    {
        $return = $this->find($returnId);

        if ($return) {
            // Get order info
            $return['order'] = $this->db->fetch(
                "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone
                 FROM orders o
                 LEFT JOIN users u ON o.user_id = u.id
                 WHERE o.id = ?",
                [$return['order_id']]
            );

            // Get return items
            $return['items'] = $this->db->fetchAll(
                "SELECT ri.*,
                        (SELECT image_path FROM product_images WHERE product_id = ri.product_id ORDER BY is_primary DESC LIMIT 1) as image
                 FROM return_items ri
                 WHERE ri.return_id = ?",
                [$returnId]
            );
        }

        return $return;
    }

    /**
     * Get returns for admin listing
     */
    public function getAdminReturns(int $page = 1, int $perPage = 20, int $storeId = 1, array $filters = []): array
    {
        $where = 'r.store_id = ?';
        $params = [$storeId];

        if (!empty($filters['reason'])) {
            $where .= ' AND r.reason = ?';
            $params[] = $filters['reason'];
        }

        if (!empty($filters['search'])) {
            $where .= ' AND (r.return_number LIKE ? OR o.order_number LIKE ? OR u.name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $offset = ($page - 1) * $perPage;

        // Count total
        $total = $this->db->fetch(
            "SELECT COUNT(*) as count FROM returns r
             LEFT JOIN orders o ON r.order_id = o.id
             LEFT JOIN users u ON o.user_id = u.id
             WHERE {$where}",
            $params
        )['count'];

        $totalPages = ceil($total / $perPage);

        // Get returns with order info
        $returns = $this->db->fetchAll(
            "SELECT r.*, o.order_number, o.total_amount as order_total,
                    u.name as customer_name,
                    (SELECT COUNT(*) FROM return_items WHERE return_id = r.id) as item_count
             FROM returns r
             LEFT JOIN orders o ON r.order_id = o.id
             LEFT JOIN users u ON o.user_id = u.id
             WHERE {$where}
             ORDER BY r.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $returns,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages
        ];
    }

    /**
     * Get returns by store
     */
    public function getByStore(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT r.*, o.order_number
             FROM returns r
             LEFT JOIN orders o ON r.order_id = o.id
             WHERE r.store_id = ?
             ORDER BY r.created_at DESC",
            [$storeId]
        );
    }

    /**
     * Get eligible orders for return (delivered orders without existing returns)
     */
    public function getEligibleOrders(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT o.id, o.order_number, o.total_amount, o.created_at, o.status,
                    u.name as customer_name
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             WHERE o.store_id = ?
               AND o.status IN ('delivered', 'shipped', 'processing')
               AND o.id NOT IN (SELECT order_id FROM returns)
             ORDER BY o.created_at DESC",
            [$storeId]
        );
    }

    /**
     * Update refund status
     */
    public function updateRefundStatus(int $returnId, string $status): bool
    {
        return $this->update($returnId, ['refund_status' => $status]);
    }

    /**
     * Get return stats
     */
    public function getStats(int $storeId = 1): array
    {
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        return [
            'total_returns' => $this->db->count('returns', 'store_id = ?', [$storeId]),
            'today_returns' => $this->db->count('returns', "store_id = ? AND DATE(created_at) = ?", [$storeId, $today]),
            'month_returns' => $this->db->count('returns', "store_id = ? AND created_at >= ?", [$storeId, $monthStart]),
            'pending_refunds' => $this->db->count('returns', "store_id = ? AND refund_status = 'pending'", [$storeId]),
        ];
    }

    /**
     * Get reason label
     */
    public static function getReasonLabel(string $reason): string
    {
        $labels = [
            'defective' => 'Defective Product',
            'damaged' => 'Damaged in Shipping',
            'wrong_item' => 'Wrong Item Sent',
            'not_as_described' => 'Not as Described',
            'changed_mind' => 'Customer Changed Mind',
            'customer_refused' => 'Customer Refused Delivery',
            'undelivered' => 'Could Not Deliver',
            'other' => 'Other'
        ];
        return $labels[$reason] ?? ucfirst($reason);
    }

    /**
     * Get all reason options
     */
    public static function getReasonOptions(): array
    {
        return [
            'defective' => 'Defective Product',
            'damaged' => 'Damaged in Shipping',
            'wrong_item' => 'Wrong Item Sent',
            'not_as_described' => 'Not as Described',
            'changed_mind' => 'Customer Changed Mind',
            'customer_refused' => 'Customer Refused Delivery',
            'undelivered' => 'Could Not Deliver',
            'other' => 'Other'
        ];
    }
}
