<?php

class AdminCustomerController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();

        // Check admin authentication
        if (!Auth::isAdmin()) {
            $this->redirect('admin/login');
        }
    }

    public function index()
    {
        $storeId = Session::get('admin_store_id', 1);
        $page = (int)($_GET['page'] ?? 1);
        $search = trim($_GET['search'] ?? '');
        $perPage = 20;

        $conditions = ['store_id' => $storeId, 'role' => 'customer'];

        if ($search) {
            $customers = $this->searchCustomers($storeId, $search, $page, $perPage);
        } else {
            $customers = $this->getCustomersPaginated($storeId, $page, $perPage);
        }

        parent::view('admin/customers/index', [
            'customers' => $customers['data'],
            'pagination' => $customers['pagination'],
            'search' => $search,
            'pageTitle' => 'Customers'
        ], 'admin');
    }

    public function show($id)
    {
        $storeId = Session::get('admin_store_id', 1);
        $customer = $this->userModel->find($id);

        if (!$customer || $customer['store_id'] != $storeId || $customer['role'] !== 'customer') {
            Session::flash('error', 'Customer not found');
            $this->redirect('admin/customers');
        }

        // Get customer orders
        $orders = $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 20",
            [$id]
        );

        // Get customer addresses
        $addresses = $this->db->fetchAll(
            "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC",
            [$id]
        );

        // Get customer stats
        $stats = $this->db->fetch(
            "SELECT
                COUNT(*) as total_orders,
                COALESCE(SUM(total_amount), 0) as total_spent,
                MAX(created_at) as last_order
             FROM orders WHERE user_id = ?",
            [$id]
        );

        parent::view('admin/customers/view', [
            'customer' => $customer,
            'orders' => $orders,
            'addresses' => $addresses,
            'stats' => $stats,
            'pageTitle' => 'Customer Details'
        ], 'admin');
    }

    public function toggleStatus($id)
    {
        $storeId = Session::get('admin_store_id', 1);
        $customer = $this->userModel->find($id);

        if (!$customer || $customer['store_id'] != $storeId || $customer['role'] !== 'customer') {
            Session::flash('error', 'Customer not found');
            $this->redirect('admin/customers');
        }

        $newStatus = $customer['status'] ? 0 : 1;
        $this->userModel->update($id, ['status' => $newStatus]);

        $statusText = $newStatus ? 'activated' : 'deactivated';
        Session::flash('success', "Customer {$statusText} successfully");
        $this->redirect('admin/customers');
    }

    private function getCustomersPaginated($storeId, $page, $perPage)
    {
        $offset = ($page - 1) * $perPage;

        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM users WHERE store_id = ? AND role = 'customer'",
            [$storeId]
        );
        $total = $result['count'] ?? 0;

        $customers = $this->db->fetchAll(
            "SELECT u.*,
                    (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
                    (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = u.id) as total_spent
             FROM users u
             WHERE u.store_id = ? AND u.role = 'customer'
             ORDER BY u.created_at DESC
             LIMIT ? OFFSET ?",
            [$storeId, $perPage, $offset]
        );

        return [
            'data' => $customers,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ];
    }

    private function searchCustomers($storeId, $search, $page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        $searchTerm = "%{$search}%";

        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM users
             WHERE store_id = ? AND role = 'customer'
             AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)",
            [$storeId, $searchTerm, $searchTerm, $searchTerm]
        );
        $total = $result['count'] ?? 0;

        $customers = $this->db->fetchAll(
            "SELECT u.*,
                    (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
                    (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = u.id) as total_spent
             FROM users u
             WHERE u.store_id = ? AND u.role = 'customer'
             AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)
             ORDER BY u.created_at DESC
             LIMIT ? OFFSET ?",
            [$storeId, $searchTerm, $searchTerm, $searchTerm, $perPage, $offset]
        );

        return [
            'data' => $customers,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ];
    }
}
