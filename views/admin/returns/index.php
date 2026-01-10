<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h1 class="h3 mb-1">Product Returns</h1>
        <p class="text-muted mb-0">Manage returned products and restore inventory</p>
    </div>
    <a href="<?= url('admin/returns/create') ?>" class="btn btn-primary btn-lg shadow-sm">
        <i class="fas fa-plus-circle me-2"></i> Record Return
    </a>
</div>

<!-- Stats Cards with Gradient -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-icon bg-primary bg-gradient rounded-3 p-3">
                            <i class="fas fa-undo text-white fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted fw-normal mb-1">Total Returns</h6>
                        <h3 class="mb-0 fw-bold"><?= number_format($stats['total_returns']) ?></h3>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-primary bg-opacity-10 border-0 py-2">
                <small class="text-primary"><i class="fas fa-box me-1"></i> All time</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-icon bg-info bg-gradient rounded-3 p-3">
                            <i class="fas fa-calendar-day text-white fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted fw-normal mb-1">Today</h6>
                        <h3 class="mb-0 fw-bold"><?= number_format($stats['today_returns']) ?></h3>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-info bg-opacity-10 border-0 py-2">
                <small class="text-info"><i class="fas fa-clock me-1"></i> <?= date('M d, Y') ?></small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-icon bg-success bg-gradient rounded-3 p-3">
                            <i class="fas fa-calendar-alt text-white fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted fw-normal mb-1">This Month</h6>
                        <h3 class="mb-0 fw-bold"><?= number_format($stats['month_returns']) ?></h3>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-success bg-opacity-10 border-0 py-2">
                <small class="text-success"><i class="fas fa-chart-line me-1"></i> <?= date('F Y') ?></small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-body position-relative">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="stats-icon bg-warning bg-gradient rounded-3 p-3">
                            <i class="fas fa-money-bill-wave text-white fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted fw-normal mb-1">Pending Refunds</h6>
                        <h3 class="mb-0 fw-bold"><?= number_format($stats['pending_refunds']) ?></h3>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-warning bg-opacity-10 border-0 py-2">
                <small class="text-warning"><i class="fas fa-exclamation-circle me-1"></i> Needs attention</small>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-lg-5">
                <label class="form-label small text-muted fw-semibold">
                    <i class="fas fa-search me-1"></i> Search
                </label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Return #, Order #, or Customer name..."
                           value="<?= sanitize($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-lg-4">
                <label class="form-label small text-muted fw-semibold">
                    <i class="fas fa-filter me-1"></i> Return Reason
                </label>
                <select name="reason" class="form-select form-select-lg">
                    <option value="">All Reasons</option>
                    <?php foreach ($reasons as $key => $label): ?>
                    <option value="<?= $key ?>" <?= ($filters['reason'] ?? '') == $key ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                    <?php if (!empty($filters['search']) || !empty($filters['reason'])): ?>
                    <a href="<?= url('admin/returns') ?>" class="btn btn-outline-secondary btn-lg" title="Clear Filters">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (empty($returns['data'])): ?>
<!-- Empty State -->
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <div class="mb-4">
            <div class="empty-state-icon mx-auto mb-4">
                <i class="fas fa-box-open fa-4x text-muted opacity-50"></i>
            </div>
            <h4 class="text-dark mb-2">No Returns Found</h4>
            <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">
                <?php if (!empty($filters['search']) || !empty($filters['reason'])): ?>
                    No returns match your search criteria. Try adjusting your filters.
                <?php else: ?>
                    When products are returned by customers or couriers, record them here to restore inventory.
                <?php endif; ?>
            </p>
        </div>
        <div class="d-flex gap-2 justify-content-center">
            <?php if (!empty($filters['search']) || !empty($filters['reason'])): ?>
            <a href="<?= url('admin/returns') ?>" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-times me-2"></i> Clear Filters
            </a>
            <?php endif; ?>
            <a href="<?= url('admin/returns/create') ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-plus-circle me-2"></i> Record First Return
            </a>
        </div>
    </div>
</div>
<?php else: ?>

<!-- Returns Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4 py-3">Return Details</th>
                        <th class="border-0 py-3">Customer</th>
                        <th class="border-0 py-3">Reason</th>
                        <th class="border-0 text-center py-3">Items</th>
                        <th class="border-0 py-3">Refund Status</th>
                        <th class="border-0 py-3">Date</th>
                        <th class="border-0 text-end pe-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($returns['data'] as $return): ?>
                    <tr class="border-bottom">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="return-icon me-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="fas fa-undo text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <a href="<?= url('admin/returns/show/' . $return['id']) ?>" class="fw-bold text-decoration-none text-dark d-block">
                                        <?= sanitize($return['return_number']) ?>
                                    </a>
                                    <a href="<?= url('admin/orders/view/' . $return['order_id']) ?>" class="small text-muted text-decoration-none">
                                        <i class="fas fa-shopping-bag me-1"></i><?= sanitize($return['order_number']) ?>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="fw-medium"><?= sanitize($return['customer_name'] ?? 'Guest Customer') ?></span>
                        </td>
                        <td class="py-3">
                            <?php
                            $reasonColors = [
                                'defective' => 'danger',
                                'damaged' => 'danger',
                                'wrong_item' => 'warning',
                                'not_as_described' => 'warning',
                                'changed_mind' => 'info',
                                'customer_refused' => 'secondary',
                                'undelivered' => 'dark',
                                'other' => 'secondary'
                            ];
                            $color = $reasonColors[$return['reason']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> fw-medium px-3 py-2">
                                <?= ProductReturn::getReasonLabel($return['reason']) ?>
                            </span>
                        </td>
                        <td class="text-center py-3">
                            <span class="badge bg-light text-dark fw-bold px-3 py-2">
                                <?= $return['item_count'] ?> item<?= $return['item_count'] > 1 ? 's' : '' ?>
                            </span>
                        </td>
                        <td class="py-3">
                            <?php
                            $refundConfig = match($return['refund_status']) {
                                'not_required' => ['bg' => 'light', 'text' => 'secondary', 'icon' => 'minus-circle'],
                                'pending' => ['bg' => 'warning', 'text' => 'warning', 'icon' => 'clock'],
                                'completed' => ['bg' => 'success', 'text' => 'success', 'icon' => 'check-circle'],
                                default => ['bg' => 'secondary', 'text' => 'secondary', 'icon' => 'question-circle']
                            };
                            ?>
                            <span class="badge bg-<?= $refundConfig['bg'] ?> bg-opacity-10 text-<?= $refundConfig['text'] ?> fw-medium px-3 py-2">
                                <i class="fas fa-<?= $refundConfig['icon'] ?> me-1"></i>
                                <?= ucfirst(str_replace('_', ' ', $return['refund_status'])) ?>
                            </span>
                        </td>
                        <td class="py-3">
                            <div class="text-dark"><?= date('M d, Y', strtotime($return['created_at'])) ?></div>
                            <small class="text-muted"><?= date('h:i A', strtotime($return['created_at'])) ?></small>
                        </td>
                        <td class="text-end pe-4 py-3">
                            <div class="btn-group">
                                <a href="<?= url('admin/returns/show/' . $return['id']) ?>"
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (Auth::user()['role'] === 'admin'): ?>
                                <a href="<?= url('admin/returns/delete/' . $return['id']) ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   data-confirm="Are you sure you want to delete this return record? This action cannot be undone."
                                   title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($returns['total_pages'] > 1): ?>
    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="text-muted small">
                Showing <strong><?= count($returns['data']) ?></strong> of <strong><?= number_format($returns['total']) ?></strong> returns
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($returns['current_page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('admin/returns?page=' . ($returns['current_page'] - 1) .
                            (!empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '') .
                            (!empty($filters['reason']) ? '&reason=' . urlencode($filters['reason']) : '')) ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php
                    $startPage = max(1, $returns['current_page'] - 2);
                    $endPage = min($returns['total_pages'], $returns['current_page'] + 2);

                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                    <li class="page-item <?= $i == $returns['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('admin/returns?page=' . $i .
                            (!empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '') .
                            (!empty($filters['reason']) ? '&reason=' . urlencode($filters['reason']) : '')) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($returns['current_page'] < $returns['total_pages']): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('admin/returns?page=' . ($returns['current_page'] + 1) .
                            (!empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '') .
                            (!empty($filters['reason']) ? '&reason=' . urlencode($filters['reason']) : '')) ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<style>
.stats-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.empty-state-icon {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}
.table > :not(caption) > * > * {
    border-bottom-width: 0;
}
.table tbody tr {
    transition: all 0.2s ease;
}
.table tbody tr:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.03);
}
.btn-group .btn {
    border-radius: 6px !important;
    margin-left: 4px;
}
</style>
