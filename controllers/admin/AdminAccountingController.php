<?php
/**
 * Admin Accounting Controller
 * Chart of Accounts and Journal Entries
 */

class AdminAccountingController extends Controller
{
    private ChartOfAccounts $accountModel;

    public function __construct()
    {
        parent::__construct();

        // Require admin access
        if (!Session::isAdmin()) {
            $this->redirect('admin/login');
        }

        // Only full admin can access accounting
        if (Session::get('admin_role') !== 'admin') {
            $this->redirect('admin', 'Access denied', 'error');
        }

        $this->accountModel = new ChartOfAccounts();
    }

    /**
     * Accounting dashboard
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $accountSummary = $this->accountModel->getAccountSummary($storeId);
        $trialBalance = $this->accountModel->getTrialBalance($storeId);

        // Recent journal entries
        $recentEntries = $this->accountModel->getJournalEntries($storeId, [], 1, 5);

        $this->view('admin/accounting/index', [
            'pageTitle' => 'Accounting - Admin',
            'accountSummary' => $accountSummary,
            'trialBalance' => $trialBalance,
            'recentEntries' => $recentEntries['data']
        ], 'admin');
    }

    /**
     * Chart of Accounts
     */
    public function accounts(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $accountsByType = $this->accountModel->getByType($storeId);

        $this->view('admin/accounting/accounts', [
            'pageTitle' => 'Chart of Accounts - Admin',
            'accountsByType' => $accountsByType
        ], 'admin');
    }

    /**
     * Store new account
     */
    public function storeAccount(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/accounting/accounts', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'account_code' => trim($this->post('account_code')),
            'account_name' => trim($this->post('account_name')),
            'account_type' => $this->post('account_type'),
            'description' => trim($this->post('description')),
            'normal_balance' => in_array($this->post('account_type'), ['asset', 'expense', 'cogs']) ? 'debit' : 'credit',
            'is_active' => 1,
            'is_system' => 0,
            'current_balance' => 0
        ];

        if (empty($data['account_code']) || empty($data['account_name'])) {
            $this->redirect('admin/accounting/accounts', 'Account code and name are required', 'error');
        }

        $this->accountModel->createAccount($data);
        $this->redirect('admin/accounting/accounts', 'Account created successfully', 'success');
    }

    /**
     * Journal entries list
     */
    public function journal(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $page = (int) $this->get('page', 1);

        $filters = [
            'status' => $this->get('status'),
            'start_date' => $this->get('start_date'),
            'end_date' => $this->get('end_date')
        ];

        $result = $this->accountModel->getJournalEntries($storeId, $filters, $page);

        $this->view('admin/accounting/journal', [
            'pageTitle' => 'Journal Entries - Admin',
            'entries' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters
        ], 'admin');
    }

    /**
     * Create journal entry form
     */
    public function createEntry(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $accounts = $this->accountModel->getActive($storeId);

        $this->view('admin/accounting/journal-create', [
            'pageTitle' => 'Create Journal Entry - Admin',
            'accounts' => $accounts
        ], 'admin');
    }

    /**
     * Store journal entry
     */
    public function storeEntry(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/accounting/journal', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $entryData = [
            'entry_date' => $this->post('entry_date'),
            'description' => trim($this->post('description')),
            'reference_type' => 'manual',
            'status' => $this->post('post_immediately') ? 'posted' : 'draft',
            'created_by' => Session::get('admin_user_id')
        ];

        if ($entryData['status'] === 'posted') {
            $entryData['posted_at'] = date('Y-m-d H:i:s');
        }

        // Parse lines
        $lines = [];
        $accountIds = $this->post('account_id', []);
        $descriptions = $this->post('line_description', []);
        $debits = $this->post('debit_amount', []);
        $credits = $this->post('credit_amount', []);

        foreach ($accountIds as $index => $accountId) {
            if (!empty($accountId) && ((float)($debits[$index] ?? 0) > 0 || (float)($credits[$index] ?? 0) > 0)) {
                $lines[] = [
                    'account_id' => (int) $accountId,
                    'description' => $descriptions[$index] ?? null,
                    'debit_amount' => (float) ($debits[$index] ?? 0),
                    'credit_amount' => (float) ($credits[$index] ?? 0)
                ];
            }
        }

        if (count($lines) < 2) {
            $this->redirect('admin/accounting/journal/create', 'At least 2 lines are required', 'error');
        }

        try {
            $entryId = $this->accountModel->createJournalEntry($storeId, $entryData, $lines);

            // If posting immediately, update account balances
            if ($entryData['status'] === 'posted') {
                foreach ($lines as $line) {
                    if ($line['debit_amount'] > 0) {
                        $this->accountModel->updateBalance($line['account_id'], $line['debit_amount'], 'debit');
                    }
                    if ($line['credit_amount'] > 0) {
                        $this->accountModel->updateBalance($line['account_id'], $line['credit_amount'], 'credit');
                    }
                }
            }

            $this->redirect('admin/accounting/journal', 'Journal entry created successfully', 'success');
        } catch (Exception $e) {
            $this->redirect('admin/accounting/journal/create', $e->getMessage(), 'error');
        }
    }
}
