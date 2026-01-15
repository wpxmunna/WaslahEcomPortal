<?php
/**
 * Admin Dashboard Controller
 */

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Dashboard
     */
    public function index(): void
    {
        $this->requireAdmin();

        $storeId = Session::get('current_store_id', 1);

        $orderModel = new Order();
        $productModel = new Product();
        $userModel = new User();
        $storeModel = new Store();

        // Get stats
        $stats = $orderModel->getStats($storeId);
        $recentOrders = $orderModel->getRecent(5, $storeId);
        $lowStockProducts = $productModel->getLowStock($storeId);

        // Count stats
        $productCount = $this->db->count('products', 'store_id = ?', [$storeId]);
        $customerCount = $this->db->count('users', "role = 'customer'");

        $data = [
            'pageTitle' => 'Dashboard - Admin',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'productCount' => $productCount,
            'customerCount' => $customerCount,
            'stores' => $storeModel->getActive(),
            'pendingOrders' => $stats['pending_orders']
        ];

        $this->view('admin/dashboard', $data, 'admin');
    }

    /**
     * Admin login
     */
    public function login(): void
    {
        if (Session::isAdmin()) {
            $this->redirect('admin');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Auth();

            if ($auth->attemptAdmin($this->post('email'), $this->post('password'))) {
                $this->redirect('admin', 'Welcome back!');
            } else {
                $this->data['error'] = 'Invalid credentials or insufficient permissions';
            }
        }

        $data = [
            'pageTitle' => 'Admin Login - Waslah',
            'error' => $this->data['error'] ?? null
        ];

        $this->view('admin/login', $data, null);
    }

    /**
     * Admin logout
     */
    public function logout(): void
    {
        (new Auth())->logoutAdmin();
        $this->redirect('admin/login', 'Logged out successfully');
    }
}
