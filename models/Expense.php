<?php
/**
 * Expense Model
 */

class Expense extends Model
{
    protected string $table = 'expenses';
    protected array $fillable = [
        'store_id', 'category_id', 'expense_number', 'title', 'description',
        'amount', 'tax_amount', 'total_amount', 'expense_date',
        'payment_method', 'payment_status', 'reference_number',
        'vendor_name', 'receipt_path', 'notes', 'created_by'
    ];

    /**
     * Generate unique expense number
     */
    public function generateExpenseNumber(): string
    {
        $prefix = 'EXP';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return $prefix . $date . $random;
    }

    /**
     * Get expenses for admin listing with filters
     */
    public function getAdminExpenses(int $storeId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = 'e.store_id = ?';
        $params = [$storeId];

        if (!empty($filters['category_id'])) {
            $where .= ' AND e.category_id = ?';
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['start_date'])) {
            $where .= ' AND e.expense_date >= ?';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where .= ' AND e.expense_date <= ?';
            $params[] = $filters['end_date'];
        }

        if (!empty($filters['payment_status'])) {
            $where .= ' AND e.payment_status = ?';
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['search'])) {
            $where .= ' AND (e.title LIKE ? OR e.expense_number LIKE ? OR e.vendor_name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // Get total count
        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as total FROM expenses e WHERE {$where}",
            $params
        );
        $total = $countResult['total'];

        // Calculate pagination
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        // Get paginated results
        $expenses = $this->db->fetchAll(
            "SELECT e.*, c.name as category_name, c.color as category_color, c.icon as category_icon
             FROM expenses e
             LEFT JOIN expense_categories c ON e.category_id = c.id
             WHERE {$where}
             ORDER BY e.expense_date DESC, e.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $expenses,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Create expense
     */
    public function createExpense(array $data): int
    {
        $data['expense_number'] = $this->generateExpenseNumber();
        $data['total_amount'] = $data['amount'] + ($data['tax_amount'] ?? 0);
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update expense
     */
    public function updateExpense(int $id, array $data): bool
    {
        if (isset($data['amount']) || isset($data['tax_amount'])) {
            $expense = $this->find($id);
            $amount = $data['amount'] ?? $expense['amount'];
            $taxAmount = $data['tax_amount'] ?? $expense['tax_amount'];
            $data['total_amount'] = $amount + $taxAmount;
        }
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->update($this->table, $data, 'id = ?', [$id]);
        return true;
    }

    /**
     * Get expense with category
     */
    public function getWithCategory(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT e.*, c.name as category_name, c.color as category_color
             FROM expenses e
             LEFT JOIN expense_categories c ON e.category_id = c.id
             WHERE e.id = ?",
            [$id]
        );
    }

    /**
     * Get expense stats for dashboard
     */
    public function getStats(int $storeId, string $startDate, string $endDate): array
    {
        // Total expenses in period
        $totals = $this->db->fetch(
            "SELECT COUNT(*) as count,
                    COALESCE(SUM(total_amount), 0) as total,
                    COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) as paid,
                    COALESCE(SUM(CASE WHEN payment_status = 'pending' THEN total_amount ELSE 0 END), 0) as pending
             FROM expenses
             WHERE store_id = ? AND expense_date BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        // By category
        $byCategory = $this->db->fetchAll(
            "SELECT c.name, c.color, c.icon,
                    COUNT(e.id) as count,
                    COALESCE(SUM(e.total_amount), 0) as total
             FROM expense_categories c
             LEFT JOIN expenses e ON c.id = e.category_id
                AND e.expense_date BETWEEN ? AND ?
             WHERE c.store_id = ? AND c.is_active = 1
             GROUP BY c.id
             ORDER BY total DESC",
            [$startDate, $endDate, $storeId]
        );

        // By payment method
        $byPaymentMethod = $this->db->fetchAll(
            "SELECT payment_method, COUNT(*) as count, SUM(total_amount) as total
             FROM expenses
             WHERE store_id = ? AND expense_date BETWEEN ? AND ?
             GROUP BY payment_method",
            [$storeId, $startDate, $endDate]
        );

        // Monthly trend (last 6 months)
        $monthlyTrend = $this->db->fetchAll(
            "SELECT DATE_FORMAT(expense_date, '%Y-%m') as month,
                    COUNT(*) as count,
                    SUM(total_amount) as total
             FROM expenses
             WHERE store_id = ? AND expense_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY month
             ORDER BY month ASC",
            [$storeId]
        );

        return [
            'totals' => $totals,
            'by_category' => $byCategory,
            'by_payment_method' => $byPaymentMethod,
            'monthly_trend' => $monthlyTrend
        ];
    }

    /**
     * Get recent expenses
     */
    public function getRecent(int $storeId, int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT e.*, c.name as category_name, c.color as category_color
             FROM expenses e
             LEFT JOIN expense_categories c ON e.category_id = c.id
             WHERE e.store_id = ?
             ORDER BY e.expense_date DESC, e.created_at DESC
             LIMIT ?",
            [$storeId, $limit]
        );
    }

    /**
     * Get total expenses for period
     */
    public function getTotalForPeriod(int $storeId, string $startDate, string $endDate): float
    {
        $result = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total
             FROM expenses
             WHERE store_id = ? AND expense_date BETWEEN ? AND ?
               AND payment_status != 'pending'",
            [$storeId, $startDate, $endDate]
        );
        return (float) $result['total'];
    }

    /**
     * Delete expense
     */
    public function deleteExpense(int $id): bool
    {
        $expense = $this->find($id);
        if ($expense && $expense['receipt_path']) {
            $receiptFile = UPLOAD_PATH . '/' . $expense['receipt_path'];
            if (file_exists($receiptFile)) {
                unlink($receiptFile);
            }
        }

        $this->db->delete($this->table, 'id = ?', [$id]);
        return true;
    }
}
