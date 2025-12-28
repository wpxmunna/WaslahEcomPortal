<div class="page-header">
    <h1>Payment Settings</h1>
    <a href="<?= url('admin/settings') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Settings
    </a>
</div>

<form action="<?= url('admin/settings/payment/update') ?>" method="POST">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Cash on Delivery -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-money-bill-wave me-2"></i> Cash on Delivery</span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="payment_cod_enabled"
                               id="payment_cod_enabled"
                               <?= ($settings['payment_cod_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Allow customers to pay in cash when their order is delivered.
                        No additional configuration required.
                    </p>
                </div>
            </div>

            <!-- Stripe -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fab fa-stripe me-2"></i> Stripe</span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="payment_stripe_enabled"
                               id="payment_stripe_enabled"
                               <?= ($settings['payment_stripe_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This is a <strong>mock implementation</strong> for testing purposes.
                        In production, integrate with the actual Stripe API.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Public Key</label>
                            <input type="text" class="form-control" name="stripe_public_key"
                                   value="<?= sanitize($settings['stripe_public_key'] ?? '') ?>"
                                   placeholder="pk_test_...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Secret Key</label>
                            <input type="password" class="form-control" name="stripe_secret_key"
                                   value="<?= sanitize($settings['stripe_secret_key'] ?? '') ?>"
                                   placeholder="sk_test_...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- PayPal -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fab fa-paypal me-2"></i> PayPal</span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="payment_paypal_enabled"
                               id="payment_paypal_enabled"
                               <?= ($settings['payment_paypal_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This is a <strong>mock implementation</strong> for testing purposes.
                        In production, integrate with the actual PayPal API.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client ID</label>
                            <input type="text" class="form-control" name="paypal_client_id"
                                   value="<?= sanitize($settings['paypal_client_id'] ?? '') ?>"
                                   placeholder="Client ID">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Secret</label>
                            <input type="password" class="form-control" name="paypal_secret"
                                   value="<?= sanitize($settings['paypal_secret'] ?? '') ?>"
                                   placeholder="Secret">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mode</label>
                        <select class="form-select" name="paypal_mode">
                            <option value="sandbox" <?= ($settings['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' ?>>
                                Sandbox (Testing)
                            </option>
                            <option value="live" <?= ($settings['paypal_mode'] ?? 'sandbox') === 'live' ? 'selected' : '' ?>>
                                Live (Production)
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Test Cards -->
            <div class="card mb-4">
                <div class="card-header">Test Card Numbers</div>
                <div class="card-body">
                    <p class="small text-muted mb-3">Use these card numbers for testing:</p>

                    <div class="mb-2">
                        <strong>Success:</strong>
                        <code>4242 4242 4242 4242</code>
                    </div>
                    <div class="mb-2">
                        <strong>Declined:</strong>
                        <code>4000 0000 0000 0002</code>
                    </div>
                    <div class="mb-0">
                        <strong>Expiry/CVV:</strong>
                        <span class="text-muted">Any future date / Any 3 digits</span>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Save Payment Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
