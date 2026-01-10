<?php
/**
 * Admin Expense Controller
 */

class AdminExpenseController extends Controller
{
    private Expense $expenseModel;
    private ExpenseCategory $categoryModel;

    public function __construct()
    {
        parent::__construct();

        // Require admin or manager access
        if (!Session::isAdmin() && !Session::isManager()) {
            $this->redirect('admin/login');
        }

        $this->expenseModel = new Expense();
        $this->categoryModel = new ExpenseCategory();
    }

    /**
     * List expenses
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $page = (int) $this->get('page', 1);

        $filters = [
            'category_id' => $this->get('category_id'),
            'payment_status' => $this->get('payment_status'),
            'start_date' => $this->get('start_date'),
            'end_date' => $this->get('end_date'),
            'search' => $this->get('search')
        ];

        // Default date range: current month
        if (empty($filters['start_date']) && empty($filters['end_date'])) {
            $filters['start_date'] = date('Y-m-01');
            $filters['end_date'] = date('Y-m-t');
        }

        $result = $this->expenseModel->getAdminExpenses($storeId, $filters, $page);
        $categories = $this->categoryModel->getByStore($storeId);
        $stats = $this->expenseModel->getStats($storeId, $filters['start_date'], $filters['end_date']);

        $this->view('admin/expenses/index', [
            'pageTitle' => 'Expenses - Admin',
            'expenses' => $result['data'],
            'pagination' => $result['pagination'],
            'categories' => $categories,
            'stats' => $stats,
            'filters' => $filters
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $categories = $this->categoryModel->getByStore($storeId);

        $this->view('admin/expenses/create', [
            'pageTitle' => 'Add Expense - Admin',
            'categories' => $categories
        ], 'admin');
    }

    /**
     * Store new expense
     */
    public function store(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/expenses', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'category_id' => $this->post('category_id') ?: null,
            'title' => trim($this->post('title')),
            'description' => trim($this->post('description')),
            'amount' => (float) $this->post('amount'),
            'tax_amount' => (float) $this->post('tax_amount', 0),
            'expense_date' => $this->post('expense_date'),
            'payment_method' => $this->post('payment_method'),
            'payment_status' => $this->post('payment_status'),
            'reference_number' => trim($this->post('reference_number')),
            'vendor_name' => trim($this->post('vendor_name')),
            'notes' => trim($this->post('notes')),
            'created_by' => Session::getUserId()
        ];

        // Validate required fields
        if (empty($data['title']) || empty($data['amount']) || empty($data['expense_date'])) {
            $this->redirect('admin/expenses/create', 'Title, amount, and date are required', 'error');
        }

        // Handle receipt upload
        if (!empty($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $receiptPath = $this->uploadFile($_FILES['receipt'], 'receipts');
            if ($receiptPath) {
                $data['receipt_path'] = $receiptPath;
            }
        }

        $expenseId = $this->expenseModel->createExpense($data);

        if ($expenseId) {
            $this->redirect('admin/expenses', 'Expense added successfully', 'success');
        } else {
            $this->redirect('admin/expenses/create', 'Failed to add expense', 'error');
        }
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $expense = $this->expenseModel->getWithCategory($id);

        if (!$expense || $expense['store_id'] != $storeId) {
            $this->redirect('admin/expenses', 'Expense not found', 'error');
        }

        $categories = $this->categoryModel->getByStore($storeId);

        $this->view('admin/expenses/edit', [
            'pageTitle' => 'Edit Expense - Admin',
            'expense' => $expense,
            'categories' => $categories
        ], 'admin');
    }

    /**
     * Update expense
     */
    public function update(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/expenses', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $expense = $this->expenseModel->find($id);

        if (!$expense || $expense['store_id'] != $storeId) {
            $this->redirect('admin/expenses', 'Expense not found', 'error');
        }

        $data = [
            'category_id' => $this->post('category_id') ?: null,
            'title' => trim($this->post('title')),
            'description' => trim($this->post('description')),
            'amount' => (float) $this->post('amount'),
            'tax_amount' => (float) $this->post('tax_amount', 0),
            'expense_date' => $this->post('expense_date'),
            'payment_method' => $this->post('payment_method'),
            'payment_status' => $this->post('payment_status'),
            'reference_number' => trim($this->post('reference_number')),
            'vendor_name' => trim($this->post('vendor_name')),
            'notes' => trim($this->post('notes'))
        ];

        // Handle receipt upload
        if (!empty($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            // Delete old receipt
            if ($expense['receipt_path']) {
                $oldFile = UPLOAD_PATH . '/' . $expense['receipt_path'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $receiptPath = $this->uploadFile($_FILES['receipt'], 'receipts');
            if ($receiptPath) {
                $data['receipt_path'] = $receiptPath;
            }
        }

        $this->expenseModel->updateExpense($id, $data);
        $this->redirect('admin/expenses', 'Expense updated successfully', 'success');
    }

    /**
     * Delete expense
     */
    public function delete(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $expense = $this->expenseModel->find($id);

        if (!$expense || $expense['store_id'] != $storeId) {
            $this->redirect('admin/expenses', 'Expense not found', 'error');
        }

        $this->expenseModel->deleteExpense($id);
        $this->redirect('admin/expenses', 'Expense deleted successfully', 'success');
    }

    /**
     * Manage expense categories
     */
    public function categories(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $categories = $this->categoryModel->getWithExpenseCount($storeId);

        $this->view('admin/expenses/categories', [
            'pageTitle' => 'Expense Categories - Admin',
            'categories' => $categories
        ], 'admin');
    }

    /**
     * Store new category
     */
    public function storeCategory(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/expenses/categories', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'name' => trim($this->post('name')),
            'description' => trim($this->post('description')),
            'color' => $this->post('color', '#6c757d'),
            'icon' => $this->post('icon', 'tag'),
            'is_active' => 1
        ];

        if (empty($data['name'])) {
            $this->redirect('admin/expenses/categories', 'Category name is required', 'error');
        }

        $this->categoryModel->createCategory($data);
        $this->redirect('admin/expenses/categories', 'Category added successfully', 'success');
    }

    /**
     * Update category
     */
    public function updateCategory(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $storeId = Session::get('admin_store_id', 1);
        $category = $this->categoryModel->find($id);

        if (!$category || $category['store_id'] != $storeId) {
            $this->json(['success' => false, 'message' => 'Category not found']);
        }

        $data = [
            'name' => trim($this->post('name')),
            'description' => trim($this->post('description')),
            'color' => $this->post('color'),
            'icon' => $this->post('icon'),
            'is_active' => (int) $this->post('is_active', 1)
        ];

        $this->categoryModel->updateCategory($id, $data);
        $this->json(['success' => true, 'message' => 'Category updated']);
    }

    /**
     * Delete category
     */
    public function deleteCategory(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $category = $this->categoryModel->find($id);

        if (!$category || $category['store_id'] != $storeId) {
            $this->redirect('admin/expenses/categories', 'Category not found', 'error');
        }

        $deleted = $this->categoryModel->deleteCategory($id);

        if ($deleted) {
            $this->redirect('admin/expenses/categories', 'Category deleted successfully', 'success');
        } else {
            $this->redirect('admin/expenses/categories', 'Cannot delete category with existing expenses', 'error');
        }
    }
}
