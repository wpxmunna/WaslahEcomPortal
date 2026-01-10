<?php
/**
 * Purchase Order Model
 */

class PurchaseOrder extends Model
{
    protected string $table = 'purchase_orders';
    protected array $fillable = [
        'store_id', 'supplier_id', 'po_number', 'status', 'order_date',
        'expected_date', 'received_date', 'subtotal', 'tax_amount',
        'shipping_amount', 'discount_amount', 'total_amount',
        'payment_status', 'paid_amount', 'notes', 'created_by'
    ];

    /**
     * Generate unique PO number
     */
    public function generatePONumber(): string
    {
        $prefix = 'PO';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return $prefix . $date . $random;
    }

    /**
     * Get purchase orders for admin listing with filters
     */
    public function getAdminPurchaseOrders(int $storeId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = 'po.store_id = ?';
        $params = [$storeId];

        if (!empty($filters['status'])) {
            $where .= ' AND po.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['supplier_id'])) {
            $where .= ' AND po.supplier_id = ?';
            $params[] = $filters['supplier_id'];
        }

        if (!empty($filters['payment_status'])) {
            $where .= ' AND po.payment_status = ?';
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['start_date'])) {
            $where .= ' AND po.order_date >= ?';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where .= ' AND po.order_date <= ?';
            $params[] = $filters['end_date'];
        }

        if (!empty($filters['search'])) {
            $where .= ' AND (po.po_number LIKE ? OR s.name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        // Get total count
        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as total FROM purchase_orders po
             LEFT JOIN suppliers s ON po.supplier_id = s.id
             WHERE {$where}",
            $params
        );
        $total = $countResult['total'];

        // Calculate pagination
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        // Get paginated results
        $orders = $this->db->fetchAll(
            "SELECT po.*, s.name as supplier_name, s.code as supplier_code,
                    (SELECT COUNT(*) FROM purchase_order_items WHERE purchase_order_id = po.id) as item_count
             FROM purchase_orders po
             LEFT JOIN suppliers s ON po.supplier_id = s.id
             WHERE {$where}
             ORDER BY po.order_date DESC, po.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $orders,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Create purchase order with items
     */
    public function createPurchaseOrder(array $data, array $items = []): int
    {
        $data['po_number'] = $this->generatePONumber();
        $data['created_at'] = date('Y-m-d H:i:s');

        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['quantity'] * $item['unit_cost'];
        }

        $data['subtotal'] = $subtotal;
        $data['total_amount'] = $subtotal + ($data['tax_amount'] ?? 0) + ($data['shipping_amount'] ?? 0) - ($data['discount_amount'] ?? 0);

        $poId = $this->db->insert($this->table, $data);

        // Insert items
        foreach ($items as $item) {
            $this->db->insert('purchase_order_items', [
                'purchase_order_id' => $poId,
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'product_sku' => $item['product_sku'] ?? null,
                'variant_id' => $item['variant_id'] ?? null,
                'variant_info' => $item['variant_info'] ?? null,
                'quantity_ordered' => $item['quantity'],
                'quantity_received' => 0,
                'unit_cost' => $item['unit_cost'],
                'total_cost' => $item['quantity'] * $item['unit_cost'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $poId;
    }

    /**
     * Get purchase order with supplier and items
     */
    public function getWithDetails(int $id): ?array
    {
        $po = $this->db->fetch(
            "SELECT po.*, s.name as supplier_name, s.code as supplier_code,
                    s.contact_person, s.phone as supplier_phone, s.email as supplier_email,
                    s.address as supplier_address, s.city as supplier_city, s.country as supplier_country,
                    u.name as created_by_name
             FROM purchase_orders po
             LEFT JOIN suppliers s ON po.supplier_id = s.id
             LEFT JOIN users u ON po.created_by = u.id
             WHERE po.id = ?",
            [$id]
        );

        if ($po) {
            $po['items'] = $this->getItems($id);
            $po['payments'] = $this->getPayments($id);
        }

        return $po;
    }

    /**
     * Get purchase order items
     */
    public function getItems(int $poId): array
    {
        return $this->db->fetchAll(
            "SELECT poi.*, p.slug as product_slug,
                    (SELECT image_path FROM product_images WHERE product_id = poi.product_id AND is_primary = 1 LIMIT 1) as product_image
             FROM purchase_order_items poi
             LEFT JOIN products p ON poi.product_id = p.id
             WHERE poi.purchase_order_id = ?
             ORDER BY poi.id",
            [$poId]
        );
    }

    /**
     * Get payments for a purchase order
     */
    public function getPayments(int $poId): array
    {
        return $this->db->fetchAll(
            "SELECT sp.*, u.name as created_by_name
             FROM supplier_payments sp
             LEFT JOIN users u ON sp.created_by = u.id
             WHERE sp.purchase_order_id = ?
             ORDER BY sp.payment_date DESC",
            [$poId]
        );
    }

    /**
     * Update purchase order
     */
    public function updatePurchaseOrder(int $id, array $data, array $items = []): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Recalculate totals if items provided
        if (!empty($items)) {
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['quantity'] * $item['unit_cost'];
            }
            $data['subtotal'] = $subtotal;
            $data['total_amount'] = $subtotal + ($data['tax_amount'] ?? 0) + ($data['shipping_amount'] ?? 0) - ($data['discount_amount'] ?? 0);

            // Delete existing items and re-insert
            $this->db->delete('purchase_order_items', 'purchase_order_id = ?', [$id]);

            foreach ($items as $item) {
                $this->db->insert('purchase_order_items', [
                    'purchase_order_id' => $id,
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'] ?? null,
                    'variant_id' => $item['variant_id'] ?? null,
                    'variant_info' => $item['variant_info'] ?? null,
                    'quantity_ordered' => $item['quantity'],
                    'quantity_received' => 0,
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['quantity'] * $item['unit_cost'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $this->db->update($this->table, $data, 'id = ?', [$id]);
        return true;
    }

    /**
     * Update status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'received') {
            $data['received_date'] = date('Y-m-d');
        }

        $this->db->update($this->table, $data, 'id = ?', [$id]);
        return true;
    }

    /**
     * Receive stock for PO items
     */
    public function receiveStock(int $id, array $receivedQuantities): bool
    {
        $po = $this->find($id);
        if (!$po) return false;

        $items = $this->getItems($id);
        $allReceived = true;
        $anyReceived = false;

        foreach ($items as $item) {
            $qtyReceived = $receivedQuantities[$item['id']] ?? 0;
            if ($qtyReceived > 0) {
                $newReceived = $item['quantity_received'] + $qtyReceived;

                // Update item received quantity
                $this->db->update('purchase_order_items', [
                    'quantity_received' => $newReceived
                ], 'id = ?', [$item['id']]);

                // Update product stock if linked
                if ($item['product_id']) {
                    $this->updateProductStock($item['product_id'], $item['variant_id'], $qtyReceived, $item['unit_cost']);
                }

                $anyReceived = true;

                if ($newReceived < $item['quantity_ordered']) {
                    $allReceived = false;
                }
            } else if ($item['quantity_received'] < $item['quantity_ordered']) {
                $allReceived = false;
            }
        }

        // Update PO status
        if ($allReceived) {
            $this->updateStatus($id, 'received');
        } else if ($anyReceived) {
            $this->updateStatus($id, 'partial');
        }

        return true;
    }

    /**
     * Update product stock and cost price
     */
    private function updateProductStock(int $productId, ?int $variantId, int $quantity, float $unitCost): void
    {
        if ($variantId) {
            // Update variant stock
            $this->db->query(
                "UPDATE product_variants SET stock = stock + ? WHERE id = ?",
                [$quantity, $variantId]
            );
        }

        // Update product stock
        $this->db->query(
            "UPDATE products SET stock = stock + ? WHERE id = ?",
            [$quantity, $productId]
        );

        // Update cost price (weighted average or just latest)
        $this->db->query(
            "UPDATE products SET cost_price = ? WHERE id = ?",
            [$unitCost, $productId]
        );
    }

    /**
     * Add payment to supplier
     */
    public function addPayment(int $poId, array $paymentData): int
    {
        $po = $this->find($poId);
        if (!$po) return 0;

        // Generate payment number
        $paymentData['payment_number'] = 'PAY' . date('Ymd') . strtoupper(substr(uniqid(), -4));
        $paymentData['store_id'] = $po['store_id'];
        $paymentData['supplier_id'] = $po['supplier_id'];
        $paymentData['purchase_order_id'] = $poId;
        $paymentData['created_at'] = date('Y-m-d H:i:s');

        $paymentId = $this->db->insert('supplier_payments', $paymentData);

        // Update PO paid amount and status
        $newPaidAmount = $po['paid_amount'] + $paymentData['amount'];
        $paymentStatus = 'pending';
        if ($newPaidAmount >= $po['total_amount']) {
            $paymentStatus = 'paid';
        } else if ($newPaidAmount > 0) {
            $paymentStatus = 'partial';
        }

        $this->db->update($this->table, [
            'paid_amount' => $newPaidAmount,
            'payment_status' => $paymentStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$poId]);

        // Update supplier totals
        $supplier = new Supplier();
        $supplier->updateTotals($po['supplier_id']);

        return $paymentId;
    }

    /**
     * Cancel purchase order
     */
    public function cancelOrder(int $id): bool
    {
        $po = $this->find($id);
        if (!$po || $po['status'] === 'received') {
            return false;
        }

        $this->db->update($this->table, [
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$id]);

        // Update supplier totals
        $supplier = new Supplier();
        $supplier->updateTotals($po['supplier_id']);

        return true;
    }

    /**
     * Get stats for dashboard
     */
    public function getStats(int $storeId): array
    {
        $stats = $this->db->fetch(
            "SELECT
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'ordered' THEN 1 ELSE 0 END) as ordered,
                SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END) as total_value,
                SUM(CASE WHEN payment_status = 'pending' THEN total_amount ELSE 0 END) as unpaid_value
             FROM purchase_orders
             WHERE store_id = ?",
            [$storeId]
        );

        return $stats ?: [
            'total_orders' => 0,
            'pending_orders' => 0,
            'ordered' => 0,
            'total_value' => 0,
            'unpaid_value' => 0
        ];
    }

    /**
     * Get recent orders
     */
    public function getRecent(int $storeId, int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT po.*, s.name as supplier_name
             FROM purchase_orders po
             LEFT JOIN suppliers s ON po.supplier_id = s.id
             WHERE po.store_id = ?
             ORDER BY po.created_at DESC
             LIMIT ?",
            [$storeId, $limit]
        );
    }

    /**
     * Delete purchase order (only draft/pending)
     */
    public function deletePurchaseOrder(int $id): bool
    {
        $po = $this->find($id);
        if (!$po || !in_array($po['status'], ['draft', 'pending'])) {
            return false;
        }

        // Delete items first
        $this->db->delete('purchase_order_items', 'purchase_order_id = ?', [$id]);

        // Delete PO
        $this->db->delete($this->table, 'id = ?', [$id]);

        return true;
    }
}
