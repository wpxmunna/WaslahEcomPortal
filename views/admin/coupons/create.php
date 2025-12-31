<div class="page-header">
    <h1>Add Coupon</h1>
    <a href="<?= url('admin/coupons') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<form action="<?= url('admin/coupons/store') ?>" method="POST">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Coupon Details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Coupon Code *</label>
                            <input type="text" class="form-control text-uppercase" name="code" required
                                   placeholder="e.g. SUMMER20" maxlength="50">
                            <small class="text-muted">Customers will enter this code at checkout</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Discount Type *</label>
                            <select class="form-select" name="type" id="discountType" onchange="toggleDiscountFields()">
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage</option>
                                <option value="free_shipping">Free Shipping</option>
                                <option value="gift_item">Gift Item</option>
                                <option value="buy_x_get_y">Buy X Get Y Free</option>
                            </select>
                        </div>
                    </div>

                    <div class="row" id="valueFields">
                        <div class="col-md-6 mb-3" id="discountValueField">
                            <label class="form-label">Discount Value *</label>
                            <div class="input-group">
                                <span class="input-group-text" id="valuePrefix"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="value" id="valueInput" step="0.01" min="0">
                                <span class="input-group-text d-none" id="valueSuffix">%</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3" id="maxDiscountField" style="display: none;">
                            <label class="form-label">Maximum Discount</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="maximum_discount" step="0.01" min="0">
                            </div>
                            <small class="text-muted">Leave empty for no limit</small>
                        </div>
                    </div>

                    <!-- Gift Item Fields -->
                    <div class="row" id="giftItemFields" style="display: none;">
                        <div class="col-12 mb-3">
                            <label class="form-label">Select Gift Product *</label>
                            <select class="form-select" name="gift_product_id" id="giftProductId">
                                <option value="">-- Select a product --</option>
                                <?php foreach ($products ?? [] as $product): ?>
                                <option value="<?= $product['id'] ?>"><?= sanitize($product['name']) ?> (<?= formatPrice($product['price']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">This product will be added free to qualifying orders</small>
                        </div>
                    </div>

                    <!-- Buy X Get Y Fields -->
                    <div class="row" id="buyXGetYFields" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Buy Quantity *</label>
                            <input type="number" class="form-control" name="buy_quantity" min="1" placeholder="e.g. 2">
                            <small class="text-muted">Customer must buy this many items</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Get Free Quantity *</label>
                            <input type="number" class="form-control" name="get_quantity" min="1" placeholder="e.g. 1">
                            <small class="text-muted">Customer gets this many items free</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Order Amount</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="minimum_amount" step="0.01" min="0" value="0">
                            </div>
                            <small class="text-muted">Minimum cart total required to use this coupon</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Usage Limit</label>
                            <input type="number" class="form-control" name="usage_limit" min="1">
                            <small class="text-muted">Leave empty for unlimited usage</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Validity Period</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control" name="starts_at">
                            <small class="text-muted">Leave empty to start immediately</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="datetime-local" class="form-control" name="expires_at">
                            <small class="text-muted">Leave empty for no expiry</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Status</div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" checked>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                    <small class="text-muted">Inactive coupons cannot be used at checkout</small>
                </div>
            </div>

            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <h6><i class="fas fa-lightbulb text-warning me-2"></i> Tips</h6>
                    <ul class="small text-muted mb-0">
                        <li>Use memorable codes like SUMMER20, FLAT50</li>
                        <li>Set minimum amount to prevent abuse</li>
                        <li>For percentage discounts, set a maximum cap</li>
                        <li>Set expiry dates for promotional campaigns</li>
                    </ul>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-save me-2"></i> Create Coupon
            </button>
        </div>
    </div>
</form>

<script>
function toggleDiscountFields() {
    const type = document.getElementById('discountType').value;
    const discountValueField = document.getElementById('discountValueField');
    const maxField = document.getElementById('maxDiscountField');
    const valuePrefix = document.getElementById('valuePrefix');
    const valueSuffix = document.getElementById('valueSuffix');
    const valueInput = document.getElementById('valueInput');
    const giftItemFields = document.getElementById('giftItemFields');
    const buyXGetYFields = document.getElementById('buyXGetYFields');

    // Reset all fields
    discountValueField.style.display = 'block';
    maxField.style.display = 'none';
    giftItemFields.style.display = 'none';
    buyXGetYFields.style.display = 'none';
    valuePrefix.classList.remove('d-none');
    valueSuffix.classList.add('d-none');
    valueInput.required = true;

    switch (type) {
        case 'percentage':
            maxField.style.display = 'block';
            valuePrefix.classList.add('d-none');
            valueSuffix.classList.remove('d-none');
            break;

        case 'free_shipping':
            discountValueField.style.display = 'none';
            valueInput.required = false;
            valueInput.value = '0';
            break;

        case 'gift_item':
            discountValueField.style.display = 'none';
            valueInput.required = false;
            valueInput.value = '0';
            giftItemFields.style.display = 'flex';
            break;

        case 'buy_x_get_y':
            buyXGetYFields.style.display = 'flex';
            break;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', toggleDiscountFields);
</script>
