<?php

class AdminReturnController extends Controller
{
    private $returnModel;

    public function __construct()
    {
        parent::__construct();
        $this->returnModel = new ProductReturn();

        // Check admin authentication (allows both admin and manager)
        if (!Auth::isAdmin()) {
            $this->redirect('admin/login');
        }
    }

    /**
     * List all returns
     */
    public function index()
    {
        $storeId = Session::get('admin_store_id', 1);
        $page = (int)($_GET['page'] ?? 1);

        $filters = [
            'search' => $_GET['search'] ?? '',
            'reason' => $_GET['reason'] ?? ''
        ];

        $returns = $this->returnModel->getAdminReturns($page, 20, $storeId, $filters);
        $stats = $this->returnModel->getStats($storeId);

        $this->view('admin/returns/index', [
            'returns' => $returns,
            'stats' => $stats,
            'filters' => $filters,
            'reasons' => ProductReturn::getReasonOptions(),
            'pageTitle' => 'Product Returns'
        ], 'admin');
    }

    /**
     * Show create return form
     */
    public function create()
    {
        $storeId = Session::get('admin_store_id', 1);

        // Get orders eligible for return
        $eligibleOrders = $this->returnModel->getEligibleOrders($storeId);

        $this->view('admin/returns/create', [
            'orders' => $eligibleOrders,
            'reasons' => ProductReturn::getReasonOptions(),
            'pageTitle' => 'Record Return'
        ], 'admin');
    }

    /**
     * Store new return
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/returns');
        }

        // Verify CSRF
        if (!Session::validateCsrf($_POST['csrf_token'] ?? '')) {
            Session::setFlash('Invalid request', 'error');
            $this->redirect('admin/returns/create');
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        $reason = $_POST['reason'] ?? '';
        $reasonDetails = trim($_POST['reason_details'] ?? '');
        $adminNotes = trim($_POST['admin_notes'] ?? '');

        // Validate
        if (empty($orderId)) {
            Session::setFlash('Please select an order', 'error');
            $this->redirect('admin/returns/create');
        }

        if (empty($reason)) {
            Session::setFlash('Please select a reason', 'error');
            $this->redirect('admin/returns/create');
        }

        try {
            $returnId = $this->returnModel->createFromOrder($orderId, [
                'reason' => $reason,
                'reason_details' => $reasonDetails,
                'admin_notes' => $adminNotes
            ]);

            Session::setFlash('Return recorded successfully. Stock has been restored.', 'success');
            $this->redirect('admin/returns/show/' . $returnId);

        } catch (Exception $e) {
            Session::setFlash($e->getMessage(), 'error');
            $this->redirect('admin/returns/create');
        }
    }

    /**
     * View return details
     */
    public function show($id)
    {
        $storeId = Session::get('admin_store_id', 1);
        $return = $this->returnModel->getWithDetails($id);

        if (!$return || $return['store_id'] != $storeId) {
            Session::setFlash('Return not found', 'error');
            $this->redirect('admin/returns');
        }

        $this->view('admin/returns/show', [
            'return' => $return,
            'pageTitle' => 'Return #' . $return['return_number']
        ], 'admin');
    }

    /**
     * Update refund status
     */
    public function updateRefund($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/returns');
        }

        // Verify CSRF
        if (!Session::validateCsrf($_POST['csrf_token'] ?? '')) {
            Session::setFlash('Invalid request', 'error');
            $this->redirect('admin/returns/show/' . $id);
        }

        $storeId = Session::get('admin_store_id', 1);
        $return = $this->returnModel->find($id);

        if (!$return || $return['store_id'] != $storeId) {
            Session::setFlash('Return not found', 'error');
            $this->redirect('admin/returns');
        }

        $refundStatus = $_POST['refund_status'] ?? '';

        if (in_array($refundStatus, ['pending', 'completed'])) {
            $this->returnModel->updateRefundStatus($id, $refundStatus);
            Session::setFlash('Refund status updated', 'success');
        }

        $this->redirect('admin/returns/show/' . $id);
    }

    /**
     * Save admin notes
     */
    public function saveNotes($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/returns');
        }

        // Verify CSRF
        if (!Session::validateCsrf($_POST['csrf_token'] ?? '')) {
            Session::setFlash('Invalid request', 'error');
            $this->redirect('admin/returns/show/' . $id);
        }

        $storeId = Session::get('admin_store_id', 1);
        $return = $this->returnModel->find($id);

        if (!$return || $return['store_id'] != $storeId) {
            Session::setFlash('Return not found', 'error');
            $this->redirect('admin/returns');
        }

        $adminNotes = trim($_POST['admin_notes'] ?? '');
        $this->returnModel->update($id, ['admin_notes' => $adminNotes]);

        Session::setFlash('Notes saved', 'success');
        $this->redirect('admin/returns/show/' . $id);
    }

    /**
     * Delete return (admin only)
     */
    public function delete($id)
    {
        // Only full admin can delete
        $user = Auth::user();
        if ($user['role'] !== 'admin') {
            Session::setFlash('Only admin can delete returns', 'error');
            $this->redirect('admin/returns');
        }

        $storeId = Session::get('admin_store_id', 1);
        $return = $this->returnModel->find($id);

        if (!$return || $return['store_id'] != $storeId) {
            Session::setFlash('Return not found', 'error');
            $this->redirect('admin/returns');
        }

        // Note: Stock was already restored when return was created
        // Deleting just removes the record
        $deleted = $this->returnModel->delete($id);

        if ($deleted) {
            Session::setFlash('Return record deleted', 'success');
        } else {
            Session::setFlash('Failed to delete return', 'error');
        }

        $this->redirect('admin/returns');
    }
}
