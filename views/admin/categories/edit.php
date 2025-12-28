<div class="page-header">
    <h1>Edit Category</h1>
    <a href="<?= url('admin/categories') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Categories
    </a>
</div>

<form action="<?= url('admin/categories/update/' . $category['id']) ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Category Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" name="name" required
                               value="<?= sanitize($category['name']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug"
                               value="<?= sanitize($category['slug']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" name="parent_id">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($parents as $parent): ?>
                            <option value="<?= $parent['id'] ?>"
                                <?= $category['parent_id'] == $parent['id'] ? 'selected' : '' ?>>
                                <?= sanitize($parent['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?= sanitize($category['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Appearance</div>
                <div class="card-body">
                    <?php if ($category['image']): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div>
                            <img src="<?= upload($category['image']) ?>" class="img-thumbnail"
                                 style="max-height: 150px;">
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Icon Class</label>
                            <input type="text" class="form-control" name="icon"
                                   value="<?= sanitize($category['icon']) ?>"
                                   placeholder="e.g., fas fa-tshirt">
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
                        <input type="number" class="form-control" name="sort_order"
                               value="<?= $category['sort_order'] ?>" min="0">
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="status"
                               <?= $category['status'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Update Category
                    </button>
                </div>
            </div>

            <div class="card border-danger">
                <div class="card-header bg-danger text-white">Danger Zone</div>
                <div class="card-body">
                    <a href="<?= url('admin/categories/delete/' . $category['id']) ?>"
                       class="btn btn-outline-danger w-100"
                       data-confirm="Are you sure you want to delete this category?">
                        <i class="fas fa-trash me-2"></i> Delete Category
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
