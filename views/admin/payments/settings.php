<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Payment Settings</h1>
        <a href="<?= url('admin/payments') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Payments
        </a>
    </div>

    <form method="POST" action="<?= url('admin/payments/settings') ?>">
        <div class="row g-4">
            <!-- Cash on Delivery -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery</h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="payment_cod_enabled" id="codEnabled"
                                   <?= ($settings['cod_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="codEnabled">Enabled</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-0">Allow customers to pay when they receive their order. No additional configuration required.</p>
                    </div>
                </div>
            </div>

            <!-- bKash -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-mobile-alt me-2"></i>bKash</h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="payment_bkash_enabled" id="bkashEnabled"
                                   <?= ($settings['bkash_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="bkashEnabled">Enabled</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="bkashNumber" class="form-label">bKash Number</label>
                            <input type="text" class="form-control" id="bkashNumber" name="payment_bkash_number"
                                   value="<?= sanitize($settings['bkash_number'] ?? '') ?>" placeholder="01XXXXXXXXX">
                            <div class="form-text">Your bKash merchant or personal number</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nagad -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-mobile-alt me-2"></i>Nagad</h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="payment_nagad_enabled" id="nagadEnabled"
                                   <?= ($settings['nagad_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="nagadEnabled">Enabled</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nagadNumber" class="form-label">Nagad Number</label>
                            <input type="text" class="form-control" id="nagadNumber" name="payment_nagad_number"
                                   value="<?= sanitize($settings['nagad_number'] ?? '') ?>" placeholder="01XXXXXXXXX">
                            <div class="form-text">Your Nagad merchant or personal number</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credit/Debit Card -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Credit/Debit Card</h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="payment_card_enabled" id="cardEnabled"
                                   <?= ($settings['card_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="cardEnabled">Enabled</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-0">Accept Visa, Mastercard payments. Integration with payment gateway required for production use.</p>
                    </div>
                </div>
            </div>

            <!-- Bank Transfer -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-university me-2"></i>Bank Transfer</h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="payment_bank_enabled" id="bankEnabled"
                                   <?= ($settings['bank_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="bankEnabled">Enabled</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="bankName" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bankName" name="payment_bank_name"
                                       value="<?= sanitize($settings['bank_name'] ?? '') ?>" placeholder="e.g., Dutch Bangla Bank">
                            </div>
                            <div class="col-md-4">
                                <label for="bankAccount" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="bankAccount" name="payment_bank_account"
                                       value="<?= sanitize($settings['bank_account'] ?? '') ?>" placeholder="Account number">
                            </div>
                            <div class="col-md-4">
                                <label for="bankBranch" class="form-label">Branch</label>
                                <input type="text" class="form-control" id="bankBranch" name="payment_bank_branch"
                                       value="<?= sanitize($settings['bank_branch'] ?? '') ?>" placeholder="Branch name">
                            </div>
                        </div>
                        <div class="form-text mt-2">These details will be shown to customers who select bank transfer</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>
