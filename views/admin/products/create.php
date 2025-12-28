<div class="page-header">
    <h1>Add Product</h1>
    <a href="<?= url('admin/products') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Products
    </a>
</div>

<form action="<?= url('admin/products/store') ?>" method="POST" enctype="multipart/form-data" id="productForm">
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" class="form-control" name="sku">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Low Stock Threshold</label>
                            <input type="number" class="form-control" name="low_stock_threshold"
                                   value="5" min="0">
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Stock quantity will be calculated from size variants below.
                    </div>
                </div>
            </div>

            <!-- Size & Color Variants -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Size & Color Variants</span>
                    <button type="button" class="btn btn-sm btn-primary" id="addColorBtn">
                        <i class="fas fa-plus me-1"></i> Add Color
                    </button>
                </div>
                <div class="card-body">
                    <!-- Available Colors Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Colors for this Product</label>
                        <div class="d-flex flex-wrap gap-3" id="colorSelection">
                            <?php foreach ($colors ?? [] as $color): ?>
                            <div class="form-check">
                                <input class="form-check-input color-checkbox" type="checkbox"
                                       name="selected_colors[]"
                                       value="<?= $color['id'] ?>"
                                       data-color-name="<?= $color['name'] ?>"
                                       data-color-code="<?= $color['color_code'] ?>"
                                       id="color_<?= $color['id'] ?>">
                                <label class="form-check-label" for="color_<?= $color['id'] ?>">
                                    <span class="color-swatch" style="background: <?= $color['color_code'] ?>; <?= $color['color_code'] === '#FFFFFF' ? 'border: 1px solid #ddd;' : '' ?>"></span>
                                    <?= $color['name'] ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (empty($colors)): ?>
                        <div class="alert alert-warning">
                            No colors configured. <a href="<?= url('admin/colors') ?>">Add colors first</a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Size Quantities per Color -->
                    <div id="variantsContainer">
                        <div class="alert alert-secondary text-center" id="noColorSelected">
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
                        <img id="imagePreview" src="<?= asset('images/placeholder.jpg') ?>"
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

    // Handle color checkbox changes
    colorCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const colorId = this.value;
            const colorName = this.dataset.colorName;
            const colorCode = this.dataset.colorCode;

            if (this.checked) {
                addColorVariant(colorId, colorName, colorCode);
            } else {
                removeColorVariant(colorId);
            }
            updateNoColorMessage();
        });
    });

    function addColorVariant(colorId, colorName, colorCode) {
        const card = document.createElement('div');
        card.className = 'color-variant-card';
        card.id = 'variant_color_' + colorId;

        let sizesHtml = '';
        sizes.forEach(size => {
            sizesHtml += `
                <div class="size-qty-item">
                    <label>${size}</label>
                    <input type="number" name="variants[${colorId}][${size}]"
                           value="0" min="0" placeholder="Qty">
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

    // Image preview
    document.querySelectorAll('input[data-preview]').forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.querySelector(this.dataset.preview);
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => preview.src = e.target.result;
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
});
</script>
