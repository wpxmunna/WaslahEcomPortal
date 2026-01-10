<?php
/**
 * Admin Supplier Controller
 */

class AdminSupplierController extends Controller
{
    private Supplier $supplierModel;

    public function __construct()
    {
        parent::__construct();

        // Require admin access
        if (!Session::isAdmin()) {
            $this->redirect('admin/login');
        }

        $this->supplierModel = new Supplier();
    }

    /**
     * List suppliers
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $page = (int) $this->get('page', 1);

        $filters = [
            'status' => $this->get('status'),
            'search' => $this->get('search')
        ];

        $result = $this->supplierModel->getAdminSuppliers($storeId, $filters, $page);

        $this->view('admin/suppliers/index', [
            'pageTitle' => 'Suppliers - Admin',
            'suppliers' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->view('admin/suppliers/create', [
            'pageTitle' => 'Add Supplier - Admin'
        ], 'admin');
    }

    /**
     * Store new supplier
     */
    public function store(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/suppliers', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'name' => trim($this->post('name')),
            'code' => trim($this->post('code')) ?: null,
            'contact_person' => trim($this->post('contact_person')),
            'email' => trim($this->post('email')),
            'phone' => trim($this->post('phone')),
            'address' => trim($this->post('address')),
            'city' => trim($this->post('city')),
            'country' => $this->post('country', 'Bangladesh'),
            'payment_terms' => (int) $this->post('payment_terms', 30),
            'notes' => trim($this->post('notes')),
            'status' => 'active'
        ];

        if (empty($data['name'])) {
            $this->redirect('admin/suppliers/create', 'Supplier name is required', 'error');
        }

        $supplierId = $this->supplierModel->createSupplier($data);

        if ($supplierId) {
            $this->redirect('admin/suppliers', 'Supplier added successfully', 'success');
        } else {
            $this->redirect('admin/suppliers/create', 'Failed to add supplier', 'error');
        }
    }

    /**
     * View supplier details
     */
    public function show(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $supplier = $this->supplierModel->getWithStats($id);

        if (!$supplier || $supplier['store_id'] != $storeId) {
            $this->redirect('admin/suppliers', 'Supplier not found', 'error');
        }

        $purchaseOrders = $this->supplierModel->getPurchaseOrders($id, 20);
        $payments = $this->supplierModel->getPayments($id, 20);

        $this->view('admin/suppliers/show', [
            'pageTitle' => $supplier['name'] . ' - Supplier',
            'supplier' => $supplier,
            'purchaseOrders' => $purchaseOrders,
            'payments' => $payments
        ], 'admin');
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $supplier = $this->supplierModel->find($id);

        if (!$supplier || $supplier['store_id'] != $storeId) {
            $this->redirect('admin/suppliers', 'Supplier not found', 'error');
        }

        $this->view('admin/suppliers/edit', [
            'pageTitle' => 'Edit Supplier - Admin',
            'supplier' => $supplier
        ], 'admin');
    }

    /**
     * Update supplier
     */
    public function update(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/suppliers', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $supplier = $this->supplierModel->find($id);

        if (!$supplier || $supplier['store_id'] != $storeId) {
            $this->redirect('admin/suppliers', 'Supplier not found', 'error');
        }

        $data = [
            'name' => trim($this->post('name')),
            'code' => trim($this->post('code')) ?: $supplier['code'],
            'contact_person' => trim($this->post('contact_person')),
            'email' => trim($this->post('email')),
            'phone' => trim($this->post('phone')),
            'address' => trim($this->post('address')),
            'city' => trim($this->post('city')),
            'country' => $this->post('country', 'Bangladesh'),
            'payment_terms' => (int) $this->post('payment_terms', 30),
            'notes' => trim($this->post('notes')),
            'status' => $this->post('status', 'active')
        ];

        $this->supplierModel->updateSupplier($id, $data);
        $this->redirect('admin/suppliers/view/' . $id, 'Supplier updated successfully', 'success');
    }

    /**
     * Delete supplier
     */
    public function delete(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $supplier = $this->supplierModel->find($id);

        if (!$supplier || $supplier['store_id'] != $storeId) {
            $this->redirect('admin/suppliers', 'Supplier not found', 'error');
        }

        $deleted = $this->supplierModel->deleteSupplier($id);

        if ($deleted) {
            $this->redirect('admin/suppliers', 'Supplier deleted successfully', 'success');
        } else {
            $this->redirect('admin/suppliers', 'Cannot delete supplier with existing purchase orders', 'error');
        }
    }
}
