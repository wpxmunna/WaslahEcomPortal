<?php
/**
 * Social Media Manager - Edit View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-edit"></i> Edit Social Media Link</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/social-media') ?>">Social Media</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit: <?= htmlspecialchars($socialLink['name']) ?></h5>
            </div>
            <form action="<?= url('admin/social-media/update/' . $socialLink['id']) ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="card-body">
                    <!-- Platform Presets -->
                    <div class="mb-4">
                        <label class="form-label">Quick Select Platform</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($presets as $key => $preset): ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm preset-btn <?= $socialLink['platform'] === $key ? 'active' : '' ?>"
                                        data-platform="<?= $key ?>"
                                        data-name="<?= htmlspecialchars($preset['name']) ?>"
                                        data-icon="<?= htmlspecialchars($preset['icon']) ?>"
                                        data-color="<?= htmlspecialchars($preset['color']) ?>"
                                        data-style="<?= $preset['style'] ?? 'brands' ?>">
                                    <i class="fa-<?= $preset['style'] ?? 'brands' ?> <?= $preset['icon'] ?> me-1" style="color: <?= $preset['color'] ?>"></i>
                                    <?= $preset['name'] ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Platform Key <span class="text-danger">*</span></label>
                            <input type="text" name="platform" id="platform" class="form-control" required
                                   value="<?= htmlspecialchars($socialLink['platform']) ?>"
                                   placeholder="e.g., facebook, instagram">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required
                                   value="<?= htmlspecialchars($socialLink['name']) ?>"
                                   placeholder="e.g., Facebook, Instagram">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url" name="url" id="url" class="form-control" required
                               value="<?= htmlspecialchars($socialLink['url']) ?>"
                               placeholder="https://facebook.com/yourpage">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Icon Class <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-icons"></i></span>
                                <input type="text" name="icon" id="icon" class="form-control" required
                                       value="<?= htmlspecialchars($socialLink['icon']) ?>"
                                       placeholder="fa-facebook-f">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Icon Style</label>
                            <select name="icon_style" id="icon_style" class="form-select">
                                <option value="brands" <?= $socialLink['icon_style'] === 'brands' ? 'selected' : '' ?>>Brands (fa-brands)</option>
                                <option value="solid" <?= $socialLink['icon_style'] === 'solid' ? 'selected' : '' ?>>Solid (fa-solid)</option>
                                <option value="regular" <?= $socialLink['icon_style'] === 'regular' ? 'selected' : '' ?>>Regular (fa-regular)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Brand Color</label>
                            <div class="input-group">
                                <input type="color" name="color" id="color" class="form-control form-control-color"
                                       value="<?= htmlspecialchars($socialLink['color']) ?>">
                                <input type="text" id="colorHex" class="form-control"
                                       value="<?= htmlspecialchars($socialLink['color']) ?>" style="max-width: 100px;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                               value="<?= $socialLink['sort_order'] ?>" min="0" style="max-width: 100px;">
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                       <?= $socialLink['is_active'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_in_header" id="show_in_header"
                                       <?= $socialLink['show_in_header'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="show_in_header">Show in Header</label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_in_footer" id="show_in_footer"
                                       <?= $socialLink['show_in_footer'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="show_in_footer">Show in Footer</label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="open_new_tab" id="open_new_tab"
                                       <?= $socialLink['open_new_tab'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="open_new_tab">Open in New Tab</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Social Link
                    </button>
                    <a href="<?= url('admin/social-media') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <a href="<?= url('admin/social-media/delete/' . $socialLink['id'] . '?csrf_token=' . Session::getCsrfToken()) ?>"
                       class="btn btn-outline-danger float-end"
                       onclick="return confirm('Are you sure you want to delete this link?')">
                        <i class="fas fa-trash me-2"></i>Delete
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Preview Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <div id="iconPreview" class="social-icon-preview-lg mx-auto"
                         style="background-color: <?= htmlspecialchars($socialLink['color']) ?>;">
                        <i class="fa-<?= $socialLink['icon_style'] ?> <?= htmlspecialchars($socialLink['icon']) ?>"></i>
                    </div>
                </div>
                <h5 id="namePreview"><?= htmlspecialchars($socialLink['name']) ?></h5>
                <a href="<?= htmlspecialchars($socialLink['url']) ?>" id="urlPreview" class="text-muted small d-block text-truncate" target="_blank">
                    <?= htmlspecialchars($socialLink['url']) ?>
                </a>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Info</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted">Created:</td>
                        <td><?= date('M d, Y H:i', strtotime($socialLink['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Updated:</td>
                        <td><?= date('M d, Y H:i', strtotime($socialLink['updated_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.social-icon-preview-lg {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 36px;
    transition: all 0.3s ease;
}
.preset-btn.active {
    border-color: #0d6efd;
    background-color: #e7f1ff;
}
</style>

<script>
// Preset buttons
document.querySelectorAll('.preset-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        document.getElementById('platform').value = this.dataset.platform;
        document.getElementById('name').value = this.dataset.name;
        document.getElementById('icon').value = this.dataset.icon;
        document.getElementById('color').value = this.dataset.color;
        document.getElementById('colorHex').value = this.dataset.color;
        document.getElementById('icon_style').value = this.dataset.style;

        updatePreview();
    });
});

// Color picker sync
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('colorHex').value = this.value;
    updatePreview();
});

document.getElementById('colorHex').addEventListener('input', function() {
    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
        document.getElementById('color').value = this.value;
        updatePreview();
    }
});

// Live preview
['name', 'icon', 'icon_style', 'url'].forEach(id => {
    document.getElementById(id).addEventListener('input', updatePreview);
});

function updatePreview() {
    const name = document.getElementById('name').value || 'Platform Name';
    const icon = document.getElementById('icon').value || 'fa-question';
    const iconStyle = document.getElementById('icon_style').value || 'brands';
    const color = document.getElementById('color').value || '#000000';
    const url = document.getElementById('url').value || 'https://example.com';

    document.getElementById('namePreview').textContent = name;
    document.getElementById('urlPreview').textContent = url;
    document.getElementById('urlPreview').href = url;
    document.getElementById('iconPreview').style.backgroundColor = color;
    document.getElementById('iconPreview').innerHTML = `<i class="fa-${iconStyle} ${icon}"></i>`;
}
</script>
