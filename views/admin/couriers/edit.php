<div class="page-header">
    <h1>Edit Courier</h1>
    <a href="<?= url('admin/couriers') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Couriers
    </a>
</div>

<form action="<?= url('admin/couriers/update/' . $courier['id']) ?>" method="POST" enctype="multipart/form-data">
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
                                   value="<?= sanitize($courier['name']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Courier Code *</label>
                            <input type="text" class="form-control" name="code" required
                                   value="<?= sanitize($courier['code']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"><?= sanitize($courier['description']) ?></textarea>
                    </div>

                    <?php if (!empty($courier['logo'])): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Logo</label>
                        <div>
                            <img src="<?= upload($courier['logo']) ?>" class="img-thumbnail"
                                 style="max-height: 60px;">
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">New Logo</label>
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
                                       step="0.01" min="0" value="<?= $courier['base_rate'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Per KG Rate</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="per_kg_rate"
                                       step="0.01" min="0" value="<?= $courier['per_kg_rate'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estimated Delivery</label>
                            <input type="text" class="form-control" name="estimated_days"
                                   value="<?= sanitize($courier['estimated_days']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tracking URL Template</label>
                        <input type="text" class="form-control" name="tracking_url"
                               value="<?= sanitize($courier['tracking_url']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Settings</div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="status"
                               <?= $courier['status'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Update Courier
                    </button>
                </div>
            </div>

            <div class="card border-danger">
                <div class="card-header bg-danger text-white">Danger Zone</div>
                <div class="card-body">
                    <a href="<?= url('admin/couriers/delete/' . $courier['id']) ?>"
                       class="btn btn-outline-danger w-100"
                       data-confirm="Are you sure you want to delete this courier?">
                        <i class="fas fa-trash me-2"></i> Delete Courier
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
