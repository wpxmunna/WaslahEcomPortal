<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/returns') ?>" class="text-decoration-none">Returns</a></li>
                <li class="breadcrumb-item active">Record Return</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">Record Product Return</h1>
    </div>
    <a href="<?= url('admin/returns') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Returns
    </a>
</div>

<form action="<?= url('admin/returns/store') ?>" method="POST" id="returnForm">
    <?= csrfField() ?>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Order Selection Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex align-items-center">
                        <div class="step-number bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600;">1</div>
                        <div>
                            <h5 class="mb-0">Select Order</h5>
                            <small class="text-muted">Choose the order being returned</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-inbox fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-dark">No Eligible Orders</h5>
                        <p class="text-muted mb-4">
                            Only delivered or shipped orders that haven't been returned can be selected.<br>
                            Make sure orders are marked as delivered before processing returns.
                        </p>
                        <a href="<?= url('admin/orders') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-cart me-2"></i> View Orders
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Select Order <span class="text-danger">*</span></label>
                        <select name="order_id" class="form-select form-select-lg" required id="orderSelect">
                            <option value="">-- Choose an order --</option>
                            <?php foreach ($orders as $order): ?>
                            <option value="<?= $order['id'] ?>"
                                    data-total="<?= formatPrice($order['total_amount']) ?>"
                                    data-customer="<?= sanitize($order['customer_name'] ?? 'Guest') ?>"
                                    data-status="<?= ucfirst($order['status']) ?>"
                                    data-date="<?= date('M d, Y', strtotime($order['created_at'])) ?>">
                                <?= $order['order_number'] ?> — <?= sanitize($order['customer_name'] ?? 'Guest') ?> — <?= formatPrice($order['total_amount']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Selected Order Preview -->
                    <div id="orderPreview" class="d-none">
                        <div class="bg-light rounded-3 p-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="text-muted small mb-1">Customer</div>
                                    <div class="fw-semibold" id="previewCustomer">-</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small mb-1">Order Total</div>
                                    <div class="fw-semibold text-primary" id="previewTotal">-</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small mb-1">Status</div>
                                    <div id="previewStatus">-</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small mb-1">Order Date</div>
                                    <div class="fw-semibold" id="previewDate">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Return Details Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex align-items-center">
                        <div class="step-number bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600;">2</div>
                        <div>
                            <h5 class="mb-0">Return Details</h5>
                            <small class="text-muted">Specify the reason for this return</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Return Reason <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <?php
                            $reasonIcons = [
                                'defective' => ['icon' => 'bug', 'color' => 'danger'],
                                'damaged' => ['icon' => 'box-open', 'color' => 'danger'],
                                'wrong_item' => ['icon' => 'exchange-alt', 'color' => 'warning'],
                                'not_as_described' => ['icon' => 'file-alt', 'color' => 'warning'],
                                'changed_mind' => ['icon' => 'brain', 'color' => 'info'],
                                'customer_refused' => ['icon' => 'hand-paper', 'color' => 'secondary'],
                                'undelivered' => ['icon' => 'truck', 'color' => 'dark'],
                                'other' => ['icon' => 'question-circle', 'color' => 'secondary']
                            ];
                            foreach ($reasons as $key => $label):
                                $config = $reasonIcons[$key] ?? ['icon' => 'tag', 'color' => 'secondary'];
                            ?>
                            <div class="col-md-6">
                                <div class="form-check reason-option">
                                    <input class="form-check-input" type="radio" name="reason" value="<?= $key ?>" id="reason_<?= $key ?>" required>
                                    <label class="form-check-label reason-label" for="reason_<?= $key ?>">
                                        <div class="d-flex align-items-center">
                                            <div class="reason-icon bg-<?= $config['color'] ?> bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-<?= $config['icon'] ?> text-<?= $config['color'] ?>"></i>
                                            </div>
                                            <span class="fw-medium"><?= $label ?></span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Additional Details <span class="text-muted fw-normal">(Optional)</span></label>
                        <textarea name="reason_details" class="form-control" rows="4"
                                  placeholder="Provide any additional context about this return..."></textarea>
                        <div class="form-text">Add specifics like condition of items, courier notes, etc.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Admin Notes Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0"><i class="fas fa-sticky-note text-muted me-2"></i> Admin Notes</h5>
                </div>
                <div class="card-body">
                    <textarea name="admin_notes" class="form-control" rows="4"
                              placeholder="Internal notes for this return (not visible to customer)..."></textarea>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10 mb-4">
                <div class="card-body">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-magic me-2"></i> What happens next?
                    </h6>
                    <ul class="mb-0 ps-3">
                        <li class="mb-2"><span class="text-dark">All items from the order will be marked as returned</span></li>
                        <li class="mb-2"><span class="text-dark">Stock quantities will be <strong>automatically restored</strong></span></li>
                        <li class="mb-2"><span class="text-dark">Order status will change to "Returned"</span></li>
                        <li class="mb-0"><span class="text-dark">Refund tracking based on payment method</span></li>
                    </ul>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3" <?= empty($orders) ? 'disabled' : '' ?> id="submitBtn">
                        <i class="fas fa-check-circle me-2"></i> Record Return
                    </button>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-lock me-1"></i> This action will restore inventory
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.reason-option {
    padding: 0;
}
.reason-option .form-check-input {
    display: none;
}
.reason-option .reason-label {
    display: block;
    padding: 16px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
}
.reason-option .reason-label:hover {
    border-color: #dee2e6;
    background: #f8f9fa;
}
.reason-option .form-check-input:checked + .reason-label {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.05);
    box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.1);
}
.step-number {
    font-size: 14px;
}
#orderPreview {
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderSelect = document.getElementById('orderSelect');
    const orderPreview = document.getElementById('orderPreview');
    const submitBtn = document.getElementById('submitBtn');

    if (orderSelect) {
        orderSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];

            if (this.value) {
                document.getElementById('previewCustomer').textContent = selected.dataset.customer;
                document.getElementById('previewTotal').textContent = selected.dataset.total;
                document.getElementById('previewStatus').innerHTML = '<span class="badge bg-success">' + selected.dataset.status + '</span>';
                document.getElementById('previewDate').textContent = selected.dataset.date;
                orderPreview.classList.remove('d-none');
            } else {
                orderPreview.classList.add('d-none');
            }
        });
    }

    // Form validation feedback
    const form = document.getElementById('returnForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!orderSelect.value) {
                e.preventDefault();
                orderSelect.focus();
                orderSelect.classList.add('is-invalid');
                return;
            }

            const reasonSelected = document.querySelector('input[name="reason"]:checked');
            if (!reasonSelected) {
                e.preventDefault();
                document.querySelector('.reason-label').scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
        });
    }
});
</script>
