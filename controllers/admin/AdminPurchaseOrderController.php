<?php
/**
 * Admin Purchase Order Controller
 */

class AdminPurchaseOrderController extends Controller
{
    private PurchaseOrder $poModel;
    private Supplier $supplierModel;

    public function __construct()
    {
        parent::__construct();

        // Require admin access
        if (!Session::isAdmin()) {
            $this->redirect('admin/login');
        }

        $this->poModel = new PurchaseOrder();
        $this->supplierModel = new Supplier();
    }

    /**
     * List purchase orders
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $page = (int) $this->get('page', 1);

        $filters = [
            'status' => $this->get('status'),
            'supplier_id' => $this->get('supplier'),
            'payment_status' => $this->get('payment'),
            'start_date' => $this->get('start_date'),
            'end_date' => $this->get('end_date'),
            'search' => $this->get('search')
        ];

        $result = $this->poModel->getAdminPurchaseOrders($storeId, $filters, $page);
        $suppliers = $this->supplierModel->getActive($storeId);
        $stats = $this->poModel->getStats($storeId);

        $this->view('admin/purchase-orders/index', [
            'pageTitle' => 'Purchase Orders - Admin',
            'purchaseOrders' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'suppliers' => $suppliers,
            'stats' => $stats
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $suppliers = $this->supplierModel->getActive($storeId);

        // Get products for selection
        $product = new Product();
        $products = $product->getActiveProducts($storeId);

        // Pre-select supplier if provided
        $selectedSupplier = $this->get('supplier');

        $this->view('admin/purchase-orders/create', [
            'pageTitle' => 'Create Purchase Order - Admin',
            'suppliers' => $suppliers,
            'products' => $products,
            'selectedSupplier' => $selectedSupplier
        ], 'admin');
    }

    /**
     * Store new purchase order
     */
    public function store(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/purchase-orders', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'supplier_id' => (int) $this->post('supplier_id'),
            'order_date' => $this->post('order_date'),
            'expected_date' => $this->post('expected_date') ?: null,
            'status' => $this->post('status', 'draft'),
            'tax_amount' => (float) $this->post('tax_amount', 0),
            'shipping_amount' => (float) $this->post('shipping_amount', 0),
            'discount_amount' => (float) $this->post('discount_amount', 0),
            'notes' => trim($this->post('notes')),
            'created_by' => Session::get('admin_user_id')
        ];

        if (empty($data['supplier_id'])) {
            $this->redirect('admin/purchase-orders/create', 'Please select a supplier', 'error');
        }

        // Parse items from form
        $items = [];
        $productIds = $this->post('product_id', []);
        $productNames = $this->post('product_name', []);
        $productSkus = $this->post('product_sku', []);
        $quantities = $this->post('quantity', []);
        $unitCosts = $this->post('unit_cost', []);

        foreach ($productNames as $index => $name) {
            if (!empty($name) && !empty($quantities[$index]) && !empty($unitCosts[$index])) {
                $items[] = [
                    'product_id' => !empty($productIds[$index]) ? (int) $productIds[$index] : null,
                    'product_name' => $name,
                    'product_sku' => $productSkus[$index] ?? null,
                    'quantity' => (int) $quantities[$index],
                    'unit_cost' => (float) $unitCosts[$index]
                ];
            }
        }

        if (empty($items)) {
            $this->redirect('admin/purchase-orders/create', 'Please add at least one item', 'error');
        }

        $poId = $this->poModel->createPurchaseOrder($data, $items);

        if ($poId) {
            $this->redirect('admin/purchase-orders/view/' . $poId, 'Purchase order created successfully', 'success');
        } else {
            $this->redirect('admin/purchase-orders/create', 'Failed to create purchase order', 'error');
        }
    }

    /**
     * View purchase order details
     */
    public function show(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $po = $this->poModel->getWithDetails($id);

        if (!$po || $po['store_id'] != $storeId) {
            $this->redirect('admin/purchase-orders', 'Purchase order not found', 'error');
        }

        $this->view('admin/purchase-orders/show', [
            'pageTitle' => $po['po_number'] . ' - Purchase Order',
            'po' => $po
        ], 'admin');
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $po = $this->poModel->getWithDetails($id);

        if (!$po || $po['store_id'] != $storeId) {
            $this->redirect('admin/purchase-orders', 'Purchase order not found', 'error');
        }

        // Only allow editing draft/pending orders
        if (!in_array($po['status'], ['draft', 'pending'])) {
            $this->redirect('admin/purchase-orders/view/' . $id, 'Cannot edit this order', 'error');
        }

        $suppliers = $this->supplierModel->getActive($storeId);
        $product = new Product();
        $products = $product->getActiveProducts($storeId);

        $this->view('admin/purchase-orders/edit', [
            'pageTitle' => 'Edit Purchase Order - Admin',
            'po' => $po,
            'suppliers' => $suppliers,
            'products' => $products
        ], 'admin');
    }

    /**
     * Update purchase order
     */
    public function update(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/purchase-orders', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $po = $this->poModel->find($id);

        if (!$po || $po['store_id'] != $storeId) {
            $this->redirect('admin/purchase-orders', 'Purchase order not found', 'error');
        }

        if (!in_array($po['status'], ['draft', 'pending'])) {
            $this->redirect('admin/purchase-orders/view/' . $id, 'Cannot edit this order', 'error');
        }

        $data = [
            'supplier_id' => (int) $this->post('supplier_id'),
            'order_date' => $this->post('order_date'),
            'expected_date' => $this->post('expected_date') ?: null,
            'status' => $this->post('status', 'draft'),
            'tax_amount' => (float) $this->post('tax_amount', 0),
            'shipping_amount' => (float) $this->post('shipping_amount', 0),
            'discount_amount' => (float) $this->post('discount_amount', 0),
            'notes' => trim($this->post('notes'))
        ];

        // Parse items
        $items = [];
        $productIds = $this->post('product_id', []);
        $productNames = $this->post('product_name', []);
        $productSkus = $this->post('product_sku', []);
        $quantities = $this->post('quantity', []);
        $unitCosts = $this->post('unit_cost', []);

        foreach ($productNames as $index => $name) {
            if (!empty($name) && !empty($quantities[$index]) && !empty($unitCosts[$index])) {
                $items[] = [
                    'product_id' => !empty($productIds[$index]) ? (int) $productIds[$index] : null,
                    'product_name' => $name,
                    'product_sku' => $productSkus[$index] ?? null,
                    'quantity' => (int) $quantities[$index],
                    'unit_cost' => (float) $unitCosts[$index]
                ];
            }
        }

        $this->poModel->updatePurchaseOrder($id, $data, $items);
        $this->redirect('admin/purchase-orders/view/' . $id, 'Purchase order updated successfully', 'success');
    }

    /**
     * Show receive stock form
     */
    public function receive(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $po = $this->poModel->getWithDetails($id);

        if (!$po || $po['store_id'] != $storeId) {
            $this->redirect('admin/purchase-orders', 'Purchase order not found', 'error');
        }

        if (!in_array($po['status'], ['approved', 'ordered', 'partial'])) {
            $this->redirect('admin/purchase-orders/view/' . $id, 'This order cannot receive stock', 'error');
        }

        $this->view('admin/purchase-orders/receive', [
            'pageTitle' => 'Receive Stock - ' . $po['po_number'],
            'po' => $po
        ], 'admin');
    }

    /**
     * Process stock receipt
     */
    public function processReceipt(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/purchase-orders/receive/' . $id, 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $po = $this->poModel->find($id);

        if (!$po || $po['store_id'] != $storeId) {
            $this->redirect('admin/purchase-orders', 'Purchase order not found', 'error');
        }

        $receivedQuantities = $this->post('received', []);

        $this->poModel->receiveStock($id, $receivedQuantities);
        $this->redirect('admin/purchase-orders/view/' . $id, 'Stock received successfully', 'success');
    }

    /**
     * Cancel purchase order
     */
    public function cancel(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $po = $this->poModel->find($id);

        if (!$po || $po['store_id'] != $storeId) {
            $this->redirect('admin/purchase-orders', 'Purchase order not found', 'error');
        }

        if ($this->poModel->cancelOrder($id)) {
            $this->redirect('admin/purchase-orders', 'Purchase order cancelled', 'success');
        } else {
            $this->redirect('admin/purchase-orders/view/' . $id, 'Cannot cancel this order', 'error');
        }
    }

    /**
     * Add payment to purchase order
     */
    public function addPayment(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/purchase-orders/view/' . $id, 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $po = $this->poModel->find($id);

        if (!$po || $po['store_id'] != $storeId) {
            $this->redirect('admin/purchase-orders', 'Purchase order not found', 'error');
        }

        $paymentData = [
            'amount' => (float) $this->post('amount'),
            'payment_date' => $this->post('payment_date'),
            'payment_method' => $this->post('payment_method', 'bank_transfer'),
            'reference_number' => trim($this->post('reference_number')),
            'notes' => trim($this->post('notes')),
            'created_by' => Session::get('admin_user_id')
        ];

        if ($paymentData['amount'] <= 0) {
            $this->redirect('admin/purchase-orders/view/' . $id, 'Invalid payment amount', 'error');
        }

        $this->poModel->addPayment($id, $paymentData);
        $this->redirect('admin/purchase-orders/view/' . $id, 'Payment recorded successfully', 'success');
    }
}
