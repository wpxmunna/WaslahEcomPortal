<?php
/**
 * Employees List View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-user-tie"></i> Employees</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Employees</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/employees/departments') ?>" class="btn btn-secondary">
            <i class="fas fa-building me-1"></i> Departments
        </a>
        <a href="<?= url('admin/employees/create') ?>" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Add Employee
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="quick-stats mb-4">
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-info text-white">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h4><?= $stats['total'] ?? 0 ?></h4>
            <p>Total Employees</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-success text-white">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
            <h4><?= $stats['active'] ?? 0 ?></h4>
            <p>Active</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-warning text-white">
            <i class="fas fa-user-clock"></i>
        </div>
        <div class="stat-content">
            <h4><?= $stats['on_leave'] ?? 0 ?></h4>
            <p>On Leave</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-dark text-white">
            <i class="fas fa-user-minus"></i>
        </div>
        <div class="stat-content">
            <h4><?= $stats['inactive'] ?? 0 ?></h4>
            <p>Inactive</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, phone..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <select name="department" class="form-select">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= ($filters['department_id'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="on_leave" <?= ($filters['status'] ?? '') === 'on_leave' ? 'selected' : '' ?>>On Leave</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Search</button>
                <a href="<?= url('admin/employees') ?>" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Employees Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>ID</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Contact</th>
                    <th>Hire Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-users"></i></div>
                                <h4>No Employees</h4>
                                <p>Get started by adding your first employee</p>
                                <a href="<?= url('admin/employees/create') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Add Employee
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar" style="width: 40px; height: 40px; font-size: 14px; background: <?= $emp['status'] === 'active' ? 'var(--gradient-success)' : 'var(--gradient-dark)' ?>;">
                                        <?= strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'] ?? '', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($emp['first_name'] . ' ' . ($emp['last_name'] ?? '')) ?></strong>
                                        <?php if ($emp['email']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($emp['email']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><code><?= htmlspecialchars($emp['employee_id']) ?></code></td>
                            <td><?= htmlspecialchars($emp['department_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($emp['designation'] ?? '-') ?></td>
                            <td>
                                <?php if ($emp['phone']): ?>
                                    <i class="fas fa-phone text-muted me-1"></i><?= htmlspecialchars($emp['phone']) ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($emp['hire_date'])) ?></td>
                            <td>
                                <?php
                                $statusBadges = [
                                    'active' => 'success',
                                    'on_leave' => 'warning',
                                    'inactive' => 'secondary',
                                    'terminated' => 'danger'
                                ];
                                $badge = $statusBadges[$emp['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst(str_replace('_', ' ', $emp['status'])) ?></span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('admin/employees/view/' . $emp['id']) ?>" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= url('admin/employees/edit/' . $emp['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= url('admin/payroll/employee-salary/' . $emp['id']) ?>" class="btn btn-sm btn-success" title="Salary">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="card-footer">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters ?? [])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
