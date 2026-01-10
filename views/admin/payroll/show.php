<?php
/**
 * Payroll Period Details View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-file-invoice-dollar"></i> <?= htmlspecialchars($period['period_name']) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/payroll') ?>">Payroll</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </nav>
    </div>
    <div>
        <button type="button" class="btn btn-info" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Print
        </button>
    </div>
</div>

<!-- Period Summary -->
<div class="quick-stats mb-4">
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-info text-white">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h4><?= $period['total_employees'] ?? 0 ?></h4>
            <p>Employees</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-success text-white">
            <i class="fas fa-plus"></i>
        </div>
        <div class="stat-content">
            <h4><?= CURRENCY_SYMBOL ?><?= number_format($period['total_gross'] ?? 0, 2) ?></h4>
            <p>Total Gross</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-danger text-white">
            <i class="fas fa-minus"></i>
        </div>
        <div class="stat-content">
            <h4><?= CURRENCY_SYMBOL ?><?= number_format($period['total_deductions'] ?? 0, 2) ?></h4>
            <p>Deductions</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-primary text-white">
            <i class="fas fa-money-check-alt"></i>
        </div>
        <div class="stat-content">
            <h4><?= CURRENCY_SYMBOL ?><?= number_format($period['total_net'] ?? 0, 2) ?></h4>
            <p>Net Payable</p>
        </div>
    </div>
</div>

<!-- Period Info & Actions -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Period Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl>
                            <dt>Date Range</dt>
                            <dd><?= date('M d, Y', strtotime($period['start_date'])) ?> - <?= date('M d, Y', strtotime($period['end_date'])) ?></dd>
                            <dt>Pay Date</dt>
                            <dd><?= $period['pay_date'] ? date('M d, Y', strtotime($period['pay_date'])) : 'Not set' ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl>
                            <dt>Status</dt>
                            <dd>
                                <?php
                                $statusBadges = [
                                    'draft' => 'secondary',
                                    'processing' => 'warning',
                                    'approved' => 'info',
                                    'paid' => 'success'
                                ];
                                $badge = $statusBadges[$period['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $badge ?> p-2"><?= ucfirst($period['status']) ?></span>
                            </dd>
                            <?php if ($period['processed_by']): ?>
                                <dt>Processed By</dt>
                                <dd><?= htmlspecialchars($period['processed_by_name'] ?? 'N/A') ?> on <?= date('M d, Y', strtotime($period['processed_at'])) ?></dd>
                            <?php endif; ?>
                            <?php if ($period['approved_by']): ?>
                                <dt>Approved By</dt>
                                <dd><?= htmlspecialchars($period['approved_by_name'] ?? 'N/A') ?> on <?= date('M d, Y', strtotime($period['approved_at'])) ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Actions
            </div>
            <div class="card-body">
                <?php if ($period['status'] === 'draft'): ?>
                    <form action="<?= url('admin/payroll/process/' . $period['id']) ?>" method="POST" onsubmit="return confirm('Process payroll for all employees? This will calculate salaries based on attendance and salary structures.')">
                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                        <button type="submit" class="btn btn-warning w-100 btn-lg">
                            <i class="fas fa-cog me-2"></i>Process Payroll
                        </button>
                    </form>
                    <small class="text-muted d-block mt-2">Calculate salaries for all active employees</small>
                <?php elseif ($period['status'] === 'processing'): ?>
                    <form action="<?= url('admin/payroll/approve/' . $period['id']) ?>" method="POST" onsubmit="return confirm('Approve this payroll? This confirms all calculations are correct.')">
                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <i class="fas fa-check me-2"></i>Approve Payroll
                        </button>
                    </form>
                    <small class="text-muted d-block mt-2">Confirm calculations and approve for payment</small>
                <?php elseif ($period['status'] === 'approved'): ?>
                    <form action="<?= url('admin/payroll/mark-paid/' . $period['id']) ?>" method="POST" onsubmit="return confirm('Mark all salaries as paid?')">
                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-money-check-alt me-2"></i>Mark as Paid
                        </button>
                    </form>
                    <small class="text-muted d-block mt-2">Record that salaries have been disbursed</small>
                <?php else: ?>
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle me-2"></i>Payroll completed and paid
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Employee Payroll Details -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2"></i>Employee Payroll Details
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th class="text-center">Working Days</th>
                    <th class="text-center">Present</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Overtime</th>
                    <th class="text-end">Gross</th>
                    <th class="text-end">Deductions</th>
                    <th class="text-end">Net Pay</th>
                    <th class="text-center">Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($details)): ?>
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <?php if ($period['status'] === 'draft'): ?>
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-cog"></i></div>
                                    <h4>Not Processed</h4>
                                    <p>Click "Process Payroll" to calculate salaries</p>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No payroll details found</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($details as $detail): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($detail['first_name'] . ' ' . ($detail['last_name'] ?? '')) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($detail['emp_code'] ?? '') ?></small>
                            </td>
                            <td><?= htmlspecialchars($detail['department_name'] ?? '-') ?></td>
                            <td class="text-center"><?= $detail['working_days'] ?></td>
                            <td class="text-center">
                                <span class="badge bg-success"><?= $detail['present_days'] ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($detail['absent_days'] > 0): ?>
                                    <span class="badge bg-danger"><?= $detail['absent_days'] ?></span>
                                <?php else: ?>
                                    <span class="text-muted">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($detail['overtime_hours'] > 0): ?>
                                    <span class="badge bg-warning"><?= number_format($detail['overtime_hours'], 1) ?>h</span>
                                    <br><small class="text-success">+<?= CURRENCY_SYMBOL ?><?= number_format($detail['overtime_amount'], 2) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($detail['gross_earnings'], 2) ?></td>
                            <td class="text-end text-danger">-<?= CURRENCY_SYMBOL ?><?= number_format($detail['total_deductions'], 2) ?></td>
                            <td class="text-end"><strong><?= CURRENCY_SYMBOL ?><?= number_format($detail['net_salary'], 2) ?></strong></td>
                            <td class="text-center">
                                <?php if (($detail['payment_status'] ?? 'pending') === 'paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= url('admin/payroll/payslip/' . $detail['id']) ?>" class="btn btn-sm btn-info" title="View Payslip">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    .page-header, .btn, form {
        display: none !important;
    }
}
</style>
