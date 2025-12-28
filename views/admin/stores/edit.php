<div class="page-header">
    <h1>Edit Store</h1>
    <a href="<?= url('admin/stores') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Stores
    </a>
</div>

<form action="<?= url('admin/stores/update/' . $store['id']) ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Store Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Store Name *</label>
                        <input type="text" class="form-control" name="name" required
                               value="<?= sanitize($store['name']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug"
                               value="<?= sanitize($store['slug']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?= sanitize($store['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Contact Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                   value="<?= sanitize($store['email']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone"
                                   value="<?= sanitize($store['phone']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"><?= sanitize($store['address']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Store Statistics</div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h4><?= $store['product_count'] ?? 0 ?></h4>
                            <small class="text-muted">Products</small>
                        </div>
                        <div class="col-md-3">
                            <h4><?= $store['order_count'] ?? 0 ?></h4>
                            <small class="text-muted">Orders</small>
                        </div>
                        <div class="col-md-3">
                            <h4><?= $store['customer_count'] ?? 0 ?></h4>
                            <small class="text-muted">Customers</small>
                        </div>
                        <div class="col-md-3">
                            <h4><?= formatPrice($store['total_revenue'] ?? 0) ?></h4>
                            <small class="text-muted">Revenue</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Store Logo</div>
                <div class="card-body">
                    <?php if (!empty($store['logo'])): ?>
                    <div class="mb-3 text-center">
                        <img src="<?= upload($store['logo']) ?>" class="img-thumbnail"
                             style="max-height: 100px;">
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="logo" accept="image/*">
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Settings</div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="status"
                               <?= $store['status'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Update Store
                    </button>
                </div>
            </div>

            <?php if ($store['id'] != 1): ?>
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">Danger Zone</div>
                <div class="card-body">
                    <a href="<?= url('admin/stores/delete/' . $store['id']) ?>"
                       class="btn btn-outline-danger w-100"
                       data-confirm="Are you sure you want to delete this store? All products, orders, and data will be permanently deleted.">
                        <i class="fas fa-trash me-2"></i> Delete Store
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                This is the main store and cannot be deleted.
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>
