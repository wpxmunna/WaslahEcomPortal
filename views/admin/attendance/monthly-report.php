<?php
/**
 * Monthly Attendance Report View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-bar"></i> Monthly Attendance Report</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/attendance') ?>">Attendance</a></li>
                <li class="breadcrumb-item active">Monthly Report</li>
            </ol>
        </nav>
    </div>
    <div>
        <button type="button" class="btn btn-info" onclick="window.print()">
            <i class="fas fa-print me-1"></i>Print Report
        </button>
    </div>
</div>

<!-- Month Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Select Month</label>
                <input type="month" name="month" class="form-control" value="<?= $month ?>" onchange="this.form.submit()">
            </div>
        </form>
    </div>
</div>

<!-- Report Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-chart-bar me-2"></i>
        Attendance Summary - <?= date('F Y', strtotime($month . '-01')) ?>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th class="text-center bg-success text-white">Present</th>
                    <th class="text-center bg-warning">Late</th>
                    <th class="text-center bg-danger text-white">Absent</th>
                    <th class="text-center bg-info text-white">Leave</th>
                    <th class="text-center">Total Hours</th>
                    <th class="text-center">Overtime</th>
                    <th class="text-center">Attendance %</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($report)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-chart-bar"></i></div>
                                <h4>No Data</h4>
                                <p>No data for this month</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $totalPresent = 0;
                    $totalLate = 0;
                    $totalAbsent = 0;
                    $totalLeave = 0;
                    $totalHours = 0;
                    $totalOvertime = 0;

                    // Assume 26 working days per month for percentage
                    $workingDays = 26;

                    foreach ($report as $row):
                        $stats = $row['stats'];
                        $emp = $row['employee'];

                        $totalPresent += $stats['present'];
                        $totalLate += $stats['late'];
                        $totalAbsent += $stats['absent'];
                        $totalLeave += $stats['leave'];
                        $totalHours += $stats['total_hours'];
                        $totalOvertime += $stats['overtime'];

                        $attendanceRate = $workingDays > 0 ? round(($stats['present'] / $workingDays) * 100, 1) : 0;
                    ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($emp['first_name'] . ' ' . ($emp['last_name'] ?? '')) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($emp['employee_id'] ?? '') ?></small>
                            </td>
                            <td><?= htmlspecialchars($emp['department_name'] ?? '-') ?></td>
                            <td class="text-center">
                                <span class="badge bg-success"><?= $stats['present'] ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($stats['late'] > 0): ?>
                                    <span class="badge bg-warning"><?= $stats['late'] ?></span>
                                <?php else: ?>
                                    <span class="text-muted">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($stats['absent'] > 0): ?>
                                    <span class="badge bg-danger"><?= $stats['absent'] ?></span>
                                <?php else: ?>
                                    <span class="text-muted">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($stats['leave'] > 0): ?>
                                    <span class="badge bg-info"><?= $stats['leave'] ?></span>
                                <?php else: ?>
                                    <span class="text-muted">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= number_format($stats['total_hours'], 1) ?> hrs</td>
                            <td class="text-center">
                                <?php if ($stats['overtime'] > 0): ?>
                                    <span class="text-success">+<?= number_format($stats['overtime'], 1) ?> hrs</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $badgeClass = 'success';
                                if ($attendanceRate < 70) $badgeClass = 'danger';
                                elseif ($attendanceRate < 85) $badgeClass = 'warning';
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= $attendanceRate ?>%</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($report)): ?>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2">TOTAL / AVERAGE</td>
                        <td class="text-center"><?= $totalPresent ?></td>
                        <td class="text-center"><?= $totalLate ?></td>
                        <td class="text-center"><?= $totalAbsent ?></td>
                        <td class="text-center"><?= $totalLeave ?></td>
                        <td class="text-center"><?= number_format($totalHours, 1) ?> hrs</td>
                        <td class="text-center"><?= number_format($totalOvertime, 1) ?> hrs</td>
                        <td class="text-center">
                            <?php
                            $avgRate = count($report) > 0 ? round($totalPresent / (count($report) * $workingDays) * 100, 1) : 0;
                            ?>
                            <?= $avgRate ?>%
                        </td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Legend -->
<div class="card">
    <div class="card-body">
        <h6 class="mb-2">Legend:</h6>
        <span class="badge bg-success me-2">Present</span> = Days employee was present (including late)
        <span class="badge bg-warning mx-2">Late</span> = Days employee arrived late
        <span class="badge bg-danger mx-2">Absent</span> = Days employee was absent without leave
        <span class="badge bg-info mx-2">Leave</span> = Approved leave days
    </div>
</div>

<style>
@media print {
    .page-header, .card-body form, .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
