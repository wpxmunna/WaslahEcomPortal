<div class="page-header">
    <h1>Accounting</h1>
</div>

<!-- Quick Links -->
<div class="row mb-4">
    <div class="col-md-4">
        <a href="<?= url('admin/accounting/accounts') ?>" class="card text-decoration-none h-100">
            <div class="card-body text-center">
                <i class="fas fa-sitemap fa-3x text-primary mb-3"></i>
                <h5>Chart of Accounts</h5>
                <p class="text-muted mb-0">Manage your accounts</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= url('admin/accounting/journal') ?>" class="card text-decoration-none h-100">
            <div class="card-body text-center">
                <i class="fas fa-book fa-3x text-success mb-3"></i>
                <h5>Journal Entries</h5>
                <p class="text-muted mb-0">View and create entries</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= url('admin/accounting/journal/create') ?>" class="card text-decoration-none h-100">
            <div class="card-body text-center">
                <i class="fas fa-plus-circle fa-3x text-info mb-3"></i>
                <h5>New Entry</h5>
                <p class="text-muted mb-0">Create journal entry</p>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <!-- Account Summary -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Account Summary</h5>
            </div>
            <div class="card-body">
                <?php if (empty($accountSummary)): ?>
                <p class="text-muted text-center mb-0">No accounts found</p>
                <?php else: ?>
                <?php
                $typeLabels = [
                    'asset' => ['Assets', 'text-primary'],
                    'liability' => ['Liabilities', 'text-danger'],
                    'equity' => ['Equity', 'text-info'],
                    'revenue' => ['Revenue', 'text-success'],
                    'expense' => ['Expenses', 'text-warning'],
                    'cogs' => ['Cost of Goods', 'text-secondary']
                ];
                ?>
                <?php foreach ($accountSummary as $summary): ?>
                <div class="d-flex justify-content-between mb-3">
                    <span class="<?= $typeLabels[$summary['account_type']][1] ?? '' ?>">
                        <strong><?= $typeLabels[$summary['account_type']][0] ?? ucfirst($summary['account_type']) ?></strong>
                        <br><small class="text-muted"><?= $summary['account_count'] ?> accounts</small>
                    </span>
                    <span class="text-end">
                        <strong><?= formatPrice(abs($summary['total_balance'])) ?></strong>
                    </span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Trial Balance -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-balance-scale me-2"></i> Trial Balance</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($trialBalance)): ?>
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No account balances to display</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Code</th>
                                <th>Account</th>
                                <th>Type</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalDebit = 0;
                            $totalCredit = 0;
                            foreach ($trialBalance as $account):
                                $isDebit = $account['normal_balance'] === 'debit';
                                $balance = abs($account['current_balance']);
                                if ($isDebit) {
                                    $totalDebit += $balance;
                                } else {
                                    $totalCredit += $balance;
                                }
                            ?>
                            <tr>
                                <td><code><?= $account['account_code'] ?></code></td>
                                <td><?= sanitize($account['account_name']) ?></td>
                                <td><span class="badge bg-secondary"><?= ucfirst($account['account_type']) ?></span></td>
                                <td class="text-end"><?= $isDebit ? formatPrice($balance) : '-' ?></td>
                                <td class="text-end"><?= !$isDebit ? formatPrice($balance) : '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="3">Total</th>
                                <th class="text-end"><?= formatPrice($totalDebit) ?></th>
                                <th class="text-end"><?= formatPrice($totalCredit) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Journal Entries -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-book me-2"></i> Recent Journal Entries</h5>
                <a href="<?= url('admin/accounting/journal') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentEntries)): ?>
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No journal entries yet</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Entry #</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentEntries as $entry): ?>
                            <tr>
                                <td><code><?= sanitize($entry['entry_number']) ?></code></td>
                                <td><?= date('M d, Y', strtotime($entry['entry_date'])) ?></td>
                                <td><?= sanitize(substr($entry['description'], 0, 50)) ?>...</td>
                                <td class="text-end"><?= formatPrice($entry['total_debit']) ?></td>
                                <td>
                                    <?php if ($entry['status'] === 'posted'): ?>
                                    <span class="badge bg-success">Posted</span>
                                    <?php else: ?>
                                    <span class="badge bg-warning">Draft</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
