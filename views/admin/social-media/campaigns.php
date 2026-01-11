<?php
/**
 * Campaign Messages - Index View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-bullhorn"></i> Campaign Messages</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/social-media') ?>">Social Media</a></li>
                <li class="breadcrumb-item active">Campaigns</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('admin/social-media/insights') ?>" class="btn btn-outline-info me-2">
            <i class="fas fa-chart-line me-2"></i>Insights
        </a>
        <a href="<?= url('admin/social-media') ?>" class="btn btn-outline-secondary me-2">
            <i class="fas fa-share-alt me-2"></i>Social Links
        </a>
        <a href="<?= url('admin/social-media/campaigns/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Message
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Messages</h6>
                        <h3 class="mb-0"><?= $stats['total'] ?></h3>
                    </div>
                    <i class="fas fa-comments fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Active</h6>
                        <h3 class="mb-0"><?= $stats['active'] ?></h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-dark-50 mb-1">Pinned</h6>
                        <h3 class="mb-0"><?= $stats['pinned'] ?></h3>
                    </div>
                    <i class="fas fa-thumbtack fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Copies</h6>
                        <h3 class="mb-0"><?= $stats['total_copies'] ?></h3>
                    </div>
                    <i class="fas fa-copy fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Platform</label>
                <select name="platform" class="form-select" onchange="this.form.submit()">
                    <option value="">All Platforms</option>
                    <?php foreach ($platforms as $key => $platform): ?>
                    <option value="<?= $key ?>" <?= $currentPlatform === $key ? 'selected' : '' ?>>
                        <?= $platform['name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Message Type</label>
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <?php foreach ($messageTypes as $key => $type): ?>
                    <option value="<?= $key ?>" <?= $currentType === $key ? 'selected' : '' ?>>
                        <?= $type['name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <?php if ($currentPlatform || $currentType): ?>
                <a href="<?= url('admin/social-media/campaigns') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear Filters
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Campaign Messages List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Campaign Messages</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($campaigns)): ?>
        <div class="text-center py-5">
            <i class="fas fa-bullhorn fa-4x text-muted mb-3"></i>
            <h5>No Campaign Messages</h5>
            <p class="text-muted">Create your first campaign message for social media.</p>
            <a href="<?= url('admin/social-media/campaigns/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Message
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50"></th>
                        <th>Title</th>
                        <th>Platform</th>
                        <th>Type</th>
                        <th class="text-center">Copies</th>
                        <th class="text-center">Status</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campaigns as $campaign): ?>
                    <tr class="<?= $campaign['is_pinned'] ? 'table-warning' : '' ?>">
                        <td class="text-center">
                            <?php if ($campaign['is_pinned']): ?>
                            <i class="fas fa-thumbtack text-warning" title="Pinned"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($campaign['title']) ?></strong>
                            <?php if ($campaign['short_content']): ?>
                            <small class="text-muted d-block text-truncate" style="max-width: 300px;">
                                <?= htmlspecialchars($campaign['short_content']) ?>
                            </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $platform = $platforms[$campaign['platform']] ?? null; ?>
                            <?php if ($platform): ?>
                            <span class="badge" style="background-color: <?= $platform['color'] ?>">
                                <i class="fa-brands <?= $platform['icon'] ?> me-1"></i>
                                <?= $platform['name'] ?>
                            </span>
                            <?php else: ?>
                            <span class="badge bg-secondary"><?= $campaign['platform'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $type = $messageTypes[$campaign['message_type']] ?? null; ?>
                            <?php if ($type): ?>
                            <span class="badge" style="background-color: <?= $type['color'] ?>">
                                <i class="fas <?= $type['icon'] ?> me-1"></i>
                                <?= $type['name'] ?>
                            </span>
                            <?php else: ?>
                            <span class="badge bg-secondary"><?= $campaign['message_type'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark"><?= $campaign['copy_count'] ?></span>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       <?= $campaign['is_active'] ? 'checked' : '' ?>
                                       onchange="toggleCampaign(<?= $campaign['id'] ?>)">
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('admin/social-media/campaigns/performance/' . $campaign['id']) ?>"
                                   class="btn btn-outline-primary" title="Performance">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <button type="button" class="btn btn-outline-success" title="Copy Content"
                                        onclick="copyContent(<?= $campaign['id'] ?>)">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button type="button" class="btn btn-outline-warning" title="Toggle Pin"
                                        onclick="togglePin(<?= $campaign['id'] ?>)">
                                    <i class="fas fa-thumbtack"></i>
                                </button>
                                <a href="<?= url('admin/social-media/campaigns/edit/' . $campaign['id']) ?>"
                                   class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= url('admin/social-media/campaigns/duplicate/' . $campaign['id']) ?>"
                                   class="btn btn-outline-info" title="Duplicate">
                                    <i class="fas fa-clone"></i>
                                </a>
                                <a href="<?= url('admin/social-media/campaigns/delete/' . $campaign['id'] . '?csrf_token=' . Session::getCsrfToken()) ?>"
                                   class="btn btn-outline-danger" title="Delete"
                                   onclick="return confirm('Delete this campaign message?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Copy Modal -->
<div class="modal fade" id="copyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-copy me-2"></i>Copy Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Full Message</label>
                    <textarea id="copyContent" class="form-control" rows="6" readonly></textarea>
                    <button type="button" class="btn btn-sm btn-primary mt-2" onclick="copyToClipboard('copyContent')">
                        <i class="fas fa-copy me-1"></i>Copy Full Message
                    </button>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Short Version</label>
                    <textarea id="copyShort" class="form-control" rows="2" readonly></textarea>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="copyToClipboard('copyShort')">
                        <i class="fas fa-copy me-1"></i>Copy Short Version
                    </button>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold">Hashtags</label>
                    <input type="text" id="copyHashtags" class="form-control" readonly>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="copyToClipboard('copyHashtags')">
                        <i class="fas fa-copy me-1"></i>Copy Hashtags
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCampaign(id) {
    fetch(`<?= url('admin/social-media/campaigns/toggle/') ?>${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert(data.message);
                location.reload();
            }
        });
}

function togglePin(id) {
    fetch(`<?= url('admin/social-media/campaigns/pin/') ?>${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
}

function copyContent(id) {
    fetch(`<?= url('admin/social-media/campaigns/copy/') ?>${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('copyContent').value = data.content || '';
                document.getElementById('copyShort').value = data.short_content || '';
                document.getElementById('copyHashtags').value = data.hashtags || '';
                new bootstrap.Modal(document.getElementById('copyModal')).show();
            } else {
                alert(data.message);
            }
        });
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    document.execCommand('copy');

    // Show feedback
    const btn = element.nextElementSibling;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
    btn.classList.remove('btn-primary', 'btn-outline-primary');
    btn.classList.add('btn-success');

    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-success');
        btn.classList.add(originalText.includes('Full') ? 'btn-primary' : 'btn-outline-primary');
    }, 2000);
}
</script>
