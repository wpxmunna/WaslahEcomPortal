<div class="page-header">
    <h1>Edit Slider</h1>
    <a href="<?= url('admin/sliders') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<form action="<?= url('admin/sliders/update/' . $slider['id']) ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Slider Content</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required
                               value="<?= sanitize($slider['title']) ?>"
                               placeholder="e.g., New Collection 2025">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control"
                               value="<?= sanitize($slider['subtitle'] ?? '') ?>"
                               placeholder="e.g., Discover Amazing Deals">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Optional longer description text"><?= sanitize($slider['description'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Button 1 Text</label>
                                <input type="text" name="button_text" class="form-control"
                                       value="<?= sanitize($slider['button_text'] ?? '') ?>"
                                       placeholder="e.g., Shop Now">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Button 1 Link</label>
                                <input type="text" name="button_link" class="form-control"
                                       value="<?= sanitize($slider['button_link'] ?? '') ?>"
                                       placeholder="e.g., shop or shop/category/women">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Button 2 Text</label>
                                <input type="text" name="button2_text" class="form-control"
                                       value="<?= sanitize($slider['button2_text'] ?? '') ?>"
                                       placeholder="e.g., Learn More">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Button 2 Link</label>
                                <input type="text" name="button2_link" class="form-control"
                                       value="<?= sanitize($slider['button2_link'] ?? '') ?>"
                                       placeholder="e.g., about">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Slider Image</h5>
                </div>
                <div class="card-body">
                    <?php if ($slider['image']): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div class="current-image">
                            <img src="<?= upload('sliders/' . $slider['image']) ?>"
                                 alt="<?= sanitize($slider['title']) ?>"
                                 class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label"><?= $slider['image'] ? 'Replace Image' : 'Background Image' ?></label>
                        <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">
                        <small class="text-muted">Recommended size: 1920x800px. Max 5MB. Formats: JPG, PNG, GIF, WebP</small>
                    </div>
                    <div id="imagePreview" style="display: none;">
                        <label class="form-label">New Image Preview</label>
                        <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Text Position</label>
                        <select name="text_position" class="form-select">
                            <option value="left" <?= ($slider['text_position'] ?? 'left') === 'left' ? 'selected' : '' ?>>Left</option>
                            <option value="center" <?= ($slider['text_position'] ?? '') === 'center' ? 'selected' : '' ?>>Center</option>
                            <option value="right" <?= ($slider['text_position'] ?? '') === 'right' ? 'selected' : '' ?>>Right</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Text Color</label>
                        <input type="color" name="text_color" class="form-control form-control-color"
                               value="<?= sanitize($slider['text_color'] ?? '#ffffff') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Overlay Opacity</label>
                        <input type="range" name="overlay_opacity" class="form-range"
                               min="0" max="1" step="0.05" value="<?= $slider['overlay_opacity'] ?? 0.40 ?>" id="opacityRange">
                        <small class="text-muted">Current: <span id="opacityValue"><?= $slider['overlay_opacity'] ?? 0.40 ?></span></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                               value="<?= $slider['sort_order'] ?? 0 ?>" min="0">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($slider['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($slider['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-save me-2"></i> Update Slider
            </button>

            <button type="button" class="btn btn-outline-danger btn-lg w-100 mt-3"
                    onclick="if(confirm('Are you sure you want to delete this slider?')) window.location.href='<?= url('admin/sliders/delete/' . $slider['id']) ?>'">
                <i class="fas fa-trash me-2"></i> Delete Slider
            </button>
        </div>
    </div>
</form>

<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');

    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(this.files[0]);
    }
});

document.getElementById('opacityRange').addEventListener('input', function() {
    document.getElementById('opacityValue').textContent = this.value;
});
</script>
