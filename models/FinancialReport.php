<?php
/**
 * Financial Report Model
 * Generates P&L, Cash Flow, and other financial reports
 */

class FinancialReport extends Model
{
    protected string $table = 'orders';

    /**
     * Get Profit & Loss report data
     */
    public function getProfitLoss(int $storeId, string $startDate, string $endDate): array
    {
        // Revenue from orders
        $revenue = $this->db->fetch(
            "SELECT
                COUNT(*) as order_count,
                COALESCE(SUM(total_amount), 0) as gross_revenue,
                COALESCE(SUM(discount_amount), 0) as total_discounts,
                COALESCE(SUM(shipping_amount), 0) as shipping_collected,
                COALESCE(SUM(total_amount - COALESCE(discount_amount, 0)), 0) as net_revenue
             FROM orders
             WHERE store_id = ?
               AND status NOT IN ('cancelled', 'refunded')
               AND DATE(created_at) BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        // Cost of Goods Sold (from order items)
        $cogs = $this->db->fetch(
            "SELECT COALESCE(SUM(oi.quantity * COALESCE(p.cost_price, 0)), 0) as cogs
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE o.store_id = ?
               AND o.status NOT IN ('cancelled', 'refunded')
               AND DATE(o.created_at) BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        // Operating Expenses
        $expenses = $this->db->fetch(
            "SELECT
                COALESCE(SUM(total_amount), 0) as total_expenses,
                COUNT(*) as expense_count
             FROM expenses
             WHERE store_id = ?
               AND expense_date BETWEEN ? AND ?
               AND payment_status != 'pending'",
            [$storeId, $startDate, $endDate]
        );

        // Expense breakdown by category
        $expensesByCategory = $this->db->fetchAll(
            "SELECT
                ec.name as category,
                ec.color,
                ec.icon,
                COALESCE(SUM(e.total_amount), 0) as amount
             FROM expense_categories ec
             LEFT JOIN expenses e ON ec.id = e.category_id
                AND e.store_id = ?
                AND e.expense_date BETWEEN ? AND ?
                AND e.payment_status != 'pending'
             WHERE ec.store_id = ?
             GROUP BY ec.id
             HAVING amount > 0
             ORDER BY amount DESC",
            [$storeId, $startDate, $endDate, $storeId]
        );

        // Calculate metrics
        $grossProfit = ($revenue['net_revenue'] ?? 0) - ($cogs['cogs'] ?? 0);
        $operatingProfit = $grossProfit - ($expenses['total_expenses'] ?? 0);
        $grossMargin = ($revenue['net_revenue'] ?? 0) > 0
            ? ($grossProfit / $revenue['net_revenue']) * 100
            : 0;
        $netMargin = ($revenue['net_revenue'] ?? 0) > 0
            ? ($operatingProfit / $revenue['net_revenue']) * 100
            : 0;

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'revenue' => [
                'order_count' => $revenue['order_count'] ?? 0,
                'gross' => $revenue['gross_revenue'] ?? 0,
                'discounts' => $revenue['total_discounts'] ?? 0,
                'shipping' => $revenue['shipping_collected'] ?? 0,
                'net' => $revenue['net_revenue'] ?? 0
            ],
            'cogs' => $cogs['cogs'] ?? 0,
            'gross_profit' => $grossProfit,
            'gross_margin' => $grossMargin,
            'expenses' => [
                'total' => $expenses['total_expenses'] ?? 0,
                'count' => $expenses['expense_count'] ?? 0,
                'by_category' => $expensesByCategory
            ],
            'operating_profit' => $operatingProfit,
            'net_margin' => $netMargin
        ];
    }

    /**
     * Get Cash Flow report data
     */
    public function getCashFlow(int $storeId, string $startDate, string $endDate): array
    {
        // Cash In - from paid orders
        $cashIn = $this->db->fetch(
            "SELECT
                COALESCE(SUM(total_amount), 0) as order_revenue
             FROM orders
             WHERE store_id = ?
               AND status IN ('completed', 'delivered', 'processing', 'shipped')
               AND payment_status = 'paid'
               AND DATE(created_at) BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        // Cash In - from supplier payments received (refunds)
        // This would typically be if you returned goods to suppliers

        // Cash Out - expenses paid
        $expensesPaid = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as amount
             FROM expenses
             WHERE store_id = ?
               AND expense_date BETWEEN ? AND ?
               AND payment_status = 'paid'",
            [$storeId, $startDate, $endDate]
        );

        // Cash Out - supplier payments
        $supplierPayments = $this->db->fetch(
            "SELECT COALESCE(SUM(amount), 0) as amount
             FROM supplier_payments
             WHERE store_id = ?
               AND payment_date BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        // Cash Out - refunds to customers
        $refunds = $this->db->fetch(
            "SELECT COALESCE(SUM(refund_amount), 0) as amount
             FROM returns
             WHERE store_id = ?
               AND refund_status = 'refunded'
               AND DATE(created_at) BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        $totalCashIn = ($cashIn['order_revenue'] ?? 0);
        $totalCashOut = ($expensesPaid['amount'] ?? 0) + ($supplierPayments['amount'] ?? 0) + ($refunds['amount'] ?? 0);
        $netCashFlow = $totalCashIn - $totalCashOut;

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'cash_in' => [
                'orders' => $cashIn['order_revenue'] ?? 0,
                'total' => $totalCashIn
            ],
            'cash_out' => [
                'expenses' => $expensesPaid['amount'] ?? 0,
                'supplier_payments' => $supplierPayments['amount'] ?? 0,
                'refunds' => $refunds['amount'] ?? 0,
                'total' => $totalCashOut
            ],
            'net_cash_flow' => $netCashFlow
        ];
    }

    /**
     * Get expense analysis report
     */
    public function getExpenseAnalysis(int $storeId, string $startDate, string $endDate): array
    {
        // Total by category
        $byCategory = $this->db->fetchAll(
            "SELECT
                ec.name as category,
                ec.color,
                ec.icon,
                COUNT(e.id) as count,
                COALESCE(SUM(e.total_amount), 0) as total
             FROM expense_categories ec
             LEFT JOIN expenses e ON ec.id = e.category_id
                AND e.store_id = ?
                AND e.expense_date BETWEEN ? AND ?
             WHERE ec.store_id = ?
             GROUP BY ec.id
             ORDER BY total DESC",
            [$storeId, $startDate, $endDate, $storeId]
        );

        // Monthly trend
        $monthlyTrend = $this->db->fetchAll(
            "SELECT
                DATE_FORMAT(expense_date, '%Y-%m') as month,
                DATE_FORMAT(expense_date, '%b %Y') as month_label,
                COALESCE(SUM(total_amount), 0) as total
             FROM expenses
             WHERE store_id = ?
               AND expense_date BETWEEN ? AND ?
             GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
             ORDER BY month",
            [$storeId, $startDate, $endDate]
        );

        // By payment method
        $byPaymentMethod = $this->db->fetchAll(
            "SELECT
                payment_method,
                COUNT(*) as count,
                COALESCE(SUM(total_amount), 0) as total
             FROM expenses
             WHERE store_id = ?
               AND expense_date BETWEEN ? AND ?
             GROUP BY payment_method
             ORDER BY total DESC",
            [$storeId, $startDate, $endDate]
        );

        // Top expenses
        $topExpenses = $this->db->fetchAll(
            "SELECT e.*, ec.name as category_name, ec.color, ec.icon
             FROM expenses e
             LEFT JOIN expense_categories ec ON e.category_id = ec.id
             WHERE e.store_id = ?
               AND e.expense_date BETWEEN ? AND ?
             ORDER BY e.total_amount DESC
             LIMIT 10",
            [$storeId, $startDate, $endDate]
        );

        // Summary
        $summary = $this->db->fetch(
            "SELECT
                COUNT(*) as count,
                COALESCE(SUM(total_amount), 0) as total,
                COALESCE(AVG(total_amount), 0) as average,
                COALESCE(MAX(total_amount), 0) as highest,
                COALESCE(MIN(total_amount), 0) as lowest
             FROM expenses
             WHERE store_id = ?
               AND expense_date BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'summary' => $summary,
            'by_category' => $byCategory,
            'monthly_trend' => $monthlyTrend,
            'by_payment_method' => $byPaymentMethod,
            'top_expenses' => $topExpenses
        ];
    }

    /**
     * Get revenue trends
     */
    public function getRevenueTrend(int $storeId, string $startDate, string $endDate): array
    {
        return $this->db->fetchAll(
            "SELECT
                DATE_FORMAT(created_at, '%Y-%m') as month,
                DATE_FORMAT(created_at, '%b %Y') as month_label,
                COUNT(*) as order_count,
                COALESCE(SUM(total_amount), 0) as revenue
             FROM orders
             WHERE store_id = ?
               AND status NOT IN ('cancelled', 'refunded')
               AND DATE(created_at) BETWEEN ? AND ?
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month",
            [$storeId, $startDate, $endDate]
        );
    }

    /**
     * Get purchase summary
     */
    public function getPurchaseSummary(int $storeId, string $startDate, string $endDate): array
    {
        return $this->db->fetch(
            "SELECT
                COUNT(*) as order_count,
                COALESCE(SUM(total_amount), 0) as total_purchases,
                COALESCE(SUM(paid_amount), 0) as total_paid,
                COALESCE(SUM(total_amount - paid_amount), 0) as total_due
             FROM purchase_orders
             WHERE store_id = ?
               AND status != 'cancelled'
               AND order_date BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        ) ?: ['order_count' => 0, 'total_purchases' => 0, 'total_paid' => 0, 'total_due' => 0];
    }

    /**
     * Get dashboard summary
     */
    public function getDashboardSummary(int $storeId): array
    {
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
        $lastMonthEnd = date('Y-m-t', strtotime('-1 month'));

        // This month revenue
        $thisMonthRevenue = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders
             WHERE store_id = ? AND status NOT IN ('cancelled', 'refunded')
             AND DATE(created_at) BETWEEN ? AND ?",
            [$storeId, $monthStart, $monthEnd]
        );

        // Last month revenue
        $lastMonthRevenue = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders
             WHERE store_id = ? AND status NOT IN ('cancelled', 'refunded')
             AND DATE(created_at) BETWEEN ? AND ?",
            [$storeId, $lastMonthStart, $lastMonthEnd]
        );

        // This month expenses
        $thisMonthExpenses = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total FROM expenses
             WHERE store_id = ? AND expense_date BETWEEN ? AND ?",
            [$storeId, $monthStart, $monthEnd]
        );

        // This month purchases
        $thisMonthPurchases = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total FROM purchase_orders
             WHERE store_id = ? AND status != 'cancelled' AND order_date BETWEEN ? AND ?",
            [$storeId, $monthStart, $monthEnd]
        );

        // Outstanding payables (supplier)
        $payables = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount - paid_amount), 0) as total FROM purchase_orders
             WHERE store_id = ? AND status != 'cancelled' AND payment_status != 'paid'",
            [$storeId]
        );

        // Calculate growth
        $revenueGrowth = ($lastMonthRevenue['total'] ?? 0) > 0
            ? (($thisMonthRevenue['total'] - $lastMonthRevenue['total']) / $lastMonthRevenue['total']) * 100
            : 0;

        return [
            'this_month' => [
                'revenue' => $thisMonthRevenue['total'] ?? 0,
                'expenses' => $thisMonthExpenses['total'] ?? 0,
                'purchases' => $thisMonthPurchases['total'] ?? 0,
                'profit' => ($thisMonthRevenue['total'] ?? 0) - ($thisMonthExpenses['total'] ?? 0)
            ],
            'last_month_revenue' => $lastMonthRevenue['total'] ?? 0,
            'revenue_growth' => $revenueGrowth,
            'outstanding_payables' => $payables['total'] ?? 0
        ];
    }
}
