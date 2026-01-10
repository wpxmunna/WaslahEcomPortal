<div class="page-header">
    <h1>Add Lookbook Image</h1>
    <a href="<?= url('admin/lookbook') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<form action="<?= url('admin/lookbook/store') ?>" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Image</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#uploadTab" type="button">Upload Image</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#urlTab" type="button">Image URL</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="uploadTab">
                            <div class="mb-3">
                                <label class="form-label">Upload Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">
                                <small class="text-muted">Recommended: Square images (500x500px). Max 5MB.</small>
                            </div>
                            <div id="imagePreview" style="display: none;">
                                <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="urlTab">
                            <div class="mb-3">
                                <label class="form-label">Image URL</label>
                                <input type="url" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
                                <small class="text-muted">Enter a direct link to an image (Instagram, Unsplash, etc.)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Caption</label>
                        <input type="text" name="caption" class="form-control" placeholder="e.g., Summer Vibes">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link (optional)</label>
                        <input type="url" name="link" class="form-control" placeholder="https://instagram.com/p/...">
                        <small class="text-muted">Link to Instagram post or product page</small>
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
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" value="1">
                            <label class="form-check-label" for="isFeatured">
                                <strong>Featured Image</strong>
                                <small class="d-block text-muted">This will be displayed as the large image</small>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-save me-2"></i> Add Image
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
</script>
