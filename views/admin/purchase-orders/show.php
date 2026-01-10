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

<div class="page-header">
    <div>
        <h1>
            <?= sanitize($po['po_number']) ?>
            <span class="badge bg-<?= $statusColors[$po['status']] ?? 'secondary' ?> ms-2"><?= ucfirst($po['status']) ?></span>
        </h1>
        <p class="text-muted mb-0">
            Created <?= date('M d, Y', strtotime($po['created_at'])) ?>
            <?php if ($po['created_by_name']): ?>by <?= sanitize($po['created_by_name']) ?><?php endif; ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <?php if (in_array($po['status'], ['draft', 'pending'])): ?>
        <a href="<?= url('admin/purchase-orders/edit/' . $po['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i> Edit
        </a>
        <?php endif; ?>
        <?php if (in_array($po['status'], ['approved', 'ordered', 'partial'])): ?>
        <a href="<?= url('admin/purchase-orders/receive/' . $po['id']) ?>" class="btn btn-success">
            <i class="fas fa-boxes me-2"></i> Receive Stock
        </a>
        <?php endif; ?>
        <a href="<?= url('admin/purchase-orders') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i> Order Items</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Ordered</th>
                                <th class="text-center">Received</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($po['items'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($item['product_image']): ?>
                                        <img src="<?= asset($item['product_image']) ?>" alt="" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold"><?= sanitize($item['product_name']) ?></div>
                                            <?php if ($item['product_sku']): ?>
                                            <small class="text-muted">SKU: <?= sanitize($item['product_sku']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><?= $item['quantity_ordered'] ?></td>
                                <td class="text-center">
                                    <?php if ($item['quantity_received'] >= $item['quantity_ordered']): ?>
                                    <span class="badge bg-success"><?= $item['quantity_received'] ?></span>
                                    <?php elseif ($item['quantity_received'] > 0): ?>
                                    <span class="badge bg-warning"><?= $item['quantity_received'] ?></span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end"><?= formatPrice($item['unit_cost']) ?></td>
                                <td class="text-end"><?= formatPrice($item['total_cost']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="4" class="text-end">Subtotal:</td>
                                <td class="text-end"><strong><?= formatPrice($po['subtotal']) ?></strong></td>
                            </tr>
                            <?php if ($po['tax_amount'] > 0): ?>
                            <tr>
                                <td colspan="4" class="text-end">Tax:</td>
                                <td class="text-end"><?= formatPrice($po['tax_amount']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($po['shipping_amount'] > 0): ?>
                            <tr>
                                <td colspan="4" class="text-end">Shipping:</td>
                                <td class="text-end"><?= formatPrice($po['shipping_amount']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($po['discount_amount'] > 0): ?>
                            <tr>
                                <td colspan="4" class="text-end">Discount:</td>
                                <td class="text-end text-danger">-<?= formatPrice($po['discount_amount']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong class="h5 mb-0"><?= formatPrice($po['total_amount']) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payments -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Payments</h5>
                <?php if ($po['payment_status'] !== 'paid' && $po['status'] !== 'cancelled'): ?>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="fas fa-plus me-1"></i> Add Payment
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($po['payments'])): ?>
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
                                <th>Method</th>
                                <th>Reference</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($po['payments'] as $payment): ?>
                            <tr>
                                <td><?= sanitize($payment['payment_number']) ?></td>
                                <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                                    </span>
                                </td>
                                <td><?= sanitize($payment['reference_number'] ?: '-') ?></td>
                                <td class="text-end text-success fw-bold"><?= formatPrice($payment['amount']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total Paid:</strong></td>
                                <td class="text-end text-success"><strong><?= formatPrice($po['paid_amount']) ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Balance Due:</strong></td>
                                <td class="text-end <?= ($po['total_amount'] - $po['paid_amount']) > 0 ? 'text-danger' : 'text-success' ?>">
                                    <strong><?= formatPrice($po['total_amount'] - $po['paid_amount']) ?></strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($po['notes']): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i> Notes</h5>
            </div>
            <div class="card-body">
                <?= nl2br(sanitize($po['notes'])) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Order Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Order Info</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Status</td>
                        <td>
                            <span class="badge bg-<?= $statusColors[$po['status']] ?? 'secondary' ?>">
                                <?= ucfirst($po['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Payment</td>
                        <td>
                            <span class="badge bg-<?= $paymentColors[$po['payment_status']] ?? 'secondary' ?>">
                                <?= ucfirst($po['payment_status']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Order Date</td>
                        <td><?= date('M d, Y', strtotime($po['order_date'])) ?></td>
                    </tr>
                    <?php if ($po['expected_date']): ?>
                    <tr>
                        <td class="text-muted">Expected</td>
                        <td><?= date('M d, Y', strtotime($po['expected_date'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($po['received_date']): ?>
                    <tr>
                        <td class="text-muted">Received</td>
                        <td><?= date('M d, Y', strtotime($po['received_date'])) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Supplier Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-truck-loading me-2"></i> Supplier</h5>
            </div>
            <div class="card-body">
                <h6 class="mb-2">
                    <a href="<?= url('admin/suppliers/view/' . $po['supplier_id']) ?>">
                        <?= sanitize($po['supplier_name']) ?>
                    </a>
                </h6>
                <?php if ($po['supplier_code']): ?>
                <p class="text-muted small mb-2"><?= sanitize($po['supplier_code']) ?></p>
                <?php endif; ?>

                <?php if ($po['contact_person']): ?>
                <div class="small"><i class="fas fa-user text-muted me-2"></i><?= sanitize($po['contact_person']) ?></div>
                <?php endif; ?>
                <?php if ($po['supplier_phone']): ?>
                <div class="small"><i class="fas fa-phone text-muted me-2"></i><?= sanitize($po['supplier_phone']) ?></div>
                <?php endif; ?>
                <?php if ($po['supplier_email']): ?>
                <div class="small"><i class="fas fa-envelope text-muted me-2"></i><?= sanitize($po['supplier_email']) ?></div>
                <?php endif; ?>
                <?php if ($po['supplier_address']): ?>
                <div class="small mt-2">
                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                    <?= sanitize($po['supplier_address']) ?><br>
                    <?= sanitize($po['supplier_city']) ?>, <?= sanitize($po['supplier_country']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (in_array($po['status'], ['draft', 'pending'])): ?>
                    <a href="<?= url('admin/purchase-orders/edit/' . $po['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i> Edit Order
                    </a>
                    <a href="<?= url('admin/purchase-orders/cancel/' . $po['id']) ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this order?')">
                        <i class="fas fa-times me-2"></i> Cancel Order
                    </a>
                    <?php endif; ?>

                    <?php if (in_array($po['status'], ['approved', 'ordered', 'partial'])): ?>
                    <a href="<?= url('admin/purchase-orders/receive/' . $po['id']) ?>" class="btn btn-success">
                        <i class="fas fa-boxes me-2"></i> Receive Stock
                    </a>
                    <?php endif; ?>

                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<?php if ($po['payment_status'] !== 'paid' && $po['status'] !== 'cancelled'): ?>
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('admin/purchase-orders/payment/' . $po['id']) ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between">
                            <span>Total Amount:</span>
                            <strong><?= formatPrice($po['total_amount']) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Already Paid:</span>
                            <strong><?= formatPrice($po['paid_amount']) ?></strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span>Balance Due:</span>
                            <strong><?= formatPrice($po['total_amount'] - $po['paid_amount']) ?></strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required min="0.01" step="0.01" value="<?= $po['total_amount'] - $po['paid_amount'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="Transaction ID, Check #, etc.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
