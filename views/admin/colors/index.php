<div class="page-header">
    <h1>Product Colors</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addColorModal">
        <i class="fas fa-plus me-2"></i> Add Color
    </button>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($colors)): ?>
        <div class="text-center py-5">
            <i class="fas fa-palette fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-3">No colors configured yet</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addColorModal">
                <i class="fas fa-plus me-2"></i> Add Your First Color
            </button>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="60">Color</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody id="colorList">
                    <?php foreach ($colors as $color): ?>
                    <tr data-id="<?= $color['id'] ?>">
                        <td>
                            <span class="color-preview" style="background: <?= $color['color_code'] ?>; <?= $color['color_code'] === '#FFFFFF' ? 'border: 2px solid #ddd;' : '' ?>"></span>
                        </td>
                        <td class="fw-bold"><?= sanitize($color['name']) ?></td>
                        <td><code><?= $color['color_code'] ?></code></td>
                        <td>
                            <span class="badge <?= $color['status'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $color['status'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                    onclick="editColor(<?= htmlspecialchars(json_encode($color)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="<?= url('admin/colors/delete/' . $color['id']) ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="Are you sure you want to delete this color?">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Color Modal -->
<div class="modal fade" id="addColorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('admin/colors/store') ?>" method="POST">
                <?= csrfField() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Color</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Color Name *</label>
                        <input type="text" class="form-control" name="name" required
                               placeholder="e.g., Navy Blue, Coral Red">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color Code *</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="addColorPicker"
                                   value="#000000" title="Choose color">
                            <input type="text" class="form-control" name="color_code" id="addColorCode"
                                   value="#000000" pattern="^#[0-9A-Fa-f]{6}$" required
                                   placeholder="#000000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preview</label>
                        <div class="color-preview-large" id="addColorPreview" style="background: #000000;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Color</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Color Modal -->
<div class="modal fade" id="editColorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="editColorForm">
                <?= csrfField() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Color</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Color Name *</label>
                        <input type="text" class="form-control" name="name" id="editColorName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color Code *</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="editColorPicker"
                                   value="#000000" title="Choose color">
                            <input type="text" class="form-control" name="color_code" id="editColorCode"
                                   pattern="^#[0-9A-Fa-f]{6}$" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preview</label>
                        <div class="color-preview-large" id="editColorPreview"></div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status" id="editColorStatus" checked>
                            <label class="form-check-label" for="editColorStatus">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Color</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.color-preview {
    display: inline-block;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.color-preview-large {
    width: 100%;
    height: 60px;
    border-radius: 8px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #ddd;
}
.form-control-color {
    width: 60px;
    padding: 0.375rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add color picker sync
    const addColorPicker = document.getElementById('addColorPicker');
    const addColorCode = document.getElementById('addColorCode');
    const addColorPreview = document.getElementById('addColorPreview');

    addColorPicker.addEventListener('input', function() {
        addColorCode.value = this.value.toUpperCase();
        addColorPreview.style.background = this.value;
    });

    addColorCode.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            addColorPicker.value = this.value;
            addColorPreview.style.background = this.value;
        }
    });

    // Edit color picker sync
    const editColorPicker = document.getElementById('editColorPicker');
    const editColorCode = document.getElementById('editColorCode');
    const editColorPreview = document.getElementById('editColorPreview');

    editColorPicker.addEventListener('input', function() {
        editColorCode.value = this.value.toUpperCase();
        editColorPreview.style.background = this.value;
    });

    editColorCode.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            editColorPicker.value = this.value;
            editColorPreview.style.background = this.value;
        }
    });
});

function editColor(color) {
    document.getElementById('editColorForm').action = '<?= url('admin/colors/update/') ?>' + color.id;
    document.getElementById('editColorName').value = color.name;
    document.getElementById('editColorCode').value = color.color_code;
    document.getElementById('editColorPicker').value = color.color_code;
    document.getElementById('editColorPreview').style.background = color.color_code;
    document.getElementById('editColorStatus').checked = color.status == 1;

    new bootstrap.Modal(document.getElementById('editColorModal')).show();
}
</script>
