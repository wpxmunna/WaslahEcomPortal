<?php
/**
 * Social Media Manager - Index View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-share-alt"></i> Social Media Manager</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Social Media</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('admin/social-media/campaigns') ?>" class="btn btn-outline-primary me-2">
            <i class="fas fa-bullhorn me-2"></i>Campaign Messages
        </a>
        <a href="<?= url('admin/social-media/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Link
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
                        <h6 class="text-white-50 mb-1">Total Links</h6>
                        <h3 class="mb-0"><?= count($socialLinks) ?></h3>
                    </div>
                    <i class="fas fa-link fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Active Links</h6>
                        <h3 class="mb-0"><?= count(array_filter($socialLinks, fn($s) => $s['is_active'])) ?></h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">In Header</h6>
                        <h3 class="mb-0"><?= count(array_filter($socialLinks, fn($s) => $s['show_in_header'] && $s['is_active'])) ?></h3>
                    </div>
                    <i class="fas fa-arrow-up fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">In Footer</h6>
                        <h3 class="mb-0"><?= count(array_filter($socialLinks, fn($s) => $s['show_in_footer'] && $s['is_active'])) ?></h3>
                    </div>
                    <i class="fas fa-arrow-down fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Social Media Links Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Social Media Links</h5>
        <small class="text-muted">Drag to reorder</small>
    </div>
    <div class="card-body p-0">
        <?php if (empty($socialLinks)): ?>
            <div class="text-center py-5">
                <i class="fas fa-share-alt fa-4x text-muted mb-3"></i>
                <h5>No Social Media Links</h5>
                <p class="text-muted">Add your first social media link to display on your site.</p>
                <a href="<?= url('admin/social-media/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Social Link
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="socialLinksTable">
                    <thead class="table-light">
                        <tr>
                            <th width="40"><i class="fas fa-grip-vertical text-muted"></i></th>
                            <th width="60">Icon</th>
                            <th>Platform</th>
                            <th>URL</th>
                            <th class="text-center">Header</th>
                            <th class="text-center">Footer</th>
                            <th class="text-center">Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortableLinks">
                        <?php foreach ($socialLinks as $link): ?>
                            <tr data-id="<?= $link['id'] ?>">
                                <td class="drag-handle" style="cursor: grab;">
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </td>
                                <td>
                                    <div class="social-icon-preview" style="background-color: <?= htmlspecialchars($link['color']) ?>; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-<?= $link['icon_style'] ?> <?= htmlspecialchars($link['icon']) ?> text-white"></i>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($link['name']) ?></strong>
                                    <small class="text-muted d-block"><?= htmlspecialchars($link['platform']) ?></small>
                                </td>
                                <td>
                                    <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" class="text-truncate d-inline-block" style="max-width: 250px;">
                                        <?= htmlspecialchars($link['url']) ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <?php if ($link['show_in_header']): ?>
                                        <span class="badge bg-info">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($link['show_in_footer']): ?>
                                        <span class="badge bg-info">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               <?= $link['is_active'] ? 'checked' : '' ?>
                                               onchange="toggleStatus(<?= $link['id'] ?>)">
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= url('admin/social-media/edit/' . $link['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url('admin/social-media/delete/' . $link['id'] . '?csrf_token=' . Session::getCsrfToken()) ?>"
                                           class="btn btn-outline-danger"
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this link?')">
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

<!-- Preview Section -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Header Icons</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <?php foreach ($socialLinks as $link): ?>
                        <?php if ($link['is_active'] && $link['show_in_header']): ?>
                            <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank"
                               class="social-icon-btn"
                               style="background-color: <?= htmlspecialchars($link['color']) ?>;"
                               title="<?= htmlspecialchars($link['name']) ?>">
                                <i class="fa-<?= $link['icon_style'] ?> <?= htmlspecialchars($link['icon']) ?>"></i>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (empty(array_filter($socialLinks, fn($s) => $s['show_in_header'] && $s['is_active']))): ?>
                        <span class="text-muted">No icons enabled for header</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Footer Icons</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <?php foreach ($socialLinks as $link): ?>
                        <?php if ($link['is_active'] && $link['show_in_footer']): ?>
                            <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank"
                               class="social-icon-btn"
                               style="background-color: <?= htmlspecialchars($link['color']) ?>;"
                               title="<?= htmlspecialchars($link['name']) ?>">
                                <i class="fa-<?= $link['icon_style'] ?> <?= htmlspecialchars($link['icon']) ?>"></i>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (empty(array_filter($socialLinks, fn($s) => $s['show_in_footer'] && $s['is_active']))): ?>
                        <span class="text-muted">No icons enabled for footer</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.social-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}
.social-icon-btn:hover {
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.drag-handle:hover {
    cursor: grab;
}
.sortable-ghost {
    opacity: 0.4;
    background: #f0f0f0;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Initialize sortable
document.addEventListener('DOMContentLoaded', function() {
    const sortableList = document.getElementById('sortableLinks');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                const order = [];
                sortableList.querySelectorAll('tr').forEach((row, index) => {
                    order.push(parseInt(row.dataset.id));
                });
                updateOrder(order);
            }
        });
    }
});

function toggleStatus(id) {
    fetch(`<?= url('admin/social-media/toggle/') ?>${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert(data.message);
                location.reload();
            }
        });
}

function updateOrder(order) {
    fetch('<?= url('admin/social-media/update-order') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order: order })
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            alert(data.message);
        }
    });
}
</script>
