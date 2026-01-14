<div class="page-header">
    <h1>General Settings</h1>
    <a href="<?= url('admin/settings') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Settings
    </a>
</div>

<form action="<?= url('admin/settings/update-general') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Site Identity -->
            <div class="card mb-4">
                <div class="card-header">Site Identity</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Site Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="site_name"
                               class="form-control"
                               value="<?= sanitize($settings['site_name'] ?? '') ?>"
                               placeholder="Waslah Fashion"
                               required>
                        <small class="text-muted">This appears in the browser tab, header, and footer</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Site Tagline</label>
                        <input type="text"
                               name="site_tagline"
                               class="form-control"
                               value="<?= sanitize($settings['site_tagline'] ?? '') ?>"
                               placeholder="Authenticity in Every Stitch">
                        <small class="text-muted">A short description of your site</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Site Logo</label>

                        <?php if (!empty($settings['site_logo'])): ?>
                        <div class="mb-2">
                            <img src="<?= asset($settings['site_logo']) ?>"
                                 alt="Current Logo"
                                 style="max-height: 80px; background: #f5f5f5; padding: 10px; border-radius: 4px;">
                            <div class="text-muted small mt-1">Current logo</div>
                        </div>
                        <?php endif; ?>

                        <input type="file"
                               name="site_logo"
                               class="form-control"
                               accept="image/png,image/jpeg,image/jpg,image/webp">
                        <small class="text-muted">
                            Upload PNG, JPG, or WEBP. Recommended size: 200x50px (transparent background for best results)
                        </small>
                    </div>
                </div>
            </div>

            <!-- Current Values -->
            <div class="card mb-4">
                <div class="card-header">Current Display Values</div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <strong><i class="fas fa-info-circle me-2"></i>Preview:</strong><br>
                        <div class="mt-2">
                            <strong>Site Name:</strong> <?= sanitize($settings['site_name'] ?? 'Waslah') ?><br>
                            <strong>Tagline:</strong> <?= sanitize($settings['site_tagline'] ?? 'Authenticity in Every Stitch') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Save Button -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Save General Settings
                    </button>
                </div>
            </div>

            <!-- Help -->
            <div class="card mt-4">
                <div class="card-header">Help</div>
                <div class="card-body">
                    <p class="small mb-2"><strong>Site Name</strong></p>
                    <p class="small text-muted mb-3">Appears in browser title, header, and footer. Keep it short and memorable.</p>

                    <p class="small mb-2"><strong>Site Tagline</strong></p>
                    <p class="small text-muted mb-3">A brief description that appears alongside your site name.</p>

                    <p class="small mb-2"><strong>Site Logo</strong></p>
                    <p class="small text-muted mb-0">Upload your brand logo. Use a transparent PNG for best results. Logo will replace the site name in the header.</p>
                </div>
            </div>
        </div>
    </div>
</form>
