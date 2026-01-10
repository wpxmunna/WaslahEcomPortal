<div class="page-header">
    <h1>Lookbook Gallery</h1>
    <a href="<?= url('admin/lookbook/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Image
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($items)): ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <p>No lookbook images found</p>
            <a href="<?= url('admin/lookbook/create') ?>" class="btn btn-primary">Add First Image</a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="100">Image</th>
                        <th>Caption</th>
                        <th>Link</th>
                        <th width="80">Featured</th>
                        <th width="80">Order</th>
                        <th width="80">Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php
                            $imgSrc = filter_var($item['image'], FILTER_VALIDATE_URL)
                                ? $item['image']
                                : upload('lookbook/' . $item['image']);
                            ?>
                            <img src="<?= $imgSrc ?>"
                                 alt="<?= sanitize($item['caption'] ?? 'Lookbook') ?>"
                                 class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                        </td>
                        <td>
                            <?php if ($item['caption']): ?>
                            <?= sanitize($item['caption']) ?>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item['link']): ?>
                            <a href="<?= sanitize($item['link']) ?>" target="_blank" class="text-truncate d-inline-block" style="max-width: 150px;">
                                <?= sanitize($item['link']) ?>
                            </a>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item['is_featured']): ?>
                            <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> Featured</span>
                            <?php else: ?>
                            <button class="btn btn-sm btn-outline-warning" onclick="setFeatured(<?= $item['id'] ?>)" title="Set as Featured">
                                <i class="far fa-star"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark"><?= $item['sort_order'] ?></span>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       <?= $item['status'] === 'active' ? 'checked' : '' ?>
                                       onchange="toggleStatus(<?= $item['id'] ?>)">
                            </div>
                        </td>
                        <td>
                            <a href="<?= url('admin/lookbook/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteItem(<?= $item['id'] ?>)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleStatus(id) {
    fetch('<?= url('admin/lookbook/toggle') ?>/' + id, { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert('Error: ' + data.message);
                location.reload();
            }
        });
}

function setFeatured(id) {
    fetch('<?= url('admin/lookbook/featured') ?>/' + id, { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
}

function deleteItem(id) {
    if (confirm('Are you sure you want to delete this image?')) {
        window.location.href = '<?= url('admin/lookbook/delete') ?>/' + id;
    }
}
</script>
