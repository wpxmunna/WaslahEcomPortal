<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/returns') ?>" class="text-decoration-none">Returns</a></li>
                <li class="breadcrumb-item active"><?= sanitize($return['return_number']) ?></li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-3">
            <h1 class="h3 mb-0">Return Details</h1>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fs-6">
                <?= sanitize($return['return_number']) ?>
            </span>
        </div>
    </div>
    <a href="<?= url('admin/returns') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Returns
    </a>
</div>

<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Return Overview Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-6 p-4 border-end">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-undo text-primary fa-lg"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <span class="text-muted small d-block mb-1">Return Number</span>
                                <span class="fw-bold fs-5"><?= sanitize($return['return_number']) ?></span>
                                <div class="mt-2">
                                    <span class="text-muted small">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= date('F d, Y \a\t h:i A', strtotime($return['returned_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 p-4">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-shopping-bag text-info fa-lg"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <span class="text-muted small d-block mb-1">Original Order</span>
                                <a href="<?= url('admin/orders/view/' . $return['order_id']) ?>" class="fw-bold fs-5 text-decoration-none">
                                    <?= sanitize($return['order']['order_number']) ?>
                                </a>
                                <div class="mt-2">
                                    <span class="text-muted small">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?= date('M d, Y', strtotime($return['order']['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Reason Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0"><i class="fas fa-question-circle text-muted me-2"></i> Return Reason</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <?php
                    $reasonConfig = [
                        'defective' => ['icon' => 'bug', 'color' => 'danger', 'bg' => 'danger'],
                        'damaged' => ['icon' => 'box-open', 'color' => 'danger', 'bg' => 'danger'],
                        'wrong_item' => ['icon' => 'exchange-alt', 'color' => 'warning', 'bg' => 'warning'],
                        'not_as_described' => ['icon' => 'file-alt', 'color' => 'warning', 'bg' => 'warning'],
                        'changed_mind' => ['icon' => 'brain', 'color' => 'info', 'bg' => 'info'],
                        'customer_refused' => ['icon' => 'hand-paper', 'color' => 'secondary', 'bg' => 'secondary'],
                        'undelivered' => ['icon' => 'truck', 'color' => 'dark', 'bg' => 'dark'],
                        'other' => ['icon' => 'question-circle', 'color' => 'secondary', 'bg' => 'secondary']
                    ];
                    $config = $reasonConfig[$return['reason']] ?? ['icon' => 'tag', 'color' => 'secondary', 'bg' => 'secondary'];
                    ?>
                    <div class="bg-<?= $config['bg'] ?> bg-opacity-10 rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-<?= $config['icon'] ?> text-<?= $config['color'] ?> fa-lg"></i>
                    </div>
                    <div>
                        <span class="badge bg-<?= $config['bg'] ?> bg-opacity-10 text-<?= $config['color'] ?> px-3 py-2 fs-6">
                            <?= ProductReturn::getReasonLabel($return['reason']) ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($return['reason_details'])): ?>
                <div class="bg-light rounded-3 p-3 mt-3">
                    <span class="text-muted small d-block mb-2">Additional Details</span>
                    <p class="mb-0"><?= nl2br(sanitize($return['reason_details'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Returned Items Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-box text-muted me-2"></i> Returned Items</h5>
                <span class="badge bg-light text-dark px-3 py-2"><?= count($return['items']) ?> item<?= count($return['items']) > 1 ? 's' : '' ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4 py-3">Product</th>
                                <th class="border-0 py-3">Variant</th>
                                <th class="border-0 text-center py-3">Qty</th>
                                <th class="border-0 text-end py-3">Unit Price</th>
                                <th class="border-0 text-end py-3">Subtotal</th>
                                <th class="border-0 text-center pe-4 py-3">Stock</th>
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
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($item['image'])): ?>
                                        <img src="<?= upload($item['image']) ?>" class="rounded-3 me-3 shadow-sm"
                                             style="width: 55px; height: 55px; object-fit: cover;">
                                        <?php else: ?>
                                        <div class="rounded-3 bg-light d-flex align-items-center justify-content-center me-3"
                                             style="width: 55px; height: 55px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <span class="fw-semibold d-block"><?= sanitize($item['product_name']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <?php if ($item['variant_info']): ?>
                                    <span class="badge bg-light text-dark"><?= sanitize($item['variant_info']) ?></span>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center py-3">
                                    <span class="fw-semibold"><?= $item['quantity'] ?></span>
                                </td>
                                <td class="text-end py-3">
                                    <?= formatPrice($item['unit_price']) ?>
                                </td>
                                <td class="text-end py-3 fw-semibold">
                                    <?= formatPrice($itemTotal) ?>
                                </td>
                                <td class="text-center pe-4 py-3">
                                    <?php if ($item['stock_restored']): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i> Restored
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="4" class="text-end fw-bold py-3 ps-4">Total Return Value:</td>
                                <td class="text-end fw-bold text-primary fs-5 py-3"><?= formatPrice($totalAmount) ?></td>
                                <td class="pe-4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Info Card -->
        <?php if ($return['order']): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0"><i class="fas fa-user text-muted me-2"></i> Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-user text-secondary"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Name</span>
                                <span class="fw-semibold"><?= sanitize($return['order']['customer_name'] ?? 'Guest Customer') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-envelope text-secondary"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Email</span>
                                <span class="fw-semibold"><?= sanitize($return['order']['customer_email'] ?? '-') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-phone text-secondary"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Phone</span>
                                <span class="fw-semibold"><?= sanitize($return['order']['customer_phone'] ?? $return['order']['shipping_phone'] ?? '-') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Refund Status Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave text-muted me-2"></i> Refund Status</h5>
            </div>
            <div class="card-body">
                <?php
                $refundConfig = match($return['refund_status']) {
                    'not_required' => ['bg' => 'light', 'text' => 'secondary', 'icon' => 'minus-circle', 'label' => 'Not Required'],
                    'pending' => ['bg' => 'warning', 'text' => 'warning', 'icon' => 'clock', 'label' => 'Pending'],
                    'completed' => ['bg' => 'success', 'text' => 'success', 'icon' => 'check-circle', 'label' => 'Completed'],
                    default => ['bg' => 'secondary', 'text' => 'secondary', 'icon' => 'question-circle', 'label' => 'Unknown']
                };
                ?>
                <div class="text-center mb-4">
                    <div class="bg-<?= $refundConfig['bg'] ?> bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-<?= $refundConfig['icon'] ?> text-<?= $refundConfig['text'] ?> fa-2x"></i>
                    </div>
                    <span class="badge bg-<?= $refundConfig['bg'] ?> <?= $refundConfig['bg'] !== 'light' ? '' : 'text-dark' ?> px-4 py-2 fs-6">
                        <?= $refundConfig['label'] ?>
                    </span>
                    <?php if ($return['refund_amount'] > 0): ?>
                    <div class="mt-3">
                        <span class="text-muted small d-block">Refund Amount</span>
                        <span class="fw-bold text-primary fs-4"><?= formatPrice($return['refund_amount']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($return['refund_status'] !== 'not_required'): ?>
                <form action="<?= url('admin/returns/updateRefund/' . $return['id']) ?>" method="POST">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Update Status</label>
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
                        <i class="fas fa-save me-2"></i> Update Refund Status
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Admin Notes Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0"><i class="fas fa-sticky-note text-muted me-2"></i> Admin Notes</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('admin/returns/saveNotes/' . $return['id']) ?>" method="POST">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <textarea name="admin_notes" class="form-control" rows="4"
                                  placeholder="Add internal notes..."><?= sanitize($return['admin_notes'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-save me-2"></i> Save Notes
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0"><i class="fas fa-bolt text-muted me-2"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="<?= url('admin/orders/view/' . $return['order_id']) ?>" class="btn btn-outline-primary w-100 mb-3">
                    <i class="fas fa-shopping-cart me-2"></i> View Original Order
                </a>
                <a href="<?= url('admin/returns/create') ?>" class="btn btn-outline-secondary w-100 mb-3">
                    <i class="fas fa-plus me-2"></i> Record Another Return
                </a>
                <?php if (Auth::user()['role'] === 'admin'): ?>
                <hr class="my-3">
                <a href="<?= url('admin/returns/delete/' . $return['id']) ?>"
                   class="btn btn-outline-danger w-100"
                   data-confirm="Are you sure you want to delete this return record? This action cannot be undone and will NOT reverse stock changes.">
                    <i class="fas fa-trash me-2"></i> Delete Return Record
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.table > :not(caption) > * > * {
    border-bottom-width: 0;
}
.card {
    transition: all 0.2s ease;
}
</style>
