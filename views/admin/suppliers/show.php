<div class="page-header">
    <div>
        <h1><?= sanitize($supplier['name']) ?></h1>
        <p class="text-muted mb-0">
            <?php if ($supplier['code']): ?>
            <span class="badge bg-secondary"><?= sanitize($supplier['code']) ?></span>
            <?php endif; ?>
            <?php if ($supplier['city']): ?>
            <i class="fas fa-map-marker-alt ms-2 me-1"></i> <?= sanitize($supplier['city']) ?>, <?= sanitize($supplier['country']) ?>
            <?php endif; ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/suppliers/edit/' . $supplier['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i> Edit
        </a>
        <a href="<?= url('admin/suppliers') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Purchases</h6>
                        <h3 class="mb-0"><?= formatPrice($supplier['total_purchases']) ?></h3>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Paid</h6>
                        <h3 class="mb-0"><?= formatPrice($supplier['total_paid']) ?></h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card <?= $supplier['balance_due'] > 0 ? 'bg-danger' : 'bg-secondary' ?> text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Balance Due</h6>
                        <h3 class="mb-0"><?= formatPrice($supplier['balance_due']) ?></h3>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Orders</h6>
                        <h3 class="mb-0"><?= $supplier['order_count'] ?? 0 ?></h3>
                    </div>
                    <i class="fas fa-file-invoice fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Supplier Details -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Supplier Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Status</td>
                        <td>
                            <?php if ($supplier['status'] === 'active'): ?>
                            <span class="badge bg-success">Active</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Payment Terms</td>
                        <td>
                            <?php if ($supplier['payment_terms'] == 0): ?>
                            Due on Receipt
                            <?php else: ?>
                            Net <?= $supplier['payment_terms'] ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if ($supplier['contact_person']): ?>
                    <tr>
                        <td class="text-muted">Contact Person</td>
                        <td><?= sanitize($supplier['contact_person']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($supplier['phone']): ?>
                    <tr>
                        <td class="text-muted">Phone</td>
                        <td>
                            <a href="tel:<?= sanitize($supplier['phone']) ?>"><?= sanitize($supplier['phone']) ?></a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($supplier['email']): ?>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>
                            <a href="mailto:<?= sanitize($supplier['email']) ?>"><?= sanitize($supplier['email']) ?></a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($supplier['address']): ?>
                    <tr>
                        <td class="text-muted">Address</td>
                        <td><?= nl2br(sanitize($supplier['address'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="text-muted">Added On</td>
                        <td><?= date('M d, Y', strtotime($supplier['created_at'])) ?></td>
                    </tr>
                </table>

                <?php if ($supplier['notes']): ?>
                <hr>
                <h6 class="text-muted">Notes</h6>
                <p class="mb-0"><?= nl2br(sanitize($supplier['notes'])) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('admin/purchase-orders/create?supplier=' . $supplier['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> New Purchase Order
                    </a>
                    <a href="<?= url('admin/suppliers/edit/' . $supplier['id']) ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-edit me-2"></i> Edit Supplier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Orders & Payments -->
    <div class="col-lg-8">
        <!-- Purchase Orders -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i> Recent Purchase Orders</h5>
                <a href="<?= url('admin/purchase-orders?supplier=' . $supplier['id']) ?>" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($purchaseOrders)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No purchase orders yet</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>PO Number</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchaseOrders as $po): ?>
                            <tr>
                                <td>
                                    <a href="<?= url('admin/purchase-orders/view/' . $po['id']) ?>">
                                        <?= sanitize($po['po_number']) ?>
                                    </a>
                                </td>
                                <td><?= date('M d, Y', strtotime($po['order_date'])) ?></td>
                                <td>
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
                                    ?>
                                    <span class="badge bg-<?= $statusColors[$po['status']] ?? 'secondary' ?>">
                                        <?= ucfirst($po['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end"><?= formatPrice($po['total_amount']) ?></td>
                                <td>
                                    <?php if ($po['payment_status'] === 'paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                    <?php elseif ($po['payment_status'] === 'partial'): ?>
                                    <span class="badge bg-warning">Partial</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Pending</span>
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

        <!-- Recent Payments -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Recent Payments</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($payments)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No payments recorded yet</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Payment #</th>
                                <th>Date</th>
                                <th>PO Reference</th>
                                <th>Method</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= sanitize($payment['payment_number']) ?></td>
                                <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                <td>
                                    <?php if ($payment['po_number']): ?>
                                    <a href="<?= url('admin/purchase-orders/view/' . $payment['purchase_order_id']) ?>">
                                        <?= sanitize($payment['po_number']) ?>
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                                    </span>
                                </td>
                                <td class="text-end text-success fw-bold">
                                    <?= formatPrice($payment['amount']) ?>
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
