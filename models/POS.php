<?php
/**
 * POS (Point of Sale) Model
 */

class POS extends Model
{
    protected string $table = 'pos_transactions';

    /**
     * Get active terminals
     */
    public function getTerminals(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM pos_terminals WHERE store_id = ? AND is_active = 1 ORDER BY terminal_name",
            [$storeId]
        );
    }

    /**
     * Get current open shift for user
     */
    public function getCurrentShift(int $userId, int $storeId): ?array
    {
        return $this->db->fetch(
            "SELECT ps.*, pt.terminal_name
             FROM pos_shifts ps
             JOIN pos_terminals pt ON ps.terminal_id = pt.id
             WHERE ps.user_id = ? AND ps.store_id = ? AND ps.status = 'open'",
            [$userId, $storeId]
        );
    }

    /**
     * Open a new shift
     */
    public function openShift(array $data): int
    {
        $data['shift_number'] = 'SH' . date('Ymd') . strtoupper(substr(uniqid(), -4));
        $data['opening_time'] = date('Y-m-d H:i:s');
        $data['status'] = 'open';
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->db->insert('pos_shifts', $data);
    }

    /**
     * Close shift
     */
    public function closeShift(int $shiftId, array $data): bool
    {
        // Calculate expected cash
        $shift = $this->db->fetch("SELECT * FROM pos_shifts WHERE id = ?", [$shiftId]);
        if (!$shift) return false;

        // Get total cash sales
        $sales = $this->db->fetch(
            "SELECT
                COALESCE(SUM(total_amount), 0) as total_sales,
                COALESCE(SUM(cash_received - change_amount), 0) as cash_collected,
                COUNT(*) as transaction_count
             FROM pos_transactions
             WHERE shift_id = ? AND status = 'completed'",
            [$shiftId]
        );

        // Get cash in/out
        $cashLogs = $this->db->fetch(
            "SELECT
                COALESCE(SUM(CASE WHEN log_type = 'cash_in' THEN amount ELSE 0 END), 0) as cash_in,
                COALESCE(SUM(CASE WHEN log_type = 'cash_out' THEN amount ELSE 0 END), 0) as cash_out
             FROM pos_cash_logs WHERE shift_id = ?",
            [$shiftId]
        );

        $expectedCash = $shift['opening_cash'] + ($sales['cash_collected'] ?? 0) + ($cashLogs['cash_in'] ?? 0) - ($cashLogs['cash_out'] ?? 0);

        $updateData = [
            'closing_time' => date('Y-m-d H:i:s'),
            'actual_cash' => $data['actual_cash'],
            'expected_cash' => $expectedCash,
            'cash_difference' => $data['actual_cash'] - $expectedCash,
            'total_sales' => $sales['total_sales'] ?? 0,
            'total_transactions' => $sales['transaction_count'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'status' => 'closed'
        ];

        $this->db->update('pos_shifts', $updateData, 'id = ?', [$shiftId]);
        return true;
    }

    /**
     * Create POS transaction
     */
    public function createTransaction(array $data, array $items): int
    {
        $data['transaction_number'] = 'TXN' . date('Ymd') . strtoupper(substr(uniqid(), -4));
        $data['created_at'] = date('Y-m-d H:i:s');

        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);
        }

        $data['subtotal'] = $subtotal;
        $data['total_amount'] = $subtotal - ($data['discount_amount'] ?? 0) + ($data['tax_amount'] ?? 0);

        // Calculate change
        if ($data['payment_method'] === 'cash') {
            $data['change_amount'] = max(0, $data['cash_received'] - $data['total_amount']);
        }

        $transactionId = $this->db->insert($this->table, $data);

        // Insert items and update stock
        foreach ($items as $item) {
            $this->db->insert('pos_transaction_items', [
                'transaction_id' => $transactionId,
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'product_sku' => $item['product_sku'] ?? null,
                'variant_id' => $item['variant_id'] ?? null,
                'variant_info' => $item['variant_info'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'total_price' => ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Update product stock
            if (!empty($item['product_id'])) {
                $this->db->query(
                    "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?",
                    [$item['quantity'], $item['product_id']]
                );
            }
        }

        return $transactionId;
    }

    /**
     * Get transaction with items
     */
    public function getTransactionWithItems(int $id): ?array
    {
        $transaction = $this->db->fetch(
            "SELECT t.*, u.name as cashier_name, pt.terminal_name
             FROM pos_transactions t
             LEFT JOIN users u ON t.created_by = u.id
             LEFT JOIN pos_terminals pt ON t.terminal_id = pt.id
             WHERE t.id = ?",
            [$id]
        );

        if ($transaction) {
            $transaction['items'] = $this->db->fetchAll(
                "SELECT * FROM pos_transaction_items WHERE transaction_id = ?",
                [$id]
            );
        }

        return $transaction;
    }

    /**
     * Get transactions list
     */
    public function getTransactions(int $storeId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = 't.store_id = ?';
        $params = [$storeId];

        if (!empty($filters['date'])) {
            $where .= ' AND DATE(t.created_at) = ?';
            $params[] = $filters['date'];
        }

        if (!empty($filters['shift_id'])) {
            $where .= ' AND t.shift_id = ?';
            $params[] = $filters['shift_id'];
        }

        if (!empty($filters['status'])) {
            $where .= ' AND t.status = ?';
            $params[] = $filters['status'];
        }

        // Count
        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as total FROM pos_transactions t WHERE {$where}",
            $params
        );
        $total = $countResult['total'];
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $transactions = $this->db->fetchAll(
            "SELECT t.*, u.name as cashier_name
             FROM pos_transactions t
             LEFT JOIN users u ON t.created_by = u.id
             WHERE {$where}
             ORDER BY t.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $transactions,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Get shift summary
     */
    public function getShiftSummary(int $shiftId): array
    {
        $summary = $this->db->fetch(
            "SELECT
                COUNT(*) as transaction_count,
                COALESCE(SUM(total_amount), 0) as total_sales,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN cash_received - change_amount ELSE 0 END), 0) as cash_sales,
                COALESCE(SUM(CASE WHEN payment_method = 'card' THEN total_amount ELSE 0 END), 0) as card_sales,
                COALESCE(SUM(CASE WHEN payment_method = 'mobile_banking' THEN total_amount ELSE 0 END), 0) as mobile_sales,
                COALESCE(SUM(discount_amount), 0) as total_discounts
             FROM pos_transactions
             WHERE shift_id = ? AND status = 'completed'",
            [$shiftId]
        );

        return $summary ?: [
            'transaction_count' => 0,
            'total_sales' => 0,
            'cash_sales' => 0,
            'card_sales' => 0,
            'mobile_sales' => 0,
            'total_discounts' => 0
        ];
    }

    /**
     * Get shifts list
     */
    public function getShifts(int $storeId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = 's.store_id = ?';
        $params = [$storeId];

        if (!empty($filters['date'])) {
            $where .= ' AND DATE(s.opening_time) = ?';
            $params[] = $filters['date'];
        }

        if (!empty($filters['user_id'])) {
            $where .= ' AND s.user_id = ?';
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['status'])) {
            $where .= ' AND s.status = ?';
            $params[] = $filters['status'];
        }

        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as total FROM pos_shifts s WHERE {$where}",
            $params
        );
        $total = $countResult['total'];
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $shifts = $this->db->fetchAll(
            "SELECT s.*, u.name as cashier_name, pt.terminal_name
             FROM pos_shifts s
             LEFT JOIN users u ON s.user_id = u.id
             LEFT JOIN pos_terminals pt ON s.terminal_id = pt.id
             WHERE {$where}
             ORDER BY s.opening_time DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $shifts,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Add cash log entry
     */
    public function addCashLog(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('pos_cash_logs', $data);
    }

    /**
     * Get today's POS stats
     */
    public function getTodayStats(int $storeId): array
    {
        $today = date('Y-m-d');

        return $this->db->fetch(
            "SELECT
                COUNT(*) as transaction_count,
                COALESCE(SUM(total_amount), 0) as total_sales,
                COALESCE(AVG(total_amount), 0) as avg_sale,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END), 0) as cash_sales,
                COALESCE(SUM(CASE WHEN payment_method = 'card' THEN total_amount ELSE 0 END), 0) as card_sales
             FROM pos_transactions
             WHERE store_id = ? AND DATE(created_at) = ? AND status = 'completed'",
            [$storeId, $today]
        ) ?: ['transaction_count' => 0, 'total_sales' => 0, 'avg_sale' => 0, 'cash_sales' => 0, 'card_sales' => 0];
    }

    /**
     * Hold current order
     */
    public function holdOrder(int $shiftId, int $storeId, array $items, ?string $customerPhone = null, ?string $note = null): int
    {
        $holdNumber = 'HOLD' . date('His') . rand(10, 99);

        $holdId = $this->db->insert('pos_held_orders', [
            'store_id' => $storeId,
            'shift_id' => $shiftId,
            'hold_number' => $holdNumber,
            'customer_phone' => $customerPhone,
            'items_json' => json_encode($items),
            'note' => $note,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $holdId;
    }

    /**
     * Get held orders for shift
     */
    public function getHeldOrders(int $shiftId): array
    {
        $orders = $this->db->fetchAll(
            "SELECT * FROM pos_held_orders WHERE shift_id = ? ORDER BY created_at DESC",
            [$shiftId]
        );

        foreach ($orders as &$order) {
            $order['items'] = json_decode($order['items_json'], true);
            $order['total'] = array_reduce($order['items'], function($sum, $item) {
                return $sum + ($item['price'] * $item['quantity']);
            }, 0);
        }

        return $orders;
    }

    /**
     * Recall held order
     */
    public function recallOrder(int $holdId): ?array
    {
        $order = $this->db->fetch("SELECT * FROM pos_held_orders WHERE id = ?", [$holdId]);
        if ($order) {
            $order['items'] = json_decode($order['items_json'], true);
        }
        return $order;
    }

    /**
     * Delete held order
     */
    public function deleteHeldOrder(int $holdId): bool
    {
        return $this->db->delete('pos_held_orders', 'id = ?', [$holdId]);
    }

    /**
     * Search customers by phone
     */
    public function searchCustomers(string $query, int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, email, phone,
                    (SELECT COUNT(*) FROM orders WHERE customer_id = users.id) as order_count,
                    (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE customer_id = users.id AND payment_status = 'paid') as total_spent
             FROM users
             WHERE role = 'customer' AND (phone LIKE ? OR name LIKE ? OR email LIKE ?)
             ORDER BY name
             LIMIT ?",
            ["%{$query}%", "%{$query}%", "%{$query}%", $limit]
        );
    }

    /**
     * Get or create customer
     */
    public function getOrCreateCustomer(string $phone, ?string $name = null): array
    {
        $customer = $this->db->fetch(
            "SELECT * FROM users WHERE phone = ? AND role = 'customer'",
            [$phone]
        );

        if (!$customer) {
            $customerId = $this->db->insert('users', [
                'name' => $name ?: 'Walk-in Customer',
                'phone' => $phone,
                'role' => 'customer',
                'password' => password_hash(uniqid(), PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $customer = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$customerId]);
        }

        return $customer;
    }

    /**
     * Create transaction with split payment
     */
    public function createTransactionSplit(array $data, array $items, array $payments): int
    {
        $data['transaction_number'] = 'TXN' . date('Ymd') . strtoupper(substr(uniqid(), -4));
        $data['created_at'] = date('Y-m-d H:i:s');

        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $itemTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);
            $subtotal += $itemTotal;
        }

        $data['subtotal'] = $subtotal;
        $data['total_amount'] = $subtotal - ($data['discount_amount'] ?? 0) + ($data['tax_amount'] ?? 0);

        // Payment method is 'split' for split payments
        $data['payment_method'] = 'split';
        $data['cash_received'] = $payments['cash'] ?? 0;
        $data['card_amount'] = $payments['card'] ?? 0;
        $data['mobile_amount'] = $payments['mobile'] ?? 0;

        // Calculate change (only from cash)
        $totalPaid = ($payments['cash'] ?? 0) + ($payments['card'] ?? 0) + ($payments['mobile'] ?? 0);
        $data['change_amount'] = max(0, $totalPaid - $data['total_amount']);

        $transactionId = $this->db->insert($this->table, $data);

        // Insert items and update stock
        foreach ($items as $item) {
            $this->db->insert('pos_transaction_items', [
                'transaction_id' => $transactionId,
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'product_sku' => $item['product_sku'] ?? null,
                'variant_id' => $item['variant_id'] ?? null,
                'variant_info' => $item['variant_info'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'total_price' => ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Update product stock
            if (!empty($item['product_id'])) {
                $this->db->query(
                    "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?",
                    [$item['quantity'], $item['product_id']]
                );
            }
        }

        return $transactionId;
    }

    /**
     * Process POS refund
     */
    public function processRefund(int $transactionId, array $refundItems, string $reason, int $userId, string $notes = '', string $refundMethod = 'cash'): array
    {
        $transaction = $this->getTransactionWithItems($transactionId);
        if (!$transaction) {
            throw new Exception('Transaction not found');
        }

        $refundAmount = 0;
        foreach ($refundItems as $itemData) {
            $itemId = $itemData['item_id'] ?? 0;
            $qty = $itemData['quantity'] ?? 0;
            $price = $itemData['price'] ?? 0;

            foreach ($transaction['items'] as $item) {
                if ($item['id'] == $itemId) {
                    $refundAmount += ($price * $qty);

                    // Restore stock
                    if ($item['product_id']) {
                        $this->db->query(
                            "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?",
                            [$qty, $item['product_id']]
                        );
                    }
                    break;
                }
            }
        }

        $refundNumber = 'REF' . date('Ymd') . strtoupper(substr(uniqid(), -4));

        // Create refund record
        $refundId = $this->db->insert('pos_refunds', [
            'store_id' => $transaction['store_id'],
            'transaction_id' => $transactionId,
            'refund_number' => $refundNumber,
            'refund_amount' => $refundAmount,
            'refund_method' => $refundMethod,
            'reason' => $reason,
            'items_json' => json_encode($refundItems),
            'notes' => $notes,
            'created_by' => $userId,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Update original transaction
        $this->db->query(
            "UPDATE pos_transactions SET refunded_amount = COALESCE(refunded_amount, 0) + ?,
             status = CASE WHEN COALESCE(refunded_amount, 0) + ? >= total_amount THEN 'refunded' ELSE 'partial_refund' END
             WHERE id = ?",
            [$refundAmount, $refundAmount, $transactionId]
        );

        return [
            'refund_id' => $refundId,
            'refund_number' => $refundNumber,
            'refund_amount' => $refundAmount
        ];
    }

    /**
     * Get product by barcode/SKU
     */
    public function getProductByBarcode(string $barcode, int $storeId): ?array
    {
        return $this->db->fetch(
            "SELECT id, name, sku, price, stock_quantity, image
             FROM products
             WHERE (sku = ? OR barcode = ?) AND store_id = ? AND status = 'active'",
            [$barcode, $barcode, $storeId]
        );
    }

    /**
     * Get daily summary for dashboard widget
     */
    public function getDailySummary(int $storeId, ?int $shiftId = null): array
    {
        $today = date('Y-m-d');

        $whereShift = $shiftId ? " AND shift_id = ?" : "";
        $params = [$storeId, $today];
        if ($shiftId) $params[] = $shiftId;

        $summary = $this->db->fetch(
            "SELECT
                COUNT(*) as transactions,
                COALESCE(SUM(total_amount), 0) as total_sales,
                COALESCE(SUM(discount_amount), 0) as total_discounts,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN cash_received - change_amount ELSE 0 END), 0) as cash_collected,
                COALESCE(SUM(CASE WHEN payment_method = 'card' THEN total_amount ELSE 0 END), 0) as card_collected,
                COALESCE(SUM(CASE WHEN payment_method = 'mobile_banking' THEN total_amount ELSE 0 END), 0) as mobile_collected,
                COALESCE(SUM(CASE WHEN payment_method = 'split' THEN cash_received - change_amount ELSE 0 END), 0) as split_cash,
                COALESCE(SUM(CASE WHEN payment_method = 'split' THEN card_amount ELSE 0 END), 0) as split_card,
                COALESCE(SUM(CASE WHEN payment_method = 'split' THEN mobile_amount ELSE 0 END), 0) as split_mobile,
                COALESCE(SUM(refunded_amount), 0) as total_refunds,
                COALESCE(AVG(total_amount), 0) as avg_transaction
             FROM pos_transactions
             WHERE store_id = ? AND DATE(created_at) = ? AND status != 'voided'{$whereShift}",
            $params
        );

        // Calculate totals including split
        $summary['total_cash'] = ($summary['cash_collected'] ?? 0) + ($summary['split_cash'] ?? 0);
        $summary['total_card'] = ($summary['card_collected'] ?? 0) + ($summary['split_card'] ?? 0);
        $summary['total_mobile'] = ($summary['mobile_collected'] ?? 0) + ($summary['split_mobile'] ?? 0);

        // Get top selling items today
        $summary['top_items'] = $this->db->fetchAll(
            "SELECT ti.product_name, SUM(ti.quantity) as qty_sold, SUM(ti.total_price) as revenue
             FROM pos_transaction_items ti
             JOIN pos_transactions t ON ti.transaction_id = t.id
             WHERE t.store_id = ? AND DATE(t.created_at) = ? AND t.status = 'completed'
             GROUP BY ti.product_id, ti.product_name
             ORDER BY qty_sold DESC
             LIMIT 5",
            [$storeId, $today]
        );

        return $summary ?: [
            'transactions' => 0, 'total_sales' => 0, 'total_discounts' => 0,
            'total_cash' => 0, 'total_card' => 0, 'total_mobile' => 0,
            'total_refunds' => 0, 'avg_transaction' => 0, 'top_items' => []
        ];
    }
}
