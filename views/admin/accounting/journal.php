<div class="page-header">
    <h1>Journal Entries</h1>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/accounting/journal/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> New Entry
        </a>
        <a href="<?= url('admin/accounting') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?= $filters['start_date'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?= $filters['end_date'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="posted" <?= ($filters['status'] ?? '') === 'posted' ? 'selected' : '' ?>>Posted</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Entries List -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($entries)): ?>
        <div class="text-center py-5">
            <i class="fas fa-book fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No journal entries found</h5>
            <p class="text-muted">Create your first journal entry</p>
            <a href="<?= url('admin/accounting/journal/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> New Entry
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Entry #</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                        <th>Status</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><code><?= sanitize($entry['entry_number']) ?></code></td>
                        <td><?= date('M d, Y', strtotime($entry['entry_date'])) ?></td>
                        <td>
                            <?= sanitize(strlen($entry['description']) > 60 ? substr($entry['description'], 0, 60) . '...' : $entry['description']) ?>
                        </td>
                        <td>
                            <?php
                            $typeLabels = [
                                'manual' => 'Manual',
                                'order' => 'Order',
                                'expense' => 'Expense',
                                'purchase' => 'Purchase',
                                'return' => 'Return',
                                'payment' => 'Payment'
                            ];
                            ?>
                            <span class="badge bg-light text-dark"><?= $typeLabels[$entry['reference_type']] ?? ucfirst($entry['reference_type']) ?></span>
                        </td>
                        <td class="text-end"><?= formatPrice($entry['total_debit']) ?></td>
                        <td class="text-end"><?= formatPrice($entry['total_credit']) ?></td>
                        <td>
                            <?php if ($entry['status'] === 'posted'): ?>
                            <span class="badge bg-success">Posted</span>
                            <?php elseif ($entry['status'] === 'reversed'): ?>
                            <span class="badge bg-danger">Reversed</span>
                            <?php else: ?>
                            <span class="badge bg-warning">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted"><?= sanitize($entry['created_by_name'] ?? 'System') ?></small>
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
                    <?php if ($pagination['current_page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&<?= http_build_query($filters) ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query($filters) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&<?= http_build_query($filters) ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
