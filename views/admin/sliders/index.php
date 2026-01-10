<div class="page-header">
    <h1>Homepage Sliders</h1>
    <a href="<?= url('admin/sliders/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Slider
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($sliders)): ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <p>No sliders found</p>
            <a href="<?= url('admin/sliders/create') ?>" class="btn btn-primary">Create First Slider</a>
        </div>
        <?php else: ?>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th width="80">Image</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Position</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody id="sliderList">
                <?php foreach ($sliders as $slider): ?>
                <tr data-id="<?= $slider['id'] ?>">
                    <td>
                        <?php if ($slider['image']): ?>
                        <img src="<?= upload('sliders/' . $slider['image']) ?>"
                             alt="<?= sanitize($slider['title']) ?>"
                             class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                        <?php else: ?>
                        <div class="img-placeholder" style="width: 60px; height: 40px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= sanitize($slider['title']) ?></strong>
                    </td>
                    <td>
                        <?php if ($slider['subtitle']): ?>
                        <?= sanitize($slider['subtitle']) ?>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-secondary"><?= ucfirst($slider['text_position']) ?></span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark"><?= $slider['sort_order'] ?></span>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   <?= $slider['status'] === 'active' ? 'checked' : '' ?>
                                   onchange="toggleStatus(<?= $slider['id'] ?>)">
                        </div>
                    </td>
                    <td>
                        <a href="<?= url('admin/sliders/edit/' . $slider['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSlider(<?= $slider['id'] ?>)" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleStatus(id) {
    fetch('<?= url('admin/sliders/toggle') ?>/' + id, { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert('Error: ' + data.message);
                location.reload();
            }
        });
}

function deleteSlider(id) {
    if (confirm('Are you sure you want to delete this slider?')) {
        window.location.href = '<?= url('admin/sliders/delete') ?>/' + id;
    }
}
</script>
