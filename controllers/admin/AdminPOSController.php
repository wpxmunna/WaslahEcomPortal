<?php
/**
 * Admin POS Controller
 */

class AdminPOSController extends Controller
{
    private POS $posModel;
    private Product $productModel;

    public function __construct()
    {
        parent::__construct();

        if (!Session::isAdmin()) {
            $this->redirect('admin/login');
        }

        $this->posModel = new POS();
        $this->productModel = new Product();
    }

    /**
     * POS Terminal - Main sales screen
     */
    public function terminal(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $userId = (int) Session::getUserId() ?? 0;

        // Check for open shift
        $currentShift = $userId > 0 ? $this->posModel->getCurrentShift($userId, $storeId) : null;
        $terminals = $this->posModel->getTerminals($storeId);

        // Get products for quick add with categories
        $products = $this->productModel->getActiveProducts($storeId);

        // Get product categories
        $categories = $this->db->fetchAll(
            "SELECT DISTINCT c.id, c.name FROM categories c
             JOIN products p ON p.category_id = c.id
             WHERE p.store_id = ? AND p.status = 'active'
             ORDER BY c.name",
            [$storeId]
        );

        // Get held orders for current shift
        $heldOrders = $currentShift ? $this->posModel->getHeldOrders($currentShift['id']) : [];

        // Get daily summary
        $dailySummary = $currentShift ? $this->posModel->getDailySummary($storeId, $currentShift['id']) : null;

        $this->view('admin/pos/terminal', [
            'pageTitle' => 'POS Terminal',
            'activeShift' => $currentShift,
            'terminals' => $terminals,
            'products' => $products,
            'categories' => $categories,
            'heldOrders' => $heldOrders,
            'dailySummary' => $dailySummary
        ], 'admin');
    }

    /**
     * Open a new shift
     */
    public function openShift(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/pos/terminal', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $userId = (int) Session::getUserId() ?? 0;

        if ($userId <= 0) {
            $this->redirect('admin/pos/terminal', 'User session not found', 'error');
        }

        // Check if already has open shift
        $currentShift = $this->posModel->getCurrentShift($userId, $storeId);
        if ($currentShift) {
            $this->redirect('admin/pos/terminal', 'You already have an open shift', 'warning');
        }

        $data = [
            'store_id' => $storeId,
            'terminal_id' => (int) $this->post('terminal_id'),
            'user_id' => $userId,
            'opening_cash' => (float) $this->post('opening_cash', 0)
        ];

        $shiftId = $this->posModel->openShift($data);

        if ($shiftId) {
            $this->redirect('admin/pos/terminal', 'Shift opened successfully', 'success');
        } else {
            $this->redirect('admin/pos/terminal', 'Failed to open shift', 'error');
        }
    }

    /**
     * Close current shift
     */
    public function closeShift(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/pos/terminal', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $userId = (int) Session::getUserId() ?? 0;

        if ($userId <= 0) {
            $this->redirect('admin/pos/terminal', 'User session not found', 'error');
        }

        $currentShift = $this->posModel->getCurrentShift($userId, $storeId);
        if (!$currentShift) {
            $this->redirect('admin/pos/terminal', 'No open shift found', 'error');
        }

        $data = [
            'actual_cash' => (float) $this->post('actual_cash'),
            'notes' => trim($this->post('notes'))
        ];

        $this->posModel->closeShift($currentShift['id'], $data);
        $this->redirect('admin/pos/shifts', 'Shift closed successfully', 'success');
    }

    /**
     * Process POS sale
     */
    public function sale(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request']);
        }

        $storeId = Session::get('admin_store_id', 1);
        $userId = (int) Session::getUserId() ?? 0;

        if ($userId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'User session not found']);
        }

        $currentShift = $this->posModel->getCurrentShift($userId, $storeId);
        if (!$currentShift) {
            $this->jsonResponse(['success' => false, 'message' => 'Please open a shift first']);
        }

        // Parse items from JSON
        $itemsJson = $this->post('items');
        $items = json_decode($itemsJson, true);

        if (empty($items)) {
            $this->jsonResponse(['success' => false, 'message' => 'No items in cart']);
        }

        $data = [
            'store_id' => $storeId,
            'shift_id' => $currentShift['id'],
            'terminal_id' => $currentShift['terminal_id'],
            'customer_name' => trim($this->post('customer_name')),
            'customer_phone' => trim($this->post('customer_phone')),
            'discount_amount' => (float) $this->post('discount_amount', 0),
            'tax_amount' => (float) $this->post('tax_amount', 0),
            'payment_method' => $this->post('payment_method', 'cash'),
            'cash_received' => (float) $this->post('cash_received', 0),
            'card_amount' => (float) $this->post('card_amount', 0),
            'mobile_amount' => (float) $this->post('mobile_amount', 0),
            'notes' => trim($this->post('notes')),
            'created_by' => $userId
        ];

        $transactionId = $this->posModel->createTransaction($data, $items);

        if ($transactionId) {
            $transaction = $this->posModel->getTransactionWithItems($transactionId);
            $this->jsonResponse([
                'success' => true,
                'message' => 'Sale completed',
                'transaction_id' => $transactionId,
                'transaction_number' => $transaction['transaction_number'],
                'change_amount' => $transaction['change_amount']
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to process sale']);
        }
    }

    /**
     * Get transaction receipt
     */
    public function receipt(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $transaction = $this->posModel->getTransactionWithItems($id);

        if (!$transaction || $transaction['store_id'] != $storeId) {
            $this->redirect('admin/pos/transactions', 'Transaction not found', 'error');
        }

        $this->view('admin/pos/receipt', [
            'pageTitle' => 'Receipt - ' . $transaction['transaction_number'],
            'transaction' => $transaction
        ], 'admin');
    }

    /**
     * Transactions list
     */
    public function transactions(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $page = (int) $this->get('page', 1);

        $filters = [
            'date' => $this->get('date'),
            'shift_id' => $this->get('shift'),
            'status' => $this->get('status')
        ];

        $result = $this->posModel->getTransactions($storeId, $filters, $page);
        $todayStats = $this->posModel->getTodayStats($storeId);

        $this->view('admin/pos/transactions', [
            'pageTitle' => 'POS Transactions',
            'transactions' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'todayStats' => $todayStats
        ], 'admin');
    }

    /**
     * Shifts list
     */
    public function shifts(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $page = (int) $this->get('page', 1);

        $filters = [
            'date' => $this->get('date'),
            'status' => $this->get('status')
        ];

        $result = $this->posModel->getShifts($storeId, $filters, $page);

        $this->view('admin/pos/shifts', [
            'pageTitle' => 'POS Shifts',
            'shifts' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters
        ], 'admin');
    }

    /**
     * View shift details
     */
    public function shiftDetails(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $shift = $this->db->fetch(
            "SELECT s.*, u.name as cashier_name, pt.terminal_name
             FROM pos_shifts s
             LEFT JOIN users u ON s.user_id = u.id
             LEFT JOIN pos_terminals pt ON s.terminal_id = pt.id
             WHERE s.id = ? AND s.store_id = ?",
            [$id, $storeId]
        );

        if (!$shift) {
            $this->redirect('admin/pos/shifts', 'Shift not found', 'error');
        }

        $summary = $this->posModel->getShiftSummary($id);
        $transactions = $this->posModel->getTransactions($storeId, ['shift_id' => $id], 1, 100);

        $this->view('admin/pos/shift-details', [
            'pageTitle' => 'Shift Details - ' . $shift['shift_number'],
            'shift' => $shift,
            'summary' => $summary,
            'transactions' => $transactions['data']
        ], 'admin');
    }

    /**
     * Cash management (in/out)
     */
    public function cashManagement(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request']);
        }

        $storeId = Session::get('admin_store_id', 1);
        $userId = (int) Session::getUserId() ?? 0;

        if ($userId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'User session not found']);
        }

        $currentShift = $this->posModel->getCurrentShift($userId, $storeId);
        if (!$currentShift) {
            $this->jsonResponse(['success' => false, 'message' => 'No open shift']);
        }

        $data = [
            'store_id' => $storeId,
            'shift_id' => $currentShift['id'],
            'log_type' => $this->post('log_type'),
            'amount' => (float) $this->post('amount'),
            'reason' => trim($this->post('reason')),
            'created_by' => $userId
        ];

        $this->posModel->addCashLog($data);
        $this->jsonResponse(['success' => true, 'message' => 'Cash ' . $data['log_type'] . ' recorded']);
    }

    /**
     * Hold current order
     */
    public function holdOrder(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!Session::validateCsrf($input['csrf_token'] ?? '')) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request']);
        }

        $storeId = Session::get('admin_store_id', 1);
        $userId = (int) Session::getUserId() ?? 0;

        $currentShift = $this->posModel->getCurrentShift($userId, $storeId);
        if (!$currentShift) {
            $this->jsonResponse(['success' => false, 'message' => 'No open shift']);
        }

        $items = $input['items'] ?? [];
        if (empty($items)) {
            $this->jsonResponse(['success' => false, 'message' => 'No items to hold']);
        }

        $holdId = $this->posModel->holdOrder(
            $currentShift['id'],
            $storeId,
            $items,
            $input['customer_phone'] ?? null,
            $input['note'] ?? null
        );

        $this->jsonResponse([
            'success' => true,
            'message' => 'Order held successfully',
            'hold_id' => $holdId
        ]);
    }

    /**
     * Get held orders
     */
    public function getHeldOrders(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $userId = (int) Session::getUserId() ?? 0;

        $currentShift = $this->posModel->getCurrentShift($userId, $storeId);
        if (!$currentShift) {
            $this->jsonResponse(['success' => false, 'message' => 'No open shift']);
        }

        $orders = $this->posModel->getHeldOrders($currentShift['id']);
        $this->jsonResponse(['success' => true, 'orders' => $orders]);
    }

    /**
     * Recall held order
     */
    public function recallOrder(int $id): void
    {
        $order = $this->posModel->recallOrder($id);

        if (!$order) {
            $this->jsonResponse(['success' => false, 'message' => 'Order not found']);
        }

        // Delete the held order after recall
        $this->posModel->deleteHeldOrder($id);

        $this->jsonResponse([
            'success' => true,
            'items' => $order['items'],
            'customer_phone' => $order['customer_phone']
        ]);
    }

    /**
     * Delete held order
     */
    public function deleteHeldOrder(int $id): void
    {
        $this->posModel->deleteHeldOrder($id);
        $this->jsonResponse(['success' => true, 'message' => 'Order deleted']);
    }

    /**
     * Search customers
     */
    public function searchCustomers(): void
    {
        $query = $this->get('q', '');
        if (strlen($query) < 2) {
            $this->jsonResponse(['success' => true, 'customers' => []]);
        }

        $customers = $this->posModel->searchCustomers($query);
        $this->jsonResponse(['success' => true, 'customers' => $customers]);
    }

    /**
     * Lookup product by barcode/SKU
     */
    public function barcodeLookup(): void
    {
        $barcode = trim($this->get('code', ''));
        if (empty($barcode)) {
            $this->jsonResponse(['success' => false, 'message' => 'No barcode provided']);
        }

        $storeId = Session::get('admin_store_id', 1);
        $product = $this->posModel->getProductByBarcode($barcode, $storeId);

        if ($product) {
            $this->jsonResponse(['success' => true, 'product' => $product]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Product not found']);
        }
    }

    /**
     * Process refund
     */
    public function refund(): void
    {
        $this->view('admin/pos/refund', [
            'pageTitle' => 'POS Refund'
        ], 'admin');
    }

    /**
     * Search transaction for refund
     */
    public function searchTransaction(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        // Search by transaction number
        $number = trim($this->get('number', ''));
        if (!empty($number)) {
            $transaction = $this->db->fetch(
                "SELECT t.*, u.name as cashier_name
                 FROM pos_transactions t
                 LEFT JOIN users u ON t.created_by = u.id
                 WHERE t.transaction_number = ? AND t.store_id = ?",
                [$number, $storeId]
            );

            if (!$transaction) {
                $this->jsonResponse(['success' => false, 'message' => 'Transaction not found']);
            }

            $transaction['items'] = $this->db->fetchAll(
                "SELECT * FROM pos_transaction_items WHERE transaction_id = ?",
                [$transaction['id']]
            );

            $this->jsonResponse(['success' => true, 'transaction' => $transaction]);
        }

        // Search by phone or date range
        $phone = trim($this->get('phone', ''));
        $dateFrom = $this->get('from', date('Y-m-d'));
        $dateTo = $this->get('to', date('Y-m-d'));

        $params = [$storeId, $dateFrom, $dateTo];
        $whereClauses = ["t.store_id = ?", "DATE(t.created_at) BETWEEN ? AND ?"];

        if (!empty($phone)) {
            $whereClauses[] = "t.customer_phone LIKE ?";
            $params[] = "%{$phone}%";
        }

        $whereSQL = implode(' AND ', $whereClauses);
        $transactions = $this->db->fetchAll(
            "SELECT t.*, u.name as cashier_name
             FROM pos_transactions t
             LEFT JOIN users u ON t.created_by = u.id
             WHERE {$whereSQL}
             ORDER BY t.created_at DESC
             LIMIT 50",
            $params
        );

        if (empty($transactions)) {
            $this->jsonResponse(['success' => false, 'message' => 'No transactions found']);
        }

        $this->jsonResponse(['success' => true, 'transactions' => $transactions]);
    }

    /**
     * Process refund submission
     */
    public function processRefund(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!Session::validateCsrf($input['csrf_token'] ?? '')) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request']);
        }

        $userId = (int) Session::getUserId() ?? 0;
        $transactionId = (int) ($input['transaction_id'] ?? 0);
        $items = $input['items'] ?? [];
        $reason = trim($input['reason'] ?? '');
        $notes = trim($input['notes'] ?? '');
        $refundMethod = trim($input['refund_method'] ?? 'cash');

        if (empty($items)) {
            $this->jsonResponse(['success' => false, 'message' => 'Select items to refund']);
        }

        if (empty($reason)) {
            $this->jsonResponse(['success' => false, 'message' => 'Provide refund reason']);
        }

        try {
            $result = $this->posModel->processRefund($transactionId, $items, $reason, $userId, $notes, $refundMethod);
            $this->jsonResponse([
                'success' => true,
                'message' => 'Refund processed successfully',
                'refund_id' => $result['refund_id'],
                'refund_number' => $result['refund_number'],
                'refund_amount' => $result['refund_amount']
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get daily summary (AJAX)
     */
    public function getDailySummary(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $userId = (int) Session::getUserId() ?? 0;

        $currentShift = $this->posModel->getCurrentShift($userId, $storeId);
        $shiftId = $currentShift ? $currentShift['id'] : null;

        $summary = $this->posModel->getDailySummary($storeId, $shiftId);
        $this->jsonResponse(['success' => true, 'summary' => $summary]);
    }

    /**
     * JSON response helper
     */
    private function jsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
