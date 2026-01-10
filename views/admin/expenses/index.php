<div class="page-header">
    <h1>Expenses</h1>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/expenses/categories') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-tags me-2"></i> Categories
        </a>
        <a href="<?= url('admin/expenses/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add Expense
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Total Expenses</h6>
                        <h3 class="mb-0"><?= formatPrice($stats['totals']['total'] ?? 0) ?></h3>
                    </div>
                    <i class="fas fa-receipt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Paid</h6>
                        <h3 class="mb-0"><?= formatPrice($stats['totals']['paid'] ?? 0) ?></h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Pending</h6>
                        <h3 class="mb-0"><?= formatPrice($stats['totals']['pending'] ?? 0) ?></h3>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Count</h6>
                        <h3 class="mb-0"><?= number_format($stats['totals']['count'] ?? 0) ?></h3>
                    </div>
                    <i class="fas fa-list fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('admin/expenses') ?>" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?= $filters['start_date'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?= $filters['end_date'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= sanitize($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="payment_status" class="form-select">
                    <option value="">All Status</option>
                    <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="pending" <?= ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="partial" <?= ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' ?>>Partial</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Title, number..." value="<?= $filters['search'] ?? '' ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Expenses Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($expenses)): ?>
        <div class="text-center py-5">
            <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No expenses found</h5>
            <p class="text-muted">Start by adding your first expense</p>
            <a href="<?= url('admin/expenses/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Add Expense
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Expense #</th>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <td>
                            <span class="fw-medium"><?= $expense['expense_number'] ?></span>
                        </td>
                        <td><?= date('M d, Y', strtotime($expense['expense_date'])) ?></td>
                        <td>
                            <div class="fw-medium"><?= sanitize($expense['title']) ?></div>
                            <?php if ($expense['receipt_path']): ?>
                            <small class="text-muted"><i class="fas fa-paperclip"></i> Receipt attached</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($expense['category_name']): ?>
                            <span class="badge" style="background-color: <?= $expense['category_color'] ?>">
                                <i class="fas fa-<?= $expense['category_icon'] ?> me-1"></i>
                                <?= sanitize($expense['category_name']) ?>
                            </span>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $expense['vendor_name'] ? sanitize($expense['vendor_name']) : '-' ?></td>
                        <td class="text-end">
                            <strong><?= formatPrice($expense['total_amount']) ?></strong>
                            <?php if ($expense['tax_amount'] > 0): ?>
                            <br><small class="text-muted">Tax: <?= formatPrice($expense['tax_amount']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $statusClass = match($expense['payment_status']) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'partial' => 'info',
                                default => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($expense['payment_status']) ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('admin/expenses/edit/' . $expense['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($expense['receipt_path']): ?>
                                <a href="<?= upload($expense['receipt_path']) ?>" target="_blank" class="btn btn-outline-info" title="View Receipt">
                                    <i class="fas fa-file-image"></i>
                                </a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteExpense(<?= $expense['id'] ?>)" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('admin/expenses') ?>?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteExpense(id) {
    if (confirm('Are you sure you want to delete this expense?')) {
        window.location.href = '<?= url('admin/expenses/delete') ?>/' + id;
    }
}
</script>
