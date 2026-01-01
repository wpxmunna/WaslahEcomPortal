<div class="page-header">
    <h1>Record Return</h1>
    <a href="<?= url('admin/returns') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Returns
    </a>
</div>

<form action="<?= url('admin/returns/store') ?>" method="POST">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Selection -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Select Order</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        No eligible orders for return. Orders must be delivered/shipped and not already returned.
                    </div>
                    <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label">Order *</label>
                        <select name="order_id" class="form-select" required id="orderSelect">
                            <option value="">-- Select Order --</option>
                            <?php foreach ($orders as $order): ?>
                            <option value="<?= $order['id'] ?>"
                                    data-total="<?= formatPrice($order['total_amount']) ?>"
                                    data-customer="<?= sanitize($order['customer_name'] ?? 'Guest') ?>"
                                    data-status="<?= $order['status'] ?>">
                                <?= $order['order_number'] ?> -
                                <?= sanitize($order['customer_name'] ?? 'Guest') ?> -
                                <?= formatPrice($order['total_amount']) ?> -
                                <?= date('M d, Y', strtotime($order['created_at'])) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Order Info (shown when order is selected) -->
                    <div id="orderInfo" class="alert alert-light d-none">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Customer:</strong> <span id="infoCustomer"></span>
                            </div>
                            <div class="col-md-4">
                                <strong>Total:</strong> <span id="infoTotal"></span>
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong> <span id="infoStatus"></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Return Reason -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Return Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Reason *</label>
                        <select name="reason" class="form-select" required>
                            <option value="">-- Select Reason --</option>
                            <?php foreach ($reasons as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Details</label>
                        <textarea name="reason_details" class="form-control" rows="3"
                                  placeholder="Enter any additional details about the return..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Notes & Submit -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Admin Notes</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <textarea name="admin_notes" class="form-control" rows="4"
                                  placeholder="Internal notes (not visible to customer)..."></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100" <?= empty($orders) ? 'disabled' : '' ?>>
                        <i class="fas fa-save me-2"></i> Record Return
                    </button>
                </div>
            </div>

            <!-- Info Box -->
            <div class="card bg-light">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle text-info me-2"></i> What happens when you record a return:</h6>
                    <ul class="mb-0 small">
                        <li>All items from the order will be marked as returned</li>
                        <li>Stock quantities will be automatically restored</li>
                        <li>Order status will be updated to "Returned"</li>
                        <li>Refund tracking will be set based on payment method</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderSelect = document.getElementById('orderSelect');
    const orderInfo = document.getElementById('orderInfo');

    if (orderSelect) {
        orderSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];

            if (this.value) {
                document.getElementById('infoCustomer').textContent = selected.dataset.customer;
                document.getElementById('infoTotal').textContent = selected.dataset.total;
                document.getElementById('infoStatus').textContent = selected.dataset.status;
                orderInfo.classList.remove('d-none');
            } else {
                orderInfo.classList.add('d-none');
            }
        });
    }
});
</script>
