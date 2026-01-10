<?php
/**
 * Attendance List View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-calendar-check"></i> Attendance</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Attendance</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/attendance/today') ?>" class="btn btn-success">
            <i class="fas fa-calendar-day me-1"></i> Today
        </a>
        <a href="<?= url('admin/attendance/monthly-report') ?>" class="btn btn-info">
            <i class="fas fa-chart-bar me-1"></i> Report
        </a>
    </div>
</div>

<!-- Today's Summary -->
<div class="quick-stats mb-4">
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-success text-white">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
            <h4><?= $todaySummary['present'] ?? 0 ?></h4>
            <p>Present Today</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-warning text-white">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h4><?= $todaySummary['late'] ?? 0 ?></h4>
            <p>Late Today</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-danger text-white">
            <i class="fas fa-user-times"></i>
        </div>
        <div class="stat-content">
            <h4><?= $todaySummary['absent'] ?? 0 ?></h4>
            <p>Absent Today</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-info text-white">
            <i class="fas fa-plane-departure"></i>
        </div>
        <div class="stat-content">
            <h4><?= $todaySummary['on_leave'] ?? 0 ?></h4>
            <p>On Leave</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Employee</label>
                <select name="employee" class="form-select">
                    <option value="">All Employees</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>" <?= ($filters['employee_id'] ?? '') == $emp['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['first_name'] . ' ' . ($emp['last_name'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Month</label>
                <input type="month" name="month" class="form-control" value="<?= $filters['month'] ?? date('Y-m') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="present" <?= ($filters['status'] ?? '') === 'present' ? 'selected' : '' ?>>Present</option>
                    <option value="late" <?= ($filters['status'] ?? '') === 'late' ? 'selected' : '' ?>>Late</option>
                    <option value="absent" <?= ($filters['status'] ?? '') === 'absent' ? 'selected' : '' ?>>Absent</option>
                    <option value="leave" <?= ($filters['status'] ?? '') === 'leave' ? 'selected' : '' ?>>Leave</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Filter</button>
                <a href="<?= url('admin/attendance') ?>" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Records -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-calendar-check me-2"></i>Attendance Records
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Work Hours</th>
                    <th>Overtime</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-calendar-check"></i></div>
                                <h4>No Records</h4>
                                <p>No attendance records found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td>
                                <strong><?= date('M d', strtotime($record['attendance_date'])) ?></strong><br>
                                <small class="text-muted"><?= date('l', strtotime($record['attendance_date'])) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($record['first_name'] . ' ' . ($record['last_name'] ?? '')) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($record['employee_id'] ?? '') ?></small>
                            </td>
                            <td>
                                <?php if ($record['check_in']): ?>
                                    <span class="text-success"><?= date('h:i A', strtotime($record['check_in'])) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($record['check_out']): ?>
                                    <span class="text-danger"><?= date('h:i A', strtotime($record['check_out'])) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($record['work_hours']): ?>
                                    <?= number_format($record['work_hours'], 1) ?> hrs
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($record['overtime_hours'] > 0): ?>
                                    <span class="badge bg-warning">+<?= number_format($record['overtime_hours'], 1) ?> hrs</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusBadges = [
                                    'present' => 'success',
                                    'late' => 'warning',
                                    'absent' => 'danger',
                                    'leave' => 'info',
                                    'half_day' => 'secondary'
                                ];
                                $badge = $statusBadges[$record['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst(str_replace('_', ' ', $record['status'])) ?></span>
                            </td>
                            <td>
                                <?= htmlspecialchars($record['notes'] ?? '') ?>
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
                            <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
