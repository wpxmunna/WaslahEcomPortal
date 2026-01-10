<?php
/**
 * Today's Attendance View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-calendar-day"></i> Today's Attendance</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/attendance') ?>">Attendance</a></li>
                <li class="breadcrumb-item active">Today</li>
            </ol>
        </nav>
    </div>
    <div>
        <span class="badge bg-secondary p-2"><i class="fas fa-calendar me-1"></i><?= date('l, F d, Y') ?></span>
    </div>
</div>

<!-- Summary Cards -->
<div class="quick-stats mb-4">
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-success text-white">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
            <h4><?= $summary['present'] ?? 0 ?></h4>
            <p>Present</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-warning text-white">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h4><?= $summary['late'] ?? 0 ?></h4>
            <p>Late</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-dark text-white">
            <i class="fas fa-user-clock"></i>
        </div>
        <div class="stat-content">
            <h4><?= $summary['not_checked_in'] ?? 0 ?></h4>
            <p>Not Checked In</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-info text-white">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h4><?= $summary['total_active'] ?? 0 ?></h4>
            <p>Total Active</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mb-4">
    <form action="<?= url('admin/attendance/mark-absent') ?>" method="POST" class="d-inline" onsubmit="return confirm('Mark all employees who have not checked in as absent?')">
        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
        <input type="hidden" name="date" value="<?= $today ?>">
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-user-times me-1"></i>Mark Remaining as Absent
        </button>
    </form>
</div>

<!-- Employees List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-users me-2"></i>Employees</span>
        <form action="<?= url('admin/attendance/bulk-check-in') ?>" method="POST" id="bulkCheckInForm">
            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
            <button type="submit" class="btn btn-success btn-sm" id="bulkCheckInBtn" disabled>
                <i class="fas fa-check-double me-1"></i>Check In Selected (<span id="selectedCount">0</span>)
            </button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 30px;">
                        <input type="checkbox" class="form-check-input" id="selectAll" onclick="toggleSelectAll()">
                    </th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-users"></i></div>
                                <h4>No Employees</h4>
                                <p>No employees found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($employees as $emp): ?>
                        <?php
                        $hasCheckedIn = !empty($emp['check_in']);
                        $hasCheckedOut = !empty($emp['check_out']);
                        $status = $emp['attendance_status'] ?? 'not_checked';
                        ?>
                        <tr>
                            <td>
                                <?php if (!$hasCheckedIn): ?>
                                    <input type="checkbox" class="form-check-input employee-checkbox" name="employee_ids[]" form="bulkCheckInForm" value="<?= $emp['id'] ?>" onchange="updateSelectedCount()">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width: 35px; height: 35px; font-size: 12px; background: <?= $hasCheckedIn ? 'var(--gradient-success)' : 'var(--gradient-dark)' ?>;">
                                        <?= strtoupper(substr($emp['first_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($emp['first_name'] . ' ' . ($emp['last_name'] ?? '')) ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($emp['designation'] ?? '') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($emp['department_name'] ?? '-') ?></td>
                            <td>
                                <?php if ($hasCheckedIn): ?>
                                    <span class="text-success fw-bold">
                                        <i class="fas fa-sign-in-alt me-1"></i>
                                        <?= date('h:i A', strtotime($emp['check_in'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Not checked in</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($hasCheckedOut): ?>
                                    <span class="text-danger fw-bold">
                                        <i class="fas fa-sign-out-alt me-1"></i>
                                        <?= date('h:i A', strtotime($emp['check_out'])) ?>
                                    </span>
                                <?php elseif ($hasCheckedIn): ?>
                                    <span class="badge bg-success">Working</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($status === 'present'): ?>
                                    <span class="badge bg-success">Present</span>
                                <?php elseif ($status === 'late'): ?>
                                    <span class="badge bg-warning">Late</span>
                                <?php elseif ($status === 'absent'): ?>
                                    <span class="badge bg-danger">Absent</span>
                                <?php elseif ($status === 'leave'): ?>
                                    <span class="badge bg-info">On Leave</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$hasCheckedIn): ?>
                                    <form action="<?= url('admin/attendance/check-in') ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                        <input type="hidden" name="employee_id" value="<?= $emp['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-sign-in-alt"></i> Check In
                                        </button>
                                    </form>
                                <?php elseif (!$hasCheckedOut): ?>
                                    <form action="<?= url('admin/attendance/check-out') ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                        <input type="hidden" name="employee_id" value="<?= $emp['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-sign-out-alt"></i> Check Out
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-success"><i class="fas fa-check"></i> Complete</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.employee-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.employee-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = checked;
    document.getElementById('bulkCheckInBtn').disabled = checked === 0;
}
</script>
