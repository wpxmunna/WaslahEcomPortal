<div class="page-header">
    <h1>Edit Coupon</h1>
    <a href="<?= url('admin/coupons') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<form action="<?= url('admin/coupons/update/' . $coupon['id']) ?>" method="POST">
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
                                   value="<?= sanitize($coupon['code']) ?>" maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Discount Type *</label>
                            <select class="form-select" name="type" id="discountType" onchange="toggleDiscountFields()">
                                <option value="fixed" <?= $coupon['type'] === 'fixed' ? 'selected' : '' ?>>Fixed Amount</option>
                                <option value="percentage" <?= $coupon['type'] === 'percentage' ? 'selected' : '' ?>>Percentage</option>
                                <option value="free_shipping" <?= $coupon['type'] === 'free_shipping' ? 'selected' : '' ?>>Free Shipping</option>
                                <option value="gift_item" <?= $coupon['type'] === 'gift_item' ? 'selected' : '' ?>>Gift Item</option>
                                <option value="buy_x_get_y" <?= $coupon['type'] === 'buy_x_get_y' ? 'selected' : '' ?>>Buy X Get Y Free</option>
                            </select>
                        </div>
                    </div>

                    <div class="row" id="valueFields">
                        <div class="col-md-6 mb-3" id="discountValueField" style="<?= in_array($coupon['type'], ['free_shipping', 'gift_item']) ? 'display: none;' : '' ?>">
                            <label class="form-label">Discount Value *</label>
                            <div class="input-group">
                                <span class="input-group-text <?= $coupon['type'] === 'percentage' ? 'd-none' : '' ?>" id="valuePrefix"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="value" id="valueInput" step="0.01" min="0"
                                       value="<?= $coupon['value'] ?>">
                                <span class="input-group-text <?= $coupon['type'] === 'percentage' ? '' : 'd-none' ?>" id="valueSuffix">%</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3" id="maxDiscountField" style="<?= $coupon['type'] === 'percentage' ? '' : 'display: none;' ?>">
                            <label class="form-label">Maximum Discount</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="maximum_discount" step="0.01" min="0"
                                       value="<?= $coupon['maximum_discount'] ?>">
                            </div>
                            <small class="text-muted">Leave empty for no limit</small>
                        </div>
                    </div>

                    <!-- Gift Item Fields -->
                    <div class="row" id="giftItemFields" style="<?= $coupon['type'] === 'gift_item' ? '' : 'display: none;' ?>">
                        <div class="col-12 mb-3">
                            <label class="form-label">Select Gift Product *</label>
                            <select class="form-select" name="gift_product_id" id="giftProductId">
                                <option value="">-- Select a product --</option>
                                <?php foreach ($products ?? [] as $product): ?>
                                <option value="<?= $product['id'] ?>" <?= ($coupon['gift_product_id'] ?? '') == $product['id'] ? 'selected' : '' ?>>
                                    <?= sanitize($product['name']) ?> (<?= formatPrice($product['price']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">This product will be added free to qualifying orders</small>
                        </div>
                    </div>

                    <!-- Buy X Get Y Fields -->
                    <div class="row" id="buyXGetYFields" style="<?= $coupon['type'] === 'buy_x_get_y' ? '' : 'display: none;' ?>">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Buy Quantity *</label>
                            <input type="number" class="form-control" name="buy_quantity" min="1"
                                   value="<?= $coupon['buy_quantity'] ?? '' ?>" placeholder="e.g. 2">
                            <small class="text-muted">Customer must buy this many items</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Get Free Quantity *</label>
                            <input type="number" class="form-control" name="get_quantity" min="1"
                                   value="<?= $coupon['get_quantity'] ?? '' ?>" placeholder="e.g. 1">
                            <small class="text-muted">Customer gets this many items free</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Order Amount</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="minimum_amount" step="0.01" min="0"
                                       value="<?= $coupon['minimum_amount'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Usage Limit</label>
                            <input type="number" class="form-control" name="usage_limit" min="1"
                                   value="<?= $coupon['usage_limit'] ?>">
                            <small class="text-muted">Leave empty for unlimited</small>
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
                            <input type="datetime-local" class="form-control" name="starts_at"
                                   value="<?= $coupon['starts_at'] ? date('Y-m-d\TH:i', strtotime($coupon['starts_at'])) : '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="datetime-local" class="form-control" name="expires_at"
                                   value="<?= $coupon['expires_at'] ? date('Y-m-d\TH:i', strtotime($coupon['expires_at'])) : '' ?>">
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
                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1"
                               <?= $coupon['status'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Usage Statistics</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Times Used:</span>
                        <strong><?= $coupon['used_count'] ?></strong>
                    </div>
                    <?php if ($coupon['usage_limit']): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Usage Limit:</span>
                        <strong><?= $coupon['usage_limit'] ?></strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <?php $percent = min(100, ($coupon['used_count'] / $coupon['usage_limit']) * 100); ?>
                        <div class="progress-bar" style="width: <?= $percent ?>%"></div>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Created:</span>
                        <span class="text-muted"><?= date('M d, Y', strtotime($coupon['created_at'])) ?></span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-2">
                <i class="fas fa-save me-2"></i> Update Coupon
            </button>

            <button type="button" class="btn btn-outline-danger w-100" onclick="deleteCoupon()">
                <i class="fas fa-trash me-2"></i> Delete Coupon
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

    switch (type) {
        case 'percentage':
            maxField.style.display = 'block';
            valuePrefix.classList.add('d-none');
            valueSuffix.classList.remove('d-none');
            break;

        case 'free_shipping':
            discountValueField.style.display = 'none';
            break;

        case 'gift_item':
            discountValueField.style.display = 'none';
            giftItemFields.style.display = 'flex';
            break;

        case 'buy_x_get_y':
            buyXGetYFields.style.display = 'flex';
            break;
    }
}

function deleteCoupon() {
    if (confirm('Are you sure you want to delete this coupon?')) {
        window.location.href = '<?= url('admin/coupons/delete/' . $coupon['id']) ?>';
    }
}
</script>
