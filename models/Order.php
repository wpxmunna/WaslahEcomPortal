<?php
/**
 * Order Model
 */

class Order extends Model
{
    protected string $table = 'orders';
    protected array $fillable = [
        'store_id', 'user_id', 'order_number', 'status', 'payment_status',
        'payment_method', 'subtotal', 'discount_amount', 'tax_amount',
        'shipping_amount', 'total_amount', 'shipping_name', 'shipping_phone',
        'shipping_address_line1', 'shipping_address_line2', 'shipping_city',
        'shipping_state', 'shipping_postal_code', 'shipping_country',
        'billing_name', 'billing_phone', 'billing_address_line1',
        'billing_address_line2', 'billing_city', 'billing_state',
        'billing_postal_code', 'billing_country', 'notes', 'admin_notes'
    ];

    /**
     * Create order from cart
     */
    public function createFromCart(array $cartData, array $orderData): int
    {
        $this->db->getConnection()->beginTransaction();

        try {
            // Generate order number
            $orderData['order_number'] = generateOrderNumber();
            $orderData['subtotal'] = $cartData['subtotal'];
            $orderData['tax_amount'] = $cartData['tax'];
            $orderData['shipping_amount'] = $cartData['shipping'];
            $orderData['total_amount'] = $cartData['total'];
            $orderData['status'] = 'pending';
            $orderData['payment_status'] = 'pending';
            $orderData['created_at'] = date('Y-m-d H:i:s');
            $orderData['updated_at'] = date('Y-m-d H:i:s');

            $orderId = $this->db->insert('orders', $orderData);

            // Add order items
            foreach ($cartData['items'] as $item) {
                $price = $item['sale_price'] ?? $item['price'];
                $price += $item['price_modifier'] ?? 0;

                $variantInfo = '';
                if ($item['size'] || $item['color']) {
                    $variantInfo = trim(($item['size'] ?? '') . ' / ' . ($item['color'] ?? ''), ' /');
                }

                $this->db->insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'] ?? '',
                    'variant_info' => $variantInfo,
                    'quantity' => $item['quantity'],
                    'unit_price' => $price,
                    'total_price' => $price * $item['quantity'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                // Update stock
                if ($item['variant_id']) {
                    $this->db->query(
                        "UPDATE product_variants SET stock_quantity = stock_quantity - ? WHERE id = ?",
                        [$item['quantity'], $item['variant_id']]
                    );
                } else {
                    $this->db->query(
                        "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?",
                        [$item['quantity'], $item['product_id']]
                    );
                }
            }

            $this->db->getConnection()->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * Get order by order number
     */
    public function findByOrderNumber(string $orderNumber): ?array
    {
        $order = $this->db->fetch(
            "SELECT * FROM orders WHERE order_number = ?",
            [$orderNumber]
        );

        if ($order) {
            $order['items'] = $this->getItems($order['id']);
            $order['shipment'] = $this->getShipment($order['id']);
            $order['payment'] = $this->getPayment($order['id']);
        }

        return $order;
    }

    /**
     * Get order with details
     */
    public function getWithDetails(int $orderId): ?array
    {
        $order = $this->find($orderId);

        if ($order) {
            $order['items'] = $this->getItems($orderId);
            $order['shipment'] = $this->getShipment($orderId);
            $order['payment'] = $this->getPayment($orderId);
            $order['user'] = $order['user_id']
                ? $this->db->fetch("SELECT * FROM users WHERE id = ?", [$order['user_id']])
                : null;
        }

        return $order;
    }

    /**
     * Get order items
     */
    public function getItems(int $orderId): array
    {
        return $this->db->fetchAll(
            "SELECT oi.*,
                    (SELECT image_path FROM product_images WHERE product_id = oi.product_id ORDER BY is_primary DESC LIMIT 1) as image
             FROM order_items oi
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }

    /**
     * Get order shipment
     */
    public function getShipment(int $orderId): ?array
    {
        $shipment = $this->db->fetch(
            "SELECT s.*, c.name as courier_name
             FROM shipments s
             LEFT JOIN couriers c ON s.courier_id = c.id
             WHERE s.order_id = ?",
            [$orderId]
        );

        if ($shipment) {
            $shipment['tracking'] = $this->db->fetchAll(
                "SELECT * FROM shipment_tracking WHERE shipment_id = ? ORDER BY tracked_at DESC",
                [$shipment['id']]
            );
        }

        return $shipment;
    }

    /**
     * Get order payment
     */
    public function getPayment(int $orderId): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1",
            [$orderId]
        );
    }

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status): bool
    {
        return $this->update($orderId, ['status' => $status]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $orderId, string $status): bool
    {
        return $this->update($orderId, ['payment_status' => $status]);
    }

    /**
     * Get user orders
     */
    public function getUserOrders(int $userId, int $page = 1, int $perPage = 10): array
    {
        return $this->paginate($page, $perPage, 'user_id = ?', [$userId], 'created_at DESC');
    }

    /**
     * Get orders for admin
     */
    public function getAdminOrders(int $page = 1, int $perPage = 20, int $storeId = 1, array $filters = []): array
    {
        $where = 'store_id = ?';
        $params = [$storeId];

        if (!empty($filters['status'])) {
            $where .= ' AND status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where .= ' AND (order_number LIKE ? OR shipping_name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        return $this->paginate($page, $perPage, $where, $params, 'created_at DESC');
    }

    /**
     * Get order stats
     */
    public function getStats(int $storeId = 1): array
    {
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        return [
            'total_orders' => $this->db->count('orders', 'store_id = ?', [$storeId]),
            'pending_orders' => $this->db->count('orders', "store_id = ? AND status = 'pending'", [$storeId]),
            'today_orders' => $this->db->count('orders', "store_id = ? AND DATE(created_at) = ?", [$storeId, $today]),
            'month_orders' => $this->db->count('orders', "store_id = ? AND created_at >= ?", [$storeId, $monthStart]),
            'total_revenue' => $this->db->fetch(
                "SELECT SUM(total_amount) as total FROM orders WHERE store_id = ? AND payment_status = 'paid'",
                [$storeId]
            )['total'] ?? 0,
            'today_revenue' => $this->db->fetch(
                "SELECT SUM(total_amount) as total FROM orders WHERE store_id = ? AND payment_status = 'paid' AND DATE(created_at) = ?",
                [$storeId, $today]
            )['total'] ?? 0,
            'month_revenue' => $this->db->fetch(
                "SELECT SUM(total_amount) as total FROM orders WHERE store_id = ? AND payment_status = 'paid' AND created_at >= ?",
                [$storeId, $monthStart]
            )['total'] ?? 0,
        ];
    }

    /**
     * Get recent orders
     */
    public function getRecent(int $limit = 5, int $storeId = 1): array
    {
        return $this->db->fetchAll(
            "SELECT o.*, u.name as customer_name
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             WHERE o.store_id = ?
             ORDER BY o.created_at DESC
             LIMIT ?",
            [$storeId, $limit]
        );
    }
}
