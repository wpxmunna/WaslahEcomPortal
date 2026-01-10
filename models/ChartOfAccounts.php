<?php
/**
 * Chart of Accounts Model
 * Double-entry accounting system
 */

class ChartOfAccounts extends Model
{
    protected string $table = 'chart_of_accounts';
    protected array $fillable = [
        'store_id', 'account_code', 'account_name', 'account_type',
        'parent_id', 'description', 'is_system', 'is_active',
        'normal_balance', 'current_balance'
    ];

    /**
     * Get all accounts organized by type
     */
    public function getByType(int $storeId): array
    {
        $accounts = $this->db->fetchAll(
            "SELECT * FROM chart_of_accounts
             WHERE store_id = ?
             ORDER BY account_type, account_code",
            [$storeId]
        );

        $organized = [
            'asset' => [],
            'liability' => [],
            'equity' => [],
            'revenue' => [],
            'expense' => [],
            'cogs' => []
        ];

        foreach ($accounts as $account) {
            $organized[$account['account_type']][] = $account;
        }

        return $organized;
    }

    /**
     * Get all active accounts for dropdown
     */
    public function getActive(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM chart_of_accounts
             WHERE store_id = ? AND is_active = 1
             ORDER BY account_type, account_code",
            [$storeId]
        );
    }

    /**
     * Create account
     */
    public function createAccount(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update account balance
     */
    public function updateBalance(int $accountId, float $amount, string $type): void
    {
        $account = $this->find($accountId);
        if (!$account) return;

        // Determine if amount increases or decreases balance
        // Debit increases: Assets, Expenses, COGS
        // Credit increases: Liabilities, Equity, Revenue
        $debitTypes = ['asset', 'expense', 'cogs'];
        $normalBalance = in_array($account['account_type'], $debitTypes) ? 'debit' : 'credit';

        if ($type === $normalBalance) {
            $newBalance = $account['current_balance'] + $amount;
        } else {
            $newBalance = $account['current_balance'] - $amount;
        }

        $this->db->update($this->table, [
            'current_balance' => $newBalance
        ], 'id = ?', [$accountId]);
    }

    /**
     * Get account summary for reporting
     */
    public function getAccountSummary(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT account_type,
                    COUNT(*) as account_count,
                    SUM(current_balance) as total_balance
             FROM chart_of_accounts
             WHERE store_id = ? AND is_active = 1
             GROUP BY account_type
             ORDER BY FIELD(account_type, 'asset', 'liability', 'equity', 'revenue', 'expense', 'cogs')",
            [$storeId]
        );
    }

    /**
     * Create journal entry
     */
    public function createJournalEntry(int $storeId, array $entryData, array $lines): int
    {
        // Generate entry number
        $entryData['entry_number'] = 'JE' . date('Ymd') . strtoupper(substr(uniqid(), -4));
        $entryData['store_id'] = $storeId;
        $entryData['created_at'] = date('Y-m-d H:i:s');

        // Calculate totals
        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($lines as $line) {
            $totalDebit += $line['debit_amount'] ?? 0;
            $totalCredit += $line['credit_amount'] ?? 0;
        }

        // Validate debits = credits
        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new Exception('Debits must equal credits');
        }

        $entryData['total_debit'] = $totalDebit;
        $entryData['total_credit'] = $totalCredit;

        $entryId = $this->db->insert('journal_entries', $entryData);

        // Insert lines
        foreach ($lines as $line) {
            $this->db->insert('journal_entry_lines', [
                'journal_entry_id' => $entryId,
                'account_id' => $line['account_id'],
                'description' => $line['description'] ?? null,
                'debit_amount' => $line['debit_amount'] ?? 0,
                'credit_amount' => $line['credit_amount'] ?? 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $entryId;
    }

    /**
     * Post journal entry (update account balances)
     */
    public function postJournalEntry(int $entryId): bool
    {
        $entry = $this->db->fetch(
            "SELECT * FROM journal_entries WHERE id = ?",
            [$entryId]
        );

        if (!$entry || $entry['status'] !== 'draft') {
            return false;
        }

        $lines = $this->db->fetchAll(
            "SELECT * FROM journal_entry_lines WHERE journal_entry_id = ?",
            [$entryId]
        );

        foreach ($lines as $line) {
            if ($line['debit_amount'] > 0) {
                $this->updateBalance($line['account_id'], $line['debit_amount'], 'debit');
            }
            if ($line['credit_amount'] > 0) {
                $this->updateBalance($line['account_id'], $line['credit_amount'], 'credit');
            }
        }

        $this->db->update('journal_entries', [
            'status' => 'posted',
            'posted_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$entryId]);

        return true;
    }

    /**
     * Get journal entries with filters
     */
    public function getJournalEntries(int $storeId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = 'je.store_id = ?';
        $params = [$storeId];

        if (!empty($filters['status'])) {
            $where .= ' AND je.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['start_date'])) {
            $where .= ' AND je.entry_date >= ?';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where .= ' AND je.entry_date <= ?';
            $params[] = $filters['end_date'];
        }

        // Get total count
        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as total FROM journal_entries je WHERE {$where}",
            $params
        );
        $total = $countResult['total'];

        // Calculate pagination
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        // Get entries
        $entries = $this->db->fetchAll(
            "SELECT je.*, u.name as created_by_name
             FROM journal_entries je
             LEFT JOIN users u ON je.created_by = u.id
             WHERE {$where}
             ORDER BY je.entry_date DESC, je.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $entries,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Get journal entry with lines
     */
    public function getJournalEntryWithLines(int $entryId): ?array
    {
        $entry = $this->db->fetch(
            "SELECT je.*, u.name as created_by_name
             FROM journal_entries je
             LEFT JOIN users u ON je.created_by = u.id
             WHERE je.id = ?",
            [$entryId]
        );

        if ($entry) {
            $entry['lines'] = $this->db->fetchAll(
                "SELECT jel.*, coa.account_code, coa.account_name, coa.account_type
                 FROM journal_entry_lines jel
                 JOIN chart_of_accounts coa ON jel.account_id = coa.id
                 WHERE jel.journal_entry_id = ?
                 ORDER BY jel.id",
                [$entryId]
            );
        }

        return $entry;
    }

    /**
     * Get trial balance
     */
    public function getTrialBalance(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT account_code, account_name, account_type, normal_balance, current_balance
             FROM chart_of_accounts
             WHERE store_id = ? AND is_active = 1 AND current_balance != 0
             ORDER BY account_type, account_code",
            [$storeId]
        );
    }
}
