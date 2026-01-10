<div class="page-header">
    <h1>Chart of Accounts</h1>
    <a href="<?= url('admin/accounting') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <?php
        $typeLabels = [
            'asset' => ['Assets', 'primary', 'Debit'],
            'liability' => ['Liabilities', 'danger', 'Credit'],
            'equity' => ['Equity', 'info', 'Credit'],
            'revenue' => ['Revenue', 'success', 'Credit'],
            'expense' => ['Expenses', 'warning', 'Debit'],
            'cogs' => ['Cost of Goods Sold', 'secondary', 'Debit']
        ];
        ?>

        <?php foreach ($typeLabels as $type => $info): ?>
        <div class="card mb-4">
            <div class="card-header bg-<?= $info[1] ?> bg-opacity-10">
                <h5 class="mb-0 text-<?= $info[1] ?>">
                    <?= $info[0] ?>
                    <small class="text-muted ms-2">(Normal Balance: <?= $info[2] ?>)</small>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($accountsByType[$type])): ?>
                <div class="text-center py-3">
                    <p class="text-muted mb-0">No accounts in this category</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Code</th>
                                <th>Account Name</th>
                                <th>Description</th>
                                <th class="text-end">Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accountsByType[$type] as $account): ?>
                            <tr>
                                <td><code><?= sanitize($account['account_code']) ?></code></td>
                                <td>
                                    <strong><?= sanitize($account['account_name']) ?></strong>
                                    <?php if ($account['is_system']): ?>
                                    <span class="badge bg-secondary ms-1">System</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?= sanitize($account['description'] ?: '-') ?></small></td>
                                <td class="text-end">
                                    <?php if ($account['current_balance'] != 0): ?>
                                    <strong><?= formatPrice(abs($account['current_balance'])) ?></strong>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($account['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
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
        <?php endforeach; ?>
    </div>

    <div class="col-lg-4">
        <!-- Add Account Form -->
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i> Add New Account</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('admin/accounting/accounts/store') ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

                    <div class="mb-3">
                        <label class="form-label">Account Code <span class="text-danger">*</span></label>
                        <input type="text" name="account_code" class="form-control" required placeholder="e.g., 1001">
                        <small class="text-muted">Unique identifier for the account</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" class="form-control" required placeholder="e.g., Cash in Hand">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Account Type <span class="text-danger">*</span></label>
                        <select name="account_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                            <option value="equity">Equity</option>
                            <option value="revenue">Revenue</option>
                            <option value="expense">Expense</option>
                            <option value="cogs">Cost of Goods Sold</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Optional description..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Add Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
