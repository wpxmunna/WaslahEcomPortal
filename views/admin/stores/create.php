<div class="page-header">
    <h1>Create New Store</h1>
    <a href="<?= url('admin/stores') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Stores
    </a>
</div>

<form action="<?= url('admin/stores/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Store Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Store Name *</label>
                        <input type="text" class="form-control" name="name" required
                               placeholder="e.g., Waslah Fashion">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug"
                               placeholder="Leave empty to auto-generate">
                        <small class="text-muted">URL-friendly identifier</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"
                                  placeholder="Brief description of this store"></textarea>
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
                                   placeholder="store@example.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone"
                                   placeholder="+1 234 567 890">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"
                                  placeholder="Store address"></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Initial Setup</div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="clone_categories"
                               id="clone_categories" checked>
                        <label class="form-check-label" for="clone_categories">
                            Clone categories from main store
                        </label>
                        <div class="small text-muted">
                            Creates a copy of all categories from the main store
                        </div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="clone_couriers"
                               id="clone_couriers" checked>
                        <label class="form-check-label" for="clone_couriers">
                            Clone courier services from main store
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Store Logo</div>
                <div class="card-body">
                    <input type="file" class="form-control" name="logo" accept="image/*">
                    <small class="text-muted">Recommended size: 200x200px</small>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Settings</div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Create Store
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
