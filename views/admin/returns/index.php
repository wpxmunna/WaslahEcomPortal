<div class="page-header">
    <h1>Product Returns</h1>
    <a href="<?= url('admin/returns/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Record Return
    </a>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Returns</h6>
                        <h3 class="mb-0"><?= $stats['total_returns'] ?></h3>
                    </div>
                    <i class="fas fa-undo fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Today</h6>
                        <h3 class="mb-0"><?= $stats['today_returns'] ?></h3>
                    </div>
                    <i class="fas fa-calendar-day fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">This Month</h6>
                        <h3 class="mb-0"><?= $stats['month_returns'] ?></h3>
                    </div>
                    <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Pending Refunds</h6>
                        <h3 class="mb-0"><?= $stats['pending_refunds'] ?></h3>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                       placeholder="Search by return#, order#, or customer..."
                       value="<?= sanitize($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select name="reason" class="form-select">
                    <option value="">All Reasons</option>
                    <?php foreach ($reasons as $key => $label): ?>
                    <option value="<?= $key ?>" <?= ($filters['reason'] ?? '') == $key ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
            </div>
            <?php if (!empty($filters['search']) || !empty($filters['reason'])): ?>
            <div class="col-md-2">
                <a href="<?= url('admin/returns') ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-1"></i> Clear
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (empty($returns['data'])): ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-undo fa-3x text-muted mb-3"></i>
        <h4>No Returns Yet</h4>
        <p class="text-muted">When products are returned, record them here.</p>
        <a href="<?= url('admin/returns/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Record Return
        </a>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Return #</th>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Reason</th>
                    <th class="text-center">Items</th>
                    <th>Refund Status</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($returns['data'] as $return): ?>
                <tr>
                    <td>
                        <a href="<?= url('admin/returns/show/' . $return['id']) ?>" class="fw-bold text-primary">
                            <?= sanitize($return['return_number']) ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= url('admin/orders/show/' . $return['order_id']) ?>" class="text-muted">
                            <?= sanitize($return['order_number']) ?>
                        </a>
                    </td>
                    <td><?= sanitize($return['customer_name'] ?? 'Guest') ?></td>
                    <td>
                        <span class="badge bg-secondary">
                            <?= ProductReturn::getReasonLabel($return['reason']) ?>
                        </span>
                    </td>
                    <td class="text-center"><?= $return['item_count'] ?></td>
                    <td>
                        <?php
                        $refundBadge = match($return['refund_status']) {
                            'not_required' => 'bg-light text-dark',
                            'pending' => 'bg-warning text-dark',
                            'completed' => 'bg-success',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $refundBadge ?>">
                            <?= ucfirst(str_replace('_', ' ', $return['refund_status'])) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($return['created_at'])) ?></td>
                    <td class="text-end">
                        <a href="<?= url('admin/returns/show/' . $return['id']) ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if (Auth::user()['role'] === 'admin'): ?>
                        <a href="<?= url('admin/returns/delete/' . $return['id']) ?>"
                           class="btn btn-sm btn-outline-danger"
                           data-confirm="Are you sure you want to delete this return record?">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($returns['total_pages'] > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $returns['total_pages']; $i++): ?>
        <li class="page-item <?= $i == $returns['current_page'] ? 'active' : '' ?>">
            <a class="page-link" href="<?= url('admin/returns?page=' . $i .
                (!empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '') .
                (!empty($filters['reason']) ? '&reason=' . urlencode($filters['reason']) : '')) ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
<?php endif; ?>
