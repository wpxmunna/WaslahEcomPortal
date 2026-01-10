<div class="page-header">
    <h1>Purchase Orders</h1>
    <a href="<?= url('admin/purchase-orders/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Create Purchase Order
    </a>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Orders</h6>
                        <h4 class="mb-0"><?= $stats['total_orders'] ?? 0 ?></h4>
                    </div>
                    <i class="fas fa-file-invoice fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-dark-50 mb-1">Pending</h6>
                        <h4 class="mb-0"><?= $stats['pending_orders'] ?? 0 ?></h4>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Value</h6>
                        <h4 class="mb-0"><?= formatPrice($stats['total_value'] ?? 0) ?></h4>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Unpaid</h6>
                        <h4 class="mb-0"><?= formatPrice($stats['unpaid_value'] ?? 0) ?></h4>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <input type="text" name="search" class="form-control" placeholder="Search PO#..." value="<?= sanitize($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <select name="supplier" class="form-select">
                    <option value="">All Suppliers</option>
                    <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['id'] ?>" <?= ($filters['supplier_id'] ?? '') == $supplier['id'] ? 'selected' : '' ?>>
                        <?= sanitize($supplier['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="ordered" <?= ($filters['status'] ?? '') === 'ordered' ? 'selected' : '' ?>>Ordered</option>
                    <option value="partial" <?= ($filters['status'] ?? '') === 'partial' ? 'selected' : '' ?>>Partial</option>
                    <option value="received" <?= ($filters['status'] ?? '') === 'received' ? 'selected' : '' ?>>Received</option>
                    <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment" class="form-select">
                    <option value="">All Payments</option>
                    <option value="pending" <?= ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="partial" <?= ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' ?>>Partial</option>
                    <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
            <?php if (!empty(array_filter($filters))): ?>
            <div class="col-md-2">
                <a href="<?= url('admin/purchase-orders') ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-1"></i> Clear
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Purchase Orders Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($purchaseOrders)): ?>
        <div class="text-center py-5">
            <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No purchase orders found</h5>
            <p class="text-muted">Create your first purchase order</p>
            <a href="<?= url('admin/purchase-orders/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Create Purchase Order
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th class="text-center">Items</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchaseOrders as $po): ?>
                    <?php
                    $statusColors = [
                        'draft' => 'secondary',
                        'pending' => 'warning',
                        'approved' => 'info',
                        'ordered' => 'primary',
                        'partial' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger'
                    ];
                    $paymentColors = [
                        'pending' => 'secondary',
                        'partial' => 'warning',
                        'paid' => 'success'
                    ];
                    ?>
                    <tr>
                        <td>
                            <a href="<?= url('admin/purchase-orders/view/' . $po['id']) ?>" class="fw-bold">
                                <?= sanitize($po['po_number']) ?>
                            </a>
                        </td>
                        <td>
                            <div><?= sanitize($po['supplier_name']) ?></div>
                            <?php if ($po['supplier_code']): ?>
                            <small class="text-muted"><?= sanitize($po['supplier_code']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div><?= date('M d, Y', strtotime($po['order_date'])) ?></div>
                            <?php if ($po['expected_date']): ?>
                            <small class="text-muted">Expected: <?= date('M d', strtotime($po['expected_date'])) ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><?= $po['item_count'] ?></span>
                        </td>
                        <td class="text-end">
                            <strong><?= formatPrice($po['total_amount']) ?></strong>
                        </td>
                        <td>
                            <span class="badge bg-<?= $statusColors[$po['status']] ?? 'secondary' ?>">
                                <?= ucfirst($po['status']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $paymentColors[$po['payment_status']] ?? 'secondary' ?>">
                                <?= ucfirst($po['payment_status']) ?>
                            </span>
                            <?php if ($po['paid_amount'] > 0 && $po['payment_status'] !== 'paid'): ?>
                            <br><small class="text-muted"><?= formatPrice($po['paid_amount']) ?> paid</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('admin/purchase-orders/view/' . $po['id']) ?>" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (in_array($po['status'], ['draft', 'pending'])): ?>
                                <a href="<?= url('admin/purchase-orders/edit/' . $po['id']) ?>" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (in_array($po['status'], ['approved', 'ordered', 'partial'])): ?>
                                <a href="<?= url('admin/purchase-orders/receive/' . $po['id']) ?>" class="btn btn-outline-success" title="Receive Stock">
                                    <i class="fas fa-boxes"></i>
                                </a>
                                <?php endif; ?>
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
