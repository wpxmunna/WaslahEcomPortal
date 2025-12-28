<div class="page-header">
    <h1>Add Courier</h1>
    <a href="<?= url('admin/couriers') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Couriers
    </a>
</div>

<form action="<?= url('admin/couriers/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Courier Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Courier Name *</label>
                            <input type="text" class="form-control" name="name" required
                                   placeholder="e.g., Standard Shipping, Express Delivery">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Courier Code *</label>
                            <input type="text" class="form-control" name="code" required
                                   placeholder="e.g., standard, express, dhl">
                            <small class="text-muted">Unique identifier (lowercase)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"
                                  placeholder="Brief description of this shipping method"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" class="form-control" name="logo" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Rates & Delivery</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Base Rate *</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="base_rate"
                                       step="0.01" min="0" value="0" required>
                            </div>
                            <small class="text-muted">Fixed shipping cost</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Per KG Rate</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="per_kg_rate"
                                       step="0.01" min="0" value="0">
                            </div>
                            <small class="text-muted">Additional cost per kg</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estimated Delivery</label>
                            <input type="text" class="form-control" name="estimated_days"
                                   placeholder="e.g., 3-5 days, 1-2 weeks">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tracking URL Template</label>
                        <input type="text" class="form-control" name="tracking_url"
                               placeholder="e.g., https://track.courier.com/?id={tracking_number}">
                        <small class="text-muted">Use {tracking_number} as placeholder</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Settings</div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                    <small class="text-muted">Enable this courier for customer selection</small>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Create Courier
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
