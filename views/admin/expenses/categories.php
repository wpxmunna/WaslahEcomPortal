<div class="page-header">
    <h1>Expense Categories</h1>
    <a href="<?= url('admin/expenses') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Expenses
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Categories List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Categories</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($categories)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No categories found</h5>
                    <p class="text-muted">Add your first expense category</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Category</th>
                                <th>Description</th>
                                <th class="text-center">Expenses</th>
                                <th class="text-end">Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <span class="badge me-2" style="background-color: <?= $cat['color'] ?>">
                                        <i class="fas fa-<?= $cat['icon'] ?>"></i>
                                    </span>
                                    <strong><?= sanitize($cat['name']) ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted"><?= sanitize($cat['description'] ?: '-') ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $cat['expense_count'] ?></span>
                                </td>
                                <td class="text-end">
                                    <?= formatPrice($cat['total_amount']) ?>
                                </td>
                                <td>
                                    <?php if ($cat['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($cat['expense_count'] == 0): ?>
                                        <button type="button" class="btn btn-outline-danger" onclick="deleteCategory(<?= $cat['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
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
    </div>

    <div class="col-lg-4">
        <!-- Add Category Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0" id="formTitle">Add New Category</h5>
            </div>
            <div class="card-body">
                <form id="categoryForm" action="<?= url('admin/expenses/categories/store') ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    <input type="hidden" name="category_id" id="categoryId" value="">

                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="categoryName" class="form-control" required placeholder="e.g., Office Supplies">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="categoryDescription" class="form-control" rows="2" placeholder="Brief description..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Color</label>
                            <input type="color" name="color" id="categoryColor" class="form-control form-control-color w-100" value="#6c757d">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Icon</label>
                            <select name="icon" id="categoryIcon" class="form-select">
                                <option value="tag">Tag</option>
                                <option value="building">Building</option>
                                <option value="users">Users</option>
                                <option value="bullhorn">Marketing</option>
                                <option value="paperclip">Supplies</option>
                                <option value="truck">Shipping</option>
                                <option value="university">Bank</option>
                                <option value="laptop">Software</option>
                                <option value="car">Transport</option>
                                <option value="wrench">Maintenance</option>
                                <option value="bolt">Utilities</option>
                                <option value="shield-alt">Insurance</option>
                                <option value="phone">Phone</option>
                                <option value="wifi">Internet</option>
                                <option value="ellipsis-h">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3" id="statusField" style="display: none;">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="categoryActive" value="1" checked>
                            <label class="form-check-label" for="categoryActive">Active</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> <span id="submitText">Add Category</span>
                    </button>

                    <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="cancelEdit" style="display: none;" onclick="resetForm()">
                        Cancel Edit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editCategory(cat) {
    document.getElementById('formTitle').textContent = 'Edit Category';
    document.getElementById('submitText').textContent = 'Update Category';
    document.getElementById('categoryId').value = cat.id;
    document.getElementById('categoryName').value = cat.name;
    document.getElementById('categoryDescription').value = cat.description || '';
    document.getElementById('categoryColor').value = cat.color;
    document.getElementById('categoryIcon').value = cat.icon;
    document.getElementById('categoryActive').checked = cat.is_active == 1;
    document.getElementById('statusField').style.display = 'block';
    document.getElementById('cancelEdit').style.display = 'block';

    document.getElementById('categoryForm').action = '<?= url('admin/expenses/categories/update') ?>/' + cat.id;
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Add New Category';
    document.getElementById('submitText').textContent = 'Add Category';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryDescription').value = '';
    document.getElementById('categoryColor').value = '#6c757d';
    document.getElementById('categoryIcon').value = 'tag';
    document.getElementById('categoryActive').checked = true;
    document.getElementById('statusField').style.display = 'none';
    document.getElementById('cancelEdit').style.display = 'none';
    document.getElementById('categoryForm').action = '<?= url('admin/expenses/categories/store') ?>';
}

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        window.location.href = '<?= url('admin/expenses/categories/delete') ?>/' + id;
    }
}
</script>
