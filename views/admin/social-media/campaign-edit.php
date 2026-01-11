<?php
/**
 * Campaign Messages - Edit View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-edit"></i> Edit Campaign Message</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/social-media') ?>">Social Media</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/social-media/campaigns') ?>">Campaigns</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<form action="<?= url('admin/social-media/campaigns/update/' . $campaign['id']) ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Message Content</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required
                               value="<?= htmlspecialchars($campaign['title']) ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Platform</label>
                            <select name="platform" id="platform" class="form-select">
                                <?php foreach ($platforms as $key => $platform): ?>
                                <option value="<?= $key ?>"
                                        data-icon="<?= $platform['icon'] ?>"
                                        data-color="<?= $platform['color'] ?>"
                                        <?= $campaign['platform'] === $key ? 'selected' : '' ?>>
                                    <?= $platform['name'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Message Type</label>
                            <select name="message_type" id="message_type" class="form-select">
                                <?php foreach ($messageTypes as $key => $type): ?>
                                <option value="<?= $key ?>" <?= $campaign['message_type'] === $key ? 'selected' : '' ?>>
                                    <?= $type['name'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Message <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" class="form-control" rows="8" required><?= htmlspecialchars($campaign['content']) ?></textarea>
                        <small class="text-muted">
                            <span id="charCount"><?= strlen($campaign['content']) ?></span> characters
                            | Instagram: 2200 max | Facebook: 63,206 max | WhatsApp: 65,536 max
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Version <small class="text-muted">(for previews/stories)</small></label>
                        <textarea name="short_content" class="form-control" rows="2" maxlength="500"><?= htmlspecialchars($campaign['short_content'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hashtags</label>
                        <input type="text" name="hashtags" class="form-control"
                               value="<?= htmlspecialchars($campaign['hashtags'] ?? '') ?>">
                        <small class="text-muted">Separate with spaces, include # symbol</small>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Call to Action</label>
                            <input type="text" name="call_to_action" class="form-control"
                                   value="<?= htmlspecialchars($campaign['call_to_action'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CTA Link</label>
                            <input type="url" name="cta_url" class="form-control"
                                   value="<?= htmlspecialchars($campaign['cta_url'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Campaign Image</h5>
                </div>
                <div class="card-body">
                    <?php if ($campaign['image_path']): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div class="text-center">
                            <img src="<?= asset('uploads/' . $campaign['image_path']) ?>" alt="Campaign" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label"><?= $campaign['image_path'] ? 'Replace Image' : 'Upload Image' ?></label>
                        <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                        <small class="text-muted">Recommended: 1080x1080px for Instagram, 1200x630px for Facebook</small>
                    </div>
                    <div id="imagePreview" class="text-center" style="display: none;">
                        <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 300px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Settings -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   <?= $campaign['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned"
                                   <?= $campaign['is_pinned'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_pinned">Pin to Top</label>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Schedule For</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control"
                               value="<?= $campaign['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($campaign['scheduled_at'])) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expires At</label>
                        <input type="datetime-local" name="expires_at" class="form-control"
                               value="<?= $campaign['expires_at'] ? date('Y-m-d\TH:i', strtotime($campaign['expires_at'])) : '' ?>">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Update Campaign
                    </button>
                    <a href="<?= url('admin/social-media/campaigns') ?>" class="btn btn-outline-secondary w-100 mt-2">
                        Cancel
                    </a>
                </div>
            </div>

            <!-- Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Created:</td>
                            <td><?= date('M d, Y H:i', strtotime($campaign['created_at'])) ?></td>
                        </tr>
                        <?php if ($campaign['created_by_name']): ?>
                        <tr>
                            <td class="text-muted">By:</td>
                            <td><?= htmlspecialchars($campaign['created_by_name']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted">Updated:</td>
                            <td><?= date('M d, Y H:i', strtotime($campaign['updated_at'])) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Copy Count:</td>
                            <td><span class="badge bg-info"><?= $campaign['copy_count'] ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Preview -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview</h5>
                </div>
                <div class="card-body">
                    <div id="preview" class="border rounded p-3 bg-light">
                        <div class="d-flex align-items-center mb-2">
                            <?php $currentPlatform = $platforms[$campaign['platform']] ?? $platforms['all']; ?>
                            <div id="platformIcon" class="me-2" style="width: 32px; height: 32px; border-radius: 50%; background: <?= $currentPlatform['color'] ?>; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fa-brands <?= $currentPlatform['icon'] ?>"></i>
                            </div>
                            <strong>Waslah Fashion</strong>
                        </div>
                        <div id="previewContent" class="text-muted small" style="white-space: pre-wrap;"><?= htmlspecialchars($campaign['content']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="<?= url('admin/social-media/campaigns/duplicate/' . $campaign['id']) ?>"
                       class="btn btn-outline-info w-100 mb-2">
                        <i class="fas fa-clone me-2"></i>Duplicate
                    </a>
                    <a href="<?= url('admin/social-media/campaigns/delete/' . $campaign['id'] . '?csrf_token=' . Session::getCsrfToken()) ?>"
                       class="btn btn-outline-danger w-100"
                       onclick="return confirm('Are you sure you want to delete this campaign?')">
                        <i class="fas fa-trash me-2"></i>Delete
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Character counter
document.getElementById('content').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
    document.getElementById('previewContent').textContent = this.value || 'Your message preview will appear here...';
});

// Platform icon update
document.getElementById('platform').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const icon = option.dataset.icon || 'fa-globe';
    const color = option.dataset.color || '#34495e';
    const iconEl = document.getElementById('platformIcon');
    iconEl.style.backgroundColor = color;
    iconEl.innerHTML = `<i class="fa-brands ${icon}"></i>`;
});

// Image preview
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.style.display = 'block';
            preview.querySelector('img').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
