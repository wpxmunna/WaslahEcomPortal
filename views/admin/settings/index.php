<div class="page-header">
    <div>
        <h1><i class="fas fa-cog me-2"></i>Settings</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Quick Settings Menu -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <a href="<?= url('admin/settings/general') ?>" class="text-decoration-none">
            <div class="card h-100 border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-cog fa-3x text-primary mb-3"></i>
                    <h5>General</h5>
                    <p class="text-muted small mb-0">Site name, tagline, and logo</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= url('admin/settings') ?>" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-store fa-3x text-secondary mb-3"></i>
                    <h5>Store Settings</h5>
                    <p class="text-muted small mb-0">Store information and currency</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= url('admin/settings/business') ?>" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-building fa-3x text-info mb-3"></i>
                    <h5>Business Info</h5>
                    <p class="text-muted small mb-0">Social links, contacts, and URLs</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= url('admin/settings/payment') ?>" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-credit-card fa-3x text-success mb-3"></i>
                    <h5>Payment</h5>
                    <p class="text-muted small mb-0">Payment methods and gateway</p>
                </div>
            </div>
        </a>
    </div>
</div>
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <a href="<?= url('admin/settings/email') ?>" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-envelope fa-3x text-warning mb-3"></i>
                    <h5>Email</h5>
                    <p class="text-muted small mb-0">SMTP and email configuration</p>
                </div>
            </div>
        </a>
    </div>
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
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Currency Settings:</strong> Currency symbol and code are configured in <code>config/config.php</code>
                        <br><small>Current: <strong><?= CURRENCY_SYMBOL ?></strong> (<?= CURRENCY_CODE ?>)</small>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
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
