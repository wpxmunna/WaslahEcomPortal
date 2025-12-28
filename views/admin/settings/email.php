<div class="page-header">
    <h1>Email Settings</h1>
    <a href="<?= url('admin/settings') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Settings
    </a>
</div>

<form action="<?= url('admin/settings/email/update') ?>" method="POST">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- SMTP Settings -->
            <div class="card mb-4">
                <div class="card-header">SMTP Configuration</div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Email functionality is not yet implemented. Configure these settings for future use.
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" name="smtp_host"
                                   value="<?= sanitize($settings['smtp_host'] ?? '') ?>"
                                   placeholder="smtp.gmail.com">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">SMTP Port</label>
                            <input type="text" class="form-control" name="smtp_port"
                                   value="<?= sanitize($settings['smtp_port'] ?? '587') ?>"
                                   placeholder="587">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SMTP Username</label>
                            <input type="text" class="form-control" name="smtp_username"
                                   value="<?= sanitize($settings['smtp_username'] ?? '') ?>"
                                   placeholder="your-email@gmail.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SMTP Password</label>
                            <input type="password" class="form-control" name="smtp_password"
                                   value="<?= sanitize($settings['smtp_password'] ?? '') ?>"
                                   placeholder="App password">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Encryption</label>
                        <select class="form-select" name="smtp_encryption">
                            <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                            <option value="ssl" <?= ($settings['smtp_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="none" <?= ($settings['smtp_encryption'] ?? 'tls') === 'none' ? 'selected' : '' ?>>None</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- From Address -->
            <div class="card mb-4">
                <div class="card-header">From Address</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">From Name</label>
                            <input type="text" class="form-control" name="mail_from_name"
                                   value="<?= sanitize($settings['mail_from_name'] ?? SITE_NAME) ?>"
                                   placeholder="Waslah Fashion">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">From Email</label>
                            <input type="email" class="form-control" name="mail_from_email"
                                   value="<?= sanitize($settings['mail_from_email'] ?? SITE_EMAIL) ?>"
                                   placeholder="noreply@waslah.com">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Common SMTP Settings -->
            <div class="card mb-4">
                <div class="card-header">Common SMTP Settings</div>
                <div class="card-body">
                    <h6>Gmail</h6>
                    <ul class="small text-muted mb-3">
                        <li>Host: smtp.gmail.com</li>
                        <li>Port: 587</li>
                        <li>Encryption: TLS</li>
                        <li>Use App Password</li>
                    </ul>

                    <h6>Outlook</h6>
                    <ul class="small text-muted mb-3">
                        <li>Host: smtp.office365.com</li>
                        <li>Port: 587</li>
                        <li>Encryption: TLS</li>
                    </ul>

                    <h6>SendGrid</h6>
                    <ul class="small text-muted mb-0">
                        <li>Host: smtp.sendgrid.net</li>
                        <li>Port: 587</li>
                        <li>Encryption: TLS</li>
                    </ul>
                </div>
            </div>

            <!-- Save Button -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Save Email Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
