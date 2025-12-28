<?php

class AdminReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Check admin authentication
        if (!Auth::check() || !Auth::isAdmin()) {
            $this->redirect('admin/login');
        }
    }

    public function index()
    {
        $storeId = Session::get('admin_store_id', 1);

        // Get date range from request or default to last 30 days
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));

        // Summary stats
        $stats = $this->getSummaryStats($storeId, $startDate, $endDate);

        // Daily sales data for chart
        $dailySales = $this->getDailySales($storeId, $startDate, $endDate);

        // Top products
        $topProducts = $this->getTopProducts($storeId, $startDate, $endDate, 10);

        // Recent orders
        $recentOrders = $this->getRecentOrders($storeId, 10);

        // Order status breakdown
        $ordersByStatus = $this->getOrdersByStatus($storeId, $startDate, $endDate);

        $this->view('admin/reports/index', [
            'pageTitle' => 'Reports & Analytics',
            'stats' => $stats,
            'dailySales' => $dailySales,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'ordersByStatus' => $ordersByStatus,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'admin');
    }

    public function sales()
    {
        $storeId = Session::get('admin_store_id', 1);

        // Get date range
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $groupBy = $_GET['group_by'] ?? 'day'; // day, week, month

        // Sales data
        $salesData = $this->getSalesData($storeId, $startDate, $endDate, $groupBy);

        // Summary
        $summary = $this->getSalesSummary($storeId, $startDate, $endDate);

        // Payment method breakdown
        $paymentMethods = $this->getPaymentMethodBreakdown($storeId, $startDate, $endDate);

        // Hourly sales distribution
        $hourlySales = $this->getHourlySales($storeId, $startDate, $endDate);

        $this->view('admin/reports/sales', [
            'pageTitle' => 'Sales Report',
            'salesData' => $salesData,
            'summary' => $summary,
            'paymentMethods' => $paymentMethods,
            'hourlySales' => $hourlySales,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'groupBy' => $groupBy
        ], 'admin');
    }

    public function products()
    {
        $storeId = Session::get('admin_store_id', 1);

        // Get date range
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));

        // Top selling products
        $topSelling = $this->getTopProducts($storeId, $startDate, $endDate, 20);

        // Low stock products
        $lowStock = $this->getLowStockProducts($storeId);

        // Category performance
        $categoryPerformance = $this->getCategoryPerformance($storeId, $startDate, $endDate);

        // Product views (if tracked)
        $mostViewed = $this->getMostViewedProducts($storeId, 10);

        $this->view('admin/reports/products', [
            'pageTitle' => 'Product Report',
            'topSelling' => $topSelling,
            'lowStock' => $lowStock,
            'categoryPerformance' => $categoryPerformance,
            'mostViewed' => $mostViewed,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'admin');
    }

    public function customers()
    {
        $storeId = Session::get('admin_store_id', 1);

        // Get date range
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));

        // Customer stats
        $customerStats = $this->getCustomerStats($storeId, $startDate, $endDate);

        // Top customers
        $topCustomers = $this->getTopCustomers($storeId, $startDate, $endDate, 20);

        // New customers over time
        $newCustomers = $this->getNewCustomersOverTime($storeId, $startDate, $endDate);

        // Customer locations
        $customerLocations = $this->getCustomerLocations($storeId);

        $this->view('admin/reports/customers', [
            'pageTitle' => 'Customer Report',
            'customerStats' => $customerStats,
            'topCustomers' => $topCustomers,
            'newCustomers' => $newCustomers,
            'customerLocations' => $customerLocations,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'admin');
    }

    public function export()
    {
        $storeId = Session::get('admin_store_id', 1);
        $type = $_GET['type'] ?? 'sales';
        $format = $_GET['format'] ?? 'csv';
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));

        $data = [];
        $filename = '';

        switch ($type) {
            case 'sales':
                $data = $this->getExportSalesData($storeId, $startDate, $endDate);
                $filename = "sales_report_{$startDate}_to_{$endDate}";
                break;
            case 'products':
                $data = $this->getExportProductsData($storeId, $startDate, $endDate);
                $filename = "products_report_{$startDate}_to_{$endDate}";
                break;
            case 'customers':
                $data = $this->getExportCustomersData($storeId, $startDate, $endDate);
                $filename = "customers_report_{$startDate}_to_{$endDate}";
                break;
            case 'orders':
                $data = $this->getExportOrdersData($storeId, $startDate, $endDate);
                $filename = "orders_report_{$startDate}_to_{$endDate}";
                break;
        }

        if ($format === 'csv') {
            $this->exportCsv($data, $filename);
        } else {
            $this->json($data);
        }
    }

    // ==================== HELPER METHODS ====================

    private function getSummaryStats($storeId, $startDate, $endDate)
    {
        // Total revenue
        $revenue = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total
             FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?
             AND status NOT IN ('cancelled', 'refunded')",
            [$storeId, $startDate, $endDate]
        );

        // Total orders
        $orders = $this->db->fetch(
            "SELECT COUNT(*) as total
             FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        // Average order value
        $avgOrder = $this->db->fetch(
            "SELECT COALESCE(AVG(total_amount), 0) as avg_value
             FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?
             AND status NOT IN ('cancelled', 'refunded')",
            [$storeId, $startDate, $endDate]
        );

        // New customers
        $newCustomers = $this->db->fetch(
            "SELECT COUNT(*) as total
             FROM users
             WHERE store_id = ? AND role = 'customer'
             AND DATE(created_at) BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        // Products sold
        $productsSold = $this->db->fetch(
            "SELECT COALESCE(SUM(oi.quantity), 0) as total
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             WHERE o.store_id = ? AND DATE(o.created_at) BETWEEN ? AND ?
             AND o.status NOT IN ('cancelled', 'refunded')",
            [$storeId, $startDate, $endDate]
        );

        // Compare with previous period
        $daysDiff = (strtotime($endDate) - strtotime($startDate)) / 86400;
        $prevEndDate = date('Y-m-d', strtotime($startDate . ' -1 day'));
        $prevStartDate = date('Y-m-d', strtotime($prevEndDate . " -{$daysDiff} days"));

        $prevRevenue = $this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total
             FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?
             AND status NOT IN ('cancelled', 'refunded')",
            [$storeId, $prevStartDate, $prevEndDate]
        );

        $revenueChange = $prevRevenue['total'] > 0
            ? (($revenue['total'] - $prevRevenue['total']) / $prevRevenue['total']) * 100
            : 0;

        return [
            'revenue' => $revenue['total'] ?? 0,
            'orders' => $orders['total'] ?? 0,
            'avg_order' => $avgOrder['avg_value'] ?? 0,
            'new_customers' => $newCustomers['total'] ?? 0,
            'products_sold' => $productsSold['total'] ?? 0,
            'revenue_change' => round($revenueChange, 1)
        ];
    }

    private function getDailySales($storeId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT DATE(created_at) as date,
                    COUNT(*) as orders,
                    COALESCE(SUM(total_amount), 0) as revenue
             FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?
             AND status NOT IN ('cancelled', 'refunded')
             GROUP BY DATE(created_at)
             ORDER BY date",
            [$storeId, $startDate, $endDate]
        );
    }

    private function getSalesData($storeId, $startDate, $endDate, $groupBy)
    {
        $dateFormat = match($groupBy) {
            'week' => "YEARWEEK(created_at, 1)",
            'month' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => "DATE(created_at)"
        };

        $labelFormat = match($groupBy) {
            'week' => "CONCAT('Week ', WEEK(created_at, 1), ', ', YEAR(created_at))",
            'month' => "DATE_FORMAT(created_at, '%b %Y')",
            default => "DATE_FORMAT(created_at, '%b %d')"
        };

        return $this->db->fetchAll(
            "SELECT {$labelFormat} as label,
                    {$dateFormat} as period,
                    COUNT(*) as orders,
                    COALESCE(SUM(total_amount), 0) as revenue,
                    COALESCE(SUM(subtotal), 0) as subtotal,
                    COALESCE(SUM(discount_amount), 0) as discounts,
                    COALESCE(SUM(shipping_amount), 0) as shipping,
                    COALESCE(SUM(tax_amount), 0) as tax
             FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?
             AND status NOT IN ('cancelled', 'refunded')
             GROUP BY period, label
             ORDER BY period",
            [$storeId, $startDate, $endDate]
        );
    }

    private function getSalesSummary($storeId, $startDate, $endDate)
    {
        return $this->db->fetch(
            "SELECT COUNT(*) as total_orders,
                    COALESCE(SUM(total_amount), 0) as total_revenue,
                    COALESCE(SUM(subtotal), 0) as subtotal,
                    COALESCE(SUM(discount_amount), 0) as total_discounts,
                    COALESCE(SUM(shipping_amount), 0) as total_shipping,
                    COALESCE(SUM(tax_amount), 0) as total_tax,
                    COALESCE(AVG(total_amount), 0) as avg_order_value,
                    COALESCE(MAX(total_amount), 0) as max_order,
                    COALESCE(MIN(total_amount), 0) as min_order
             FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?
             AND status NOT IN ('cancelled', 'refunded')",
            [$storeId, $startDate, $endDate]
        );
    }

    private function getPaymentMethodBreakdown($storeId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT COALESCE(payment_method, 'Unknown') as method,
                    COUNT(*) as orders,
                    COALESCE(SUM(total_amount), 0) as revenue
             FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?
             AND status NOT IN ('cancelled', 'refunded')
             GROUP BY payment_method
             ORDER BY revenue DESC",
            [$storeId, $startDate, $endDate]
        );
    }

    private function getHourlySales($storeId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT HOUR(created_at) as hour,
                    COUNT(*) as orders,
                    COALESCE(SUM(total_amount), 0) as revenue
             FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?
             AND status NOT IN ('cancelled', 'refunded')
             GROUP BY HOUR(created_at)
             ORDER BY hour",
            [$storeId, $startDate, $endDate]
        );
    }

    private function getTopProducts($storeId, $startDate, $endDate, $limit = 10)
    {
        return $this->db->fetchAll(
            "SELECT p.id, p.name, p.sku,
                    (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) as image,
                    SUM(oi.quantity) as quantity_sold,
                    SUM(oi.total_price) as revenue
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             JOIN orders o ON oi.order_id = o.id
             WHERE o.store_id = ? AND DATE(o.created_at) BETWEEN ? AND ?
             AND o.status NOT IN ('cancelled', 'refunded')
             GROUP BY p.id, p.name, p.sku
             ORDER BY quantity_sold DESC
             LIMIT ?",
            [$storeId, $startDate, $endDate, $limit]
        );
    }

    private function getRecentOrders($storeId, $limit = 10)
    {
        return $this->db->fetchAll(
            "SELECT o.*, u.name as customer_name, u.email as customer_email
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             WHERE o.store_id = ?
             ORDER BY o.created_at DESC
             LIMIT ?",
            [$storeId, $limit]
        );
    }

    private function getOrdersByStatus($storeId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT status, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as revenue
             FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?
             GROUP BY status
             ORDER BY count DESC",
            [$storeId, $startDate, $endDate]
        );
    }

    private function getLowStockProducts($storeId, $threshold = 10)
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name,
                    (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) as image
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.store_id = ? AND p.stock_quantity <= ? AND p.status = 'active'
             ORDER BY p.stock_quantity ASC
             LIMIT 20",
            [$storeId, $threshold]
        );
    }

    private function getCategoryPerformance($storeId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT c.id, c.name,
                    COUNT(DISTINCT oi.id) as items_sold,
                    SUM(oi.quantity) as quantity_sold,
                    SUM(oi.total_price) as revenue
             FROM categories c
             JOIN products p ON p.category_id = c.id
             JOIN order_items oi ON oi.product_id = p.id
             JOIN orders o ON oi.order_id = o.id
             WHERE o.store_id = ? AND DATE(o.created_at) BETWEEN ? AND ?
             AND o.status NOT IN ('cancelled', 'refunded')
             GROUP BY c.id, c.name
             ORDER BY revenue DESC",
            [$storeId, $startDate, $endDate]
        );
    }

    private function getMostViewedProducts($storeId, $limit = 10)
    {
        // This would require a product_views table to track views
        // For now, return top products by order count
        return $this->db->fetchAll(
            "SELECT p.id, p.name, p.sku, p.price,
                    (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) as image,
                    COUNT(DISTINCT oi.order_id) as order_count
             FROM products p
             LEFT JOIN order_items oi ON oi.product_id = p.id
             WHERE p.store_id = ?
             GROUP BY p.id, p.name, p.sku, p.price
             ORDER BY order_count DESC
             LIMIT ?",
            [$storeId, $limit]
        );
    }

    private function getCustomerStats($storeId, $startDate, $endDate)
    {
        $total = $this->db->fetch(
            "SELECT COUNT(*) as total FROM users WHERE store_id = ? AND role = 'customer'",
            [$storeId]
        );

        $newCustomers = $this->db->fetch(
            "SELECT COUNT(*) as total FROM users
             WHERE store_id = ? AND role = 'customer'
             AND DATE(created_at) BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        $activeCustomers = $this->db->fetch(
            "SELECT COUNT(DISTINCT user_id) as total FROM orders
             WHERE store_id = ? AND DATE(created_at) BETWEEN ? AND ?",
            [$storeId, $startDate, $endDate]
        );

        $avgOrdersPerCustomer = $this->db->fetch(
            "SELECT AVG(order_count) as avg_orders FROM (
                SELECT user_id, COUNT(*) as order_count
                FROM orders
                WHERE store_id = ? AND user_id IS NOT NULL
                GROUP BY user_id
             ) as customer_orders",
            [$storeId]
        );

        return [
            'total_customers' => $total['total'] ?? 0,
            'new_customers' => $newCustomers['total'] ?? 0,
            'active_customers' => $activeCustomers['total'] ?? 0,
            'avg_orders_per_customer' => round($avgOrdersPerCustomer['avg_orders'] ?? 0, 1)
        ];
    }

    private function getTopCustomers($storeId, $startDate, $endDate, $limit = 20)
    {
        return $this->db->fetchAll(
            "SELECT u.id, u.name, u.email, u.created_at,
                    COUNT(o.id) as order_count,
                    COALESCE(SUM(o.total_amount), 0) as total_spent,
                    MAX(o.created_at) as last_order
             FROM users u
             JOIN orders o ON o.user_id = u.id
             WHERE u.store_id = ? AND u.role = 'customer'
             AND DATE(o.created_at) BETWEEN ? AND ?
             AND o.status NOT IN ('cancelled', 'refunded')
             GROUP BY u.id, u.name, u.email, u.created_at
             ORDER BY total_spent DESC
             LIMIT ?",
            [$storeId, $startDate, $endDate, $limit]
        );
    }

    private function getNewCustomersOverTime($storeId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT DATE(created_at) as date, COUNT(*) as new_customers
             FROM users
             WHERE store_id = ? AND role = 'customer'
             AND DATE(created_at) BETWEEN ? AND ?
             GROUP BY DATE(created_at)
             ORDER BY date",
            [$storeId, $startDate, $endDate]
        );
    }

    private function getCustomerLocations($storeId)
    {
        return $this->db->fetchAll(
            "SELECT COALESCE(ua.city, 'Unknown') as city,
                    COALESCE(ua.country, 'Unknown') as country,
                    COUNT(DISTINCT u.id) as customers
             FROM users u
             LEFT JOIN user_addresses ua ON ua.user_id = u.id AND ua.is_default = 1
             WHERE u.store_id = ? AND u.role = 'customer'
             GROUP BY ua.city, ua.country
             ORDER BY customers DESC
             LIMIT 20",
            [$storeId]
        );
    }

    private function getExportSalesData($storeId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT o.order_number, o.created_at, o.status, o.payment_status,
                    o.subtotal, o.discount_amount, o.shipping_amount, o.tax_amount, o.total_amount,
                    o.payment_method, u.name as customer_name, u.email as customer_email
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             WHERE o.store_id = ? AND DATE(o.created_at) BETWEEN ? AND ?
             ORDER BY o.created_at DESC",
            [$storeId, $startDate, $endDate]
        );
    }

    private function getExportProductsData($storeId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT p.sku, p.name, c.name as category,
                    p.price, p.stock_quantity,
                    COALESCE(SUM(oi.quantity), 0) as quantity_sold,
                    COALESCE(SUM(oi.total_price), 0) as revenue
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN order_items oi ON oi.product_id = p.id
             LEFT JOIN orders o ON oi.order_id = o.id AND DATE(o.created_at) BETWEEN ? AND ?
             WHERE p.store_id = ?
             GROUP BY p.id, p.sku, p.name, c.name, p.price, p.stock_quantity
             ORDER BY revenue DESC",
            [$startDate, $endDate, $storeId]
        );
    }

    private function getExportCustomersData($storeId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT u.name, u.email, u.phone, u.created_at,
                    COUNT(o.id) as total_orders,
                    COALESCE(SUM(o.total_amount), 0) as total_spent,
                    MAX(o.created_at) as last_order
             FROM users u
             LEFT JOIN orders o ON o.user_id = u.id
             WHERE u.store_id = ? AND u.role = 'customer'
             GROUP BY u.id, u.name, u.email, u.phone, u.created_at
             ORDER BY total_spent DESC",
            [$storeId]
        );
    }

    private function getExportOrdersData($storeId, $startDate, $endDate)
    {
        return $this->db->fetchAll(
            "SELECT o.order_number, o.created_at, o.status, o.payment_status, o.payment_method,
                    o.subtotal, o.discount_amount, o.shipping_amount, o.tax_amount, o.total_amount,
                    u.name as customer_name, u.email as customer_email,
                    o.shipping_name, o.shipping_city, o.shipping_country
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             WHERE o.store_id = ? AND DATE(o.created_at) BETWEEN ? AND ?
             ORDER BY o.created_at DESC",
            [$storeId, $startDate, $endDate]
        );
    }

    private function exportCsv($data, $filename)
    {
        if (empty($data)) {
            Session::setFlash('No data to export', 'error');
            $this->redirect('admin/reports');
            return;
        }

        // Disable error output completely for clean CSV
        error_reporting(0);
        ini_set('display_errors', 0);

        // Clear all output buffers to prevent HTML from being included
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        // Add BOM for Excel UTF-8 compatibility
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');

        // Headers - with escape parameter for PHP 8.4 compatibility
        fputcsv($output, array_keys($data[0]), ',', '"', '\\');

        // Data - with escape parameter for PHP 8.4 compatibility
        foreach ($data as $row) {
            fputcsv($output, $row, ',', '"', '\\');
        }

        fclose($output);
        exit;
    }
}
