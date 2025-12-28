<div class="page-header">
    <h1>Edit Product</h1>
    <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Products
    </a>
</div>

<form action="<?= url('admin/products/update/' . $product['id']) ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card mb-4">
                <div class="card-header">Basic Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" class="form-control" name="name"
                               value="<?= sanitize($product['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug"
                               value="<?= sanitize($product['slug']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea class="form-control" name="short_description" rows="2"
                                  maxlength="500"><?= sanitize($product['short_description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Description</label>
                        <textarea class="form-control" name="description" rows="5"><?= sanitize($product['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="card mb-4">
                <div class="card-header">Pricing</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Regular Price *</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="price"
                                       step="0.01" min="0" value="<?= $product['price'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sale Price</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="sale_price"
                                       step="0.01" min="0" value="<?= $product['sale_price'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cost Price</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="cost_price"
                                       step="0.01" min="0" value="<?= $product['cost_price'] ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory -->
            <div class="card mb-4">
                <div class="card-header">Inventory</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" class="form-control" name="sku"
                                   value="<?= sanitize($product['sku']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" name="stock_quantity"
                                   value="<?= $product['stock_quantity'] ?>" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Low Stock Threshold</label>
                            <input type="number" class="form-control" name="low_stock_threshold"
                                   value="<?= $product['low_stock_threshold'] ?>" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Images -->
            <?php if (!empty($images)): ?>
            <div class="card mb-4">
                <div class="card-header">Current Images</div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($images as $image): ?>
                        <div class="col-md-3">
                            <div class="border rounded p-2 text-center">
                                <img src="<?= upload($image['image_path']) ?>" class="img-fluid mb-2"
                                     style="max-height: 150px;">
                                <?php if ($image['is_primary']): ?>
                                <span class="badge bg-primary">Primary</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Existing Variants -->
            <?php if (!empty($variants)): ?>
            <div class="card mb-4">
                <div class="card-header">Current Variants</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($variants as $variant): ?>
                            <tr>
                                <td><?= $variant['size'] ?: '-' ?></td>
                                <td>
                                    <?php if ($variant['color']): ?>
                                    <span style="display: inline-block; width: 15px; height: 15px; background: <?= $variant['color_code'] ?: $variant['color'] ?>; border: 1px solid #ddd; border-radius: 3px; vertical-align: middle;"></span>
                                    <?= $variant['color'] ?>
                                    <?php else: ?>
                                    -
                                    <?php endif; ?>
                                </td>
                                <td><?= $variant['stock_quantity'] ?></td>
                                <td>
                                    <span class="badge <?= $variant['status'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $variant['status'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header">Publish</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="draft" <?= $product['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                               <?= $product['is_featured'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">Featured Product</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_new" id="is_new"
                               <?= $product['is_new'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_new">New Arrival</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Update Product
                    </button>
                </div>
            </div>

            <!-- Category -->
            <div class="card mb-4">
                <div class="card-header">Category</div>
                <div class="card-body">
                    <select class="form-select" name="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= $cat['parent_name'] ? $cat['parent_name'] . ' > ' : '' ?><?= $cat['name'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- New Image -->
            <div class="card mb-4">
                <div class="card-header">Add New Image</div>
                <div class="card-body">
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <small class="text-muted">This will become the primary image</small>
                </div>
            </div>

            <!-- SEO -->
            <div class="card mb-4">
                <div class="card-header">SEO Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" maxlength="70"
                               value="<?= sanitize($product['meta_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="3" maxlength="160"><?= sanitize($product['meta_description']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Product Stats -->
            <div class="card mb-4">
                <div class="card-header">Statistics</div>
                <div class="card-body">
                    <p class="mb-2"><strong>Views:</strong> <?= number_format($product['views']) ?></p>
                    <p class="mb-2"><strong>Created:</strong> <?= formatDateTime($product['created_at']) ?></p>
                    <p class="mb-0"><strong>Updated:</strong> <?= formatDateTime($product['updated_at']) ?></p>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">Danger Zone</div>
                <div class="card-body">
                    <a href="<?= url('admin/products/delete/' . $product['id']) ?>"
                       class="btn btn-outline-danger w-100"
                       data-confirm="Are you sure you want to delete this product? This action cannot be undone.">
                        <i class="fas fa-trash me-2"></i> Delete Product
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
