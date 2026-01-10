<?php
/**
 * Supplier Model
 */

class Supplier extends Model
{
    protected string $table = 'suppliers';
    protected array $fillable = [
        'store_id', 'name', 'code', 'contact_person', 'email', 'phone',
        'address', 'city', 'country', 'payment_terms', 'notes', 'status',
        'total_purchases', 'total_paid'
    ];

    /**
     * Generate unique supplier code
     */
    public function generateCode(): string
    {
        $prefix = 'SUP';
        $random = strtoupper(substr(uniqid(), -4));
        return $prefix . $random;
    }

    /**
     * Get suppliers for admin listing with filters
     */
    public function getAdminSuppliers(int $storeId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = 's.store_id = ?';
        $params = [$storeId];

        if (!empty($filters['status'])) {
            $where .= ' AND s.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where .= ' AND (s.name LIKE ? OR s.code LIKE ? OR s.contact_person LIKE ? OR s.email LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // Get total count
        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as total FROM suppliers s WHERE {$where}",
            $params
        );
        $total = $countResult['total'];

        // Calculate pagination
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        // Get paginated results with stats
        $suppliers = $this->db->fetchAll(
            "SELECT s.*,
                    COUNT(DISTINCT po.id) as order_count,
                    COALESCE(SUM(CASE WHEN po.status != 'cancelled' THEN po.total_amount ELSE 0 END), 0) as total_purchases,
                    COALESCE((SELECT SUM(amount) FROM supplier_payments WHERE supplier_id = s.id), 0) as total_paid
             FROM suppliers s
             LEFT JOIN purchase_orders po ON s.id = po.supplier_id
             WHERE {$where}
             GROUP BY s.id
             ORDER BY s.name ASC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $suppliers,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Get all active suppliers for dropdown
     */
    public function getActive(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, code FROM suppliers WHERE store_id = ? AND status = 'active' ORDER BY name",
            [$storeId]
        );
    }

    /**
     * Create supplier
     */
    public function createSupplier(array $data): int
    {
        if (empty($data['code'])) {
            $data['code'] = $this->generateCode();
        }
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update supplier
     */
    public function updateSupplier(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update($this->table, $data, 'id = ?', [$id]);
        return true;
    }

    /**
     * Get supplier with stats
     */
    public function getWithStats(int $id): ?array
    {
        $supplier = $this->db->fetch(
            "SELECT s.*,
                    COUNT(DISTINCT po.id) as order_count,
                    COALESCE(SUM(CASE WHEN po.status != 'cancelled' THEN po.total_amount ELSE 0 END), 0) as total_purchases,
                    COALESCE((SELECT SUM(amount) FROM supplier_payments WHERE supplier_id = s.id), 0) as total_paid
             FROM suppliers s
             LEFT JOIN purchase_orders po ON s.id = po.supplier_id
             WHERE s.id = ?
             GROUP BY s.id",
            [$id]
        );

        if ($supplier) {
            $supplier['balance_due'] = $supplier['total_purchases'] - $supplier['total_paid'];
        }

        return $supplier;
    }

    /**
     * Get supplier purchase orders
     */
    public function getPurchaseOrders(int $supplierId, int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM purchase_orders
             WHERE supplier_id = ?
             ORDER BY order_date DESC
             LIMIT ?",
            [$supplierId, $limit]
        );
    }

    /**
     * Get supplier payments
     */
    public function getPayments(int $supplierId, int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT sp.*, po.po_number
             FROM supplier_payments sp
             LEFT JOIN purchase_orders po ON sp.purchase_order_id = po.id
             WHERE sp.supplier_id = ?
             ORDER BY sp.payment_date DESC
             LIMIT ?",
            [$supplierId, $limit]
        );
    }

    /**
     * Update supplier totals
     */
    public function updateTotals(int $supplierId): void
    {
        $stats = $this->db->fetch(
            "SELECT
                COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END), 0) as total_purchases
             FROM purchase_orders
             WHERE supplier_id = ?",
            [$supplierId]
        );

        $payments = $this->db->fetch(
            "SELECT COALESCE(SUM(amount), 0) as total_paid FROM supplier_payments WHERE supplier_id = ?",
            [$supplierId]
        );

        $this->db->update($this->table, [
            'total_purchases' => $stats['total_purchases'],
            'total_paid' => $payments['total_paid'],
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$supplierId]);
    }

    /**
     * Delete supplier (only if no POs)
     */
    public function deleteSupplier(int $id): bool
    {
        $count = $this->db->fetch(
            "SELECT COUNT(*) as count FROM purchase_orders WHERE supplier_id = ?",
            [$id]
        );

        if ($count['count'] > 0) {
            return false;
        }

        $this->db->delete($this->table, 'id = ?', [$id]);
        return true;
    }
}
