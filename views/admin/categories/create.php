<div class="page-header">
    <h1>Add Category</h1>
    <a href="<?= url('admin/categories') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Categories
    </a>
</div>

<form action="<?= url('admin/categories/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Category Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" name="name" required
                               placeholder="e.g., T-Shirts, Dresses">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug"
                               placeholder="Leave empty to auto-generate">
                        <small class="text-muted">URL-friendly name. Auto-generated if left empty.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" name="parent_id">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($parents as $parent): ?>
                            <option value="<?= $parent['id'] ?>">
                                <?= sanitize($parent['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"
                                  placeholder="Brief description of this category"></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Appearance</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">Recommended size: 300x300px</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Icon Class</label>
                            <input type="text" class="form-control" name="icon"
                                   placeholder="e.g., fas fa-tshirt">
                            <small class="text-muted">Font Awesome icon class</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="0" min="0">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Create Category
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
