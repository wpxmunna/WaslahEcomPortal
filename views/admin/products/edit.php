<div class="page-header">
    <h1>Edit Product</h1>
    <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Products
    </a>
</div>

<?php
// Organize existing variants by color
$variantsByColor = [];
foreach ($variants ?? [] as $variant) {
    $colorId = $variant['color_id'] ?? $variant['color'] ?? 'default';
    if (!isset($variantsByColor[$colorId])) {
        $variantsByColor[$colorId] = [
            'color_name' => $variant['color'] ?? 'Default',
            'color_code' => $variant['color_code'] ?? '#cccccc',
            'sizes' => []
        ];
    }
    if ($variant['size']) {
        $variantsByColor[$colorId]['sizes'][$variant['size']] = $variant['stock_quantity'];
    }
}
?>

<form id="productEditForm" action="<?= url('admin/products/update/' . $product['id']) ?>" method="POST" enctype="multipart/form-data">
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" class="form-control" name="sku"
                                   value="<?= sanitize($product['sku']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Low Stock Threshold</label>
                            <input type="number" class="form-control" name="low_stock_threshold"
                                   value="<?= $product['low_stock_threshold'] ?>" min="0">
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Total Stock: <strong><?= $product['stock_quantity'] ?></strong> (calculated from variants)
                    </div>
                </div>
            </div>

            <!-- Size & Color Variants -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Size & Color Variants</span>
                </div>
                <div class="card-body">
                    <!-- Available Colors Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Colors for this Product</label>
                        <div class="d-flex flex-wrap gap-3" id="colorSelection">
                            <?php foreach ($colors ?? [] as $color):
                                $isSelected = isset($variantsByColor[$color['id']]) || isset($variantsByColor[$color['name']]);
                            ?>
                            <div class="form-check">
                                <input class="form-check-input color-checkbox" type="checkbox"
                                       name="selected_colors[]"
                                       value="<?= $color['id'] ?>"
                                       data-color-name="<?= $color['name'] ?>"
                                       data-color-code="<?= $color['color_code'] ?>"
                                       id="color_<?= $color['id'] ?>"
                                       <?= $isSelected ? 'checked' : '' ?>>
                                <label class="form-check-label" for="color_<?= $color['id'] ?>">
                                    <span class="color-swatch" style="background: <?= $color['color_code'] ?>; <?= $color['color_code'] === '#FFFFFF' ? 'border: 1px solid #ddd;' : '' ?>"></span>
                                    <?= $color['name'] ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Size Quantities per Color -->
                    <div id="variantsContainer">
                        <div class="alert alert-secondary text-center" id="noColorSelected" style="<?= !empty($variantsByColor) ? 'display:none;' : '' ?>">
                            <i class="fas fa-palette me-2"></i>
                            Select colors above to add size quantities
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

<!-- Product Images - Separate Section (Outside Main Form) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-images me-2"></i> Product Images</span>
                <span class="badge bg-secondary"><?= count($images ?? []) ?> images</span>
            </div>
            <div class="card-body">
                <!-- Current Images -->
                <div class="row g-3 mb-3">
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $image): ?>
                        <div class="col-md-2 col-4">
                            <div class="border rounded p-2 text-center">
                                <img src="<?= upload($image['image_path']) ?>" class="img-fluid mb-2" style="max-height: 100px;">
                                <div>
                                    <?php if ($image['is_primary']): ?>
                                        <span class="badge bg-primary">Primary</span>
                                    <?php else: ?>
                                        <a href="<?= url('admin/products/set-primary/' . $product['id'] . '/' . $image['id']) ?>"
                                           class="btn btn-sm btn-outline-primary" title="Set Primary">
                                            <i class="fas fa-star"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= url('admin/products/delete-image/' . $image['id']) ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete this image?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-muted text-center py-3">
                            <i class="fas fa-images fa-2x mb-2 d-block"></i>
                            No images yet
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Upload Form -->
                <div class="border-top pt-3">
                    <form action="<?= url('admin/products/upload-images/' . $product['id']) ?>" method="POST" enctype="multipart/form-data">
                        <?= csrfField() ?>
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">Add New Images</label>
                                <input type="file" class="form-control" name="images[]" accept="image/*" multiple required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-upload me-1"></i> Upload Images
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.color-swatch {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 4px;
    vertical-align: middle;
    margin-right: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.color-variant-card {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fa;
}
.color-variant-card .color-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}
.color-variant-card .color-header .color-swatch {
    width: 30px;
    height: 30px;
    margin-right: 10px;
}
.size-qty-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 10px;
}
.size-qty-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 10px;
    text-align: center;
}
.size-qty-item label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
    color: #495057;
}
.size-qty-item input {
    width: 100%;
    text-align: center;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 5px;
}
/* Product Image Card */
.product-image-card {
    transition: all 0.2s ease;
}
.product-image-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.product-image-card .image-actions {
    display: flex;
    gap: 5px;
    justify-content: center;
    margin-top: 5px;
}
.image-preview-item {
    position: relative;
}
.image-preview-item img {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
}
.image-preview-item .remove-preview {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: none;
    font-size: 12px;
    cursor: pointer;
}
.remove-color-btn {
    margin-left: auto;
    color: #dc3545;
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    const variantsContainer = document.getElementById('variantsContainer');
    const noColorSelected = document.getElementById('noColorSelected');
    const colorCheckboxes = document.querySelectorAll('.color-checkbox');

    // Existing variants data from PHP
    const existingVariants = <?= json_encode($variantsByColor) ?>;

    // Initialize existing variants
    colorCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const colorId = checkbox.value;
            const colorName = checkbox.dataset.colorName;
            const colorCode = checkbox.dataset.colorCode;

            // Get existing quantities for this color
            const existingQty = existingVariants[colorId]?.sizes || existingVariants[colorName]?.sizes || {};
            addColorVariant(colorId, colorName, colorCode, existingQty);
        }

        checkbox.addEventListener('change', function() {
            const colorId = this.value;
            const colorName = this.dataset.colorName;
            const colorCode = this.dataset.colorCode;

            if (this.checked) {
                const existingQty = existingVariants[colorId]?.sizes || existingVariants[colorName]?.sizes || {};
                addColorVariant(colorId, colorName, colorCode, existingQty);
            } else {
                removeColorVariant(colorId);
            }
            updateNoColorMessage();
        });
    });

    updateNoColorMessage();

    function addColorVariant(colorId, colorName, colorCode, existingQty = {}) {
        // Don't add if already exists
        if (document.getElementById('variant_color_' + colorId)) return;

        const card = document.createElement('div');
        card.className = 'color-variant-card';
        card.id = 'variant_color_' + colorId;

        let sizesHtml = '';
        sizes.forEach(size => {
            const qty = existingQty[size] || 0;
            sizesHtml += `
                <div class="size-qty-item">
                    <label>${size}</label>
                    <input type="number" name="variants[${colorId}][${size}]"
                           value="${qty}" min="0" placeholder="Qty">
                </div>
            `;
        });

        card.innerHTML = `
            <div class="color-header">
                <span class="color-swatch" style="background: ${colorCode}; ${colorCode === '#FFFFFF' ? 'border: 1px solid #ddd;' : ''}"></span>
                <strong>${colorName}</strong>
                <span class="remove-color-btn" onclick="document.getElementById('color_${colorId}').click()">
                    <i class="fas fa-times"></i>
                </span>
            </div>
            <div class="size-qty-grid">
                ${sizesHtml}
            </div>
            <div class="mt-2">
                <small class="text-muted">Enter quantity for each size</small>
            </div>
        `;

        variantsContainer.appendChild(card);
    }

    function removeColorVariant(colorId) {
        const card = document.getElementById('variant_color_' + colorId);
        if (card) {
            card.remove();
        }
    }

    function updateNoColorMessage() {
        const hasVariants = variantsContainer.querySelectorAll('.color-variant-card').length > 0;
        noColorSelected.style.display = hasVariants ? 'none' : 'block';
    }
});
</script>
