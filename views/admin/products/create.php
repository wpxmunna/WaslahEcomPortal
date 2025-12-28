<div class="page-header">
    <h1>Add Product</h1>
    <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Products
    </a>
</div>

<form action="<?= url('admin/products/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card mb-4">
                <div class="card-header">Basic Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug"
                               placeholder="Auto-generated from name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea class="form-control" name="short_description" rows="2"
                                  maxlength="500"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Description</label>
                        <textarea class="form-control" name="description" rows="5"></textarea>
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
                                       step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sale Price</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="sale_price"
                                       step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cost Price</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" name="cost_price"
                                       step="0.01" min="0">
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
                            <input type="text" class="form-control" name="sku">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" name="stock_quantity"
                                   value="0" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Low Stock Threshold</label>
                            <input type="number" class="form-control" name="low_stock_threshold"
                                   value="5" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variants -->
            <div class="card mb-4">
                <div class="card-header">Variants (Optional)</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sizes</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="variant_sizes[]" value="<?= $size ?>"
                                           id="size_<?= $size ?>">
                                    <label class="form-check-label" for="size_<?= $size ?>"><?= $size ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Colors</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php
                                $colors = [
                                    'Black' => '#000000',
                                    'White' => '#FFFFFF',
                                    'Red' => '#e94560',
                                    'Blue' => '#1a1a2e',
                                    'Green' => '#28a745'
                                ];
                                foreach ($colors as $name => $code):
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="variant_colors[]" value="<?= $name ?>"
                                           id="color_<?= $name ?>">
                                    <label class="form-check-label" for="color_<?= $name ?>">
                                        <span style="display: inline-block; width: 15px; height: 15px; background: <?= $code ?>; border: 1px solid #ddd; border-radius: 3px; vertical-align: middle;"></span>
                                        <?= $name ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header">Publish</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured">
                        <label class="form-check-label" for="is_featured">Featured Product</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_new" id="is_new" checked>
                        <label class="form-check-label" for="is_new">New Arrival</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Save Product
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
                        <option value="<?= $cat['id'] ?>">
                            <?= $cat['parent_name'] ? $cat['parent_name'] . ' > ' : '' ?><?= $cat['name'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Image -->
            <div class="card mb-4">
                <div class="card-header">Product Image</div>
                <div class="card-body">
                    <div class="mb-3">
                        <img id="imagePreview" src="https://via.placeholder.com/300x400?text=No+Image"
                             class="img-fluid rounded mb-3" style="max-height: 200px;">
                        <input type="file" class="form-control" name="image" accept="image/*"
                               data-preview="#imagePreview">
                    </div>
                </div>
            </div>

            <!-- Additional Images -->
            <div class="card mb-4">
                <div class="card-header">Additional Images</div>
                <div class="card-body">
                    <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
                    <small class="text-muted">You can select multiple images</small>
                </div>
            </div>

            <!-- SEO -->
            <div class="card mb-4">
                <div class="card-header">SEO Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" maxlength="70">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="3" maxlength="160"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
