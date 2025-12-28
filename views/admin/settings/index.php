<div class="page-header">
    <h1>Settings</h1>
</div>

<form action="<?= url('admin/settings/update') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Store Info -->
            <div class="card mb-4">
                <div class="card-header">Store Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Store Name</label>
                            <input type="text" class="form-control" name="store_name"
                                   value="<?= sanitize($store['name']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Store Email</label>
                            <input type="email" class="form-control" name="store_email"
                                   value="<?= sanitize($store['email']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Store Phone</label>
                            <input type="text" class="form-control" name="store_phone"
                                   value="<?= sanitize($store['phone']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Store Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                        </div>
                    </div>

                    <?php if (!empty($store['logo'])): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Logo</label>
                        <div>
                            <img src="<?= upload($store['logo']) ?>" class="img-thumbnail"
                                 style="max-height: 80px;">
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Store Address</label>
                        <textarea class="form-control" name="store_address" rows="2"><?= sanitize($store['address']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Store Description</label>
                        <textarea class="form-control" name="store_description" rows="2"><?= sanitize($store['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Currency & Pricing -->
            <div class="card mb-4">
                <div class="card-header">Currency & Pricing</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Currency Symbol</label>
                            <input type="text" class="form-control" name="currency_symbol"
                                   value="<?= sanitize($settings['currency_symbol'] ?? CURRENCY_SYMBOL) ?>" maxlength="5">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Currency Code</label>
                            <input type="text" class="form-control" name="currency_code"
                                   value="<?= sanitize($settings['currency_code'] ?? CURRENCY_CODE) ?>" maxlength="3">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" class="form-control" name="tax_rate"
                                   step="0.01" min="0" max="100"
                                   value="<?= $settings['tax_rate'] ?? TAX_RATE ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping -->
            <div class="card mb-4">
                <div class="card-header">Shipping Settings</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Free Shipping Threshold</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="free_shipping_threshold"
                                       step="0.01" min="0"
                                       value="<?= $settings['free_shipping_threshold'] ?? FREE_SHIPPING_THRESHOLD ?>">
                            </div>
                            <small class="text-muted">Set to 0 to disable free shipping</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Default Shipping Cost</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="default_shipping_cost"
                                       step="0.01" min="0"
                                       value="<?= $settings['default_shipping_cost'] ?? DEFAULT_SHIPPING_COST ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="card mb-4">
                <div class="card-header">Social Media</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="fab fa-facebook me-2"></i>Facebook</label>
                            <input type="url" class="form-control" name="facebook_url"
                                   value="<?= sanitize($settings['facebook_url'] ?? '') ?>"
                                   placeholder="https://facebook.com/...">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="fab fa-instagram me-2"></i>Instagram</label>
                            <input type="url" class="form-control" name="instagram_url"
                                   value="<?= sanitize($settings['instagram_url'] ?? '') ?>"
                                   placeholder="https://instagram.com/...">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="fab fa-twitter me-2"></i>Twitter</label>
                            <input type="url" class="form-control" name="twitter_url"
                                   value="<?= sanitize($settings['twitter_url'] ?? '') ?>"
                                   placeholder="https://twitter.com/...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- General Settings -->
            <div class="card mb-4">
                <div class="card-header">General Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Products Per Page</label>
                        <input type="number" class="form-control" name="products_per_page"
                               min="4" max="48"
                               value="<?= $settings['products_per_page'] ?? PRODUCTS_PER_PAGE ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Order Number Prefix</label>
                        <input type="text" class="form-control" name="order_prefix"
                               value="<?= sanitize($settings['order_prefix'] ?? 'ORD') ?>" maxlength="10">
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="allow_guest_checkout" id="allow_guest_checkout"
                               <?= ($settings['allow_guest_checkout'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="allow_guest_checkout">
                            Allow Guest Checkout
                        </label>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="card mb-4">
                <div class="card-header">Footer Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Footer Text</label>
                        <textarea class="form-control" name="footer_text" rows="3"><?= sanitize($settings['footer_text'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Save Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Other Settings</div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="<?= url('admin/settings/payment') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-credit-card me-3 text-primary"></i>
                            <strong>Payment Settings</strong>
                            <div class="small text-muted">Configure payment gateways</div>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="<?= url('admin/settings/email') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-envelope me-3 text-primary"></i>
                            <strong>Email Settings</strong>
                            <div class="small text-muted">Configure SMTP and email templates</div>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="<?= url('admin/couriers') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-truck me-3 text-primary"></i>
                            <strong>Shipping Methods</strong>
                            <div class="small text-muted">Manage courier services</div>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
