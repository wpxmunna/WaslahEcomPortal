<div class="page-header">
    <h1>Return #<?= sanitize($return['return_number']) ?></h1>
    <a href="<?= url('admin/returns') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Returns
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Return Info -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Return Information</h5>
                <span class="badge bg-primary"><?= date('M d, Y H:i', strtotime($return['returned_at'])) ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Return Number</label>
                        <div class="fw-bold"><?= sanitize($return['return_number']) ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Order Number</label>
                        <div>
                            <a href="<?= url('admin/orders/show/' . $return['order_id']) ?>" class="fw-bold text-primary">
                                <?= sanitize($return['order']['order_number']) ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Reason</label>
                        <div>
                            <span class="badge bg-secondary">
                                <?= ProductReturn::getReasonLabel($return['reason']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Refund Status</label>
                        <div>
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
                            <?php if ($return['refund_amount'] > 0): ?>
                            <span class="ms-2 fw-bold"><?= formatPrice($return['refund_amount']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($return['reason_details'])): ?>
                <div class="mb-3">
                    <label class="text-muted small">Additional Details</label>
                    <div class="p-3 bg-light rounded"><?= nl2br(sanitize($return['reason_details'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Returned Items -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Returned Items</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Variant</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalAmount = 0;
                        foreach ($return['items'] as $item):
                            $itemTotal = $item['quantity'] * $item['unit_price'];
                            $totalAmount += $itemTotal;
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($item['image'])): ?>
                                    <img src="<?= upload($item['image']) ?>" class="rounded me-3"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center me-3"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= sanitize($item['product_name']) ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td><?= $item['variant_info'] ?: '-' ?></td>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                            <td class="text-end"><?= formatPrice($item['unit_price']) ?></td>
                            <td class="text-end"><?= formatPrice($itemTotal) ?></td>
                            <td class="text-center">
                                <?php if ($item['stock_restored']): ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i> Restored
                                </span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="text-end fw-bold">Total:</td>
                            <td class="text-end fw-bold"><?= formatPrice($totalAmount) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Customer Info -->
        <?php if ($return['order']): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Name</label>
                        <div class="fw-bold"><?= sanitize($return['order']['customer_name'] ?? 'Guest') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Email</label>
                        <div><?= sanitize($return['order']['customer_email'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Phone</label>
                        <div><?= sanitize($return['order']['customer_phone'] ?? $return['order']['shipping_phone'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Refund Update (if applicable) -->
        <?php if ($return['refund_status'] !== 'not_required'): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Update Refund Status</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('admin/returns/updateRefund/' . $return['id']) ?>" method="POST">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label">Refund Amount</label>
                        <div class="form-control-plaintext fw-bold">
                            <?= formatPrice($return['refund_amount']) ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="refund_status" class="form-select">
                            <option value="pending" <?= $return['refund_status'] == 'pending' ? 'selected' : '' ?>>
                                Pending
                            </option>
                            <option value="completed" <?= $return['refund_status'] == 'completed' ? 'selected' : '' ?>>
                                Completed
                            </option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Update Refund
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Admin Notes -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Admin Notes</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('admin/returns/saveNotes/' . $return['id']) ?>" method="POST">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <textarea name="admin_notes" class="form-control" rows="4"
                                  placeholder="Internal notes..."><?= sanitize($return['admin_notes'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-save me-2"></i> Save Notes
                    </button>
                </form>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <a href="<?= url('admin/orders/show/' . $return['order_id']) ?>" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fas fa-shopping-cart me-2"></i> View Original Order
                </a>
                <?php if (Auth::user()['role'] === 'admin'): ?>
                <a href="<?= url('admin/returns/delete/' . $return['id']) ?>"
                   class="btn btn-outline-danger w-100"
                   data-confirm="Are you sure you want to delete this return record? This will NOT reverse stock changes.">
                    <i class="fas fa-trash me-2"></i> Delete Return
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
