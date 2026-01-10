<?php
/**
 * Departments Management View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-building"></i> Departments</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/employees') ?>">Employees</a></li>
                <li class="breadcrumb-item active">Departments</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <!-- Add Department Form -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-plus me-2"></i>Add Department
            </div>
            <form action="<?= url('admin/employees/departments/store') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Sales" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department Code</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. SLS">
                        <small class="text-muted">Short code for the department</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description..."></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i>Add Department
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Departments List -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-building me-2"></i>All Departments
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Employees</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($departments)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fas fa-building"></i></div>
                                        <h4>No Departments</h4>
                                        <p>Get started by adding your first department</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($departments as $dept): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($dept['name']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($dept['code']): ?>
                                            <code><?= htmlspecialchars($dept['code']) ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($dept['description'] ?? '-') ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $dept['employee_count'] ?? 0 ?> employees</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= url('admin/employees?department=' . $dept['id']) ?>" class="btn btn-sm btn-info" title="View Employees">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="editDepartment(<?= $dept['id'] ?>, '<?= htmlspecialchars($dept['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($dept['code'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($dept['description'] ?? '', ENT_QUOTES) ?>')" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if (($dept['employee_count'] ?? 0) == 0): ?>
                                                <form action="<?= url('admin/employees/departments/delete/' . $dept['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this department?')">
                                                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editDepartmentForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editDeptName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department Code</label>
                        <input type="text" name="code" id="editDeptCode" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="editDeptDesc" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editDepartment(id, name, code, description) {
    document.getElementById('editDepartmentForm').action = '<?= url('admin/employees/departments/update/') ?>' + id;
    document.getElementById('editDeptName').value = name;
    document.getElementById('editDeptCode').value = code;
    document.getElementById('editDeptDesc').value = description;

    const modal = new bootstrap.Modal(document.getElementById('editDepartmentModal'));
    modal.show();
}
</script>
