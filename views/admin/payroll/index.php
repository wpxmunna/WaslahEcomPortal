<?php
/**
 * Payroll Periods List View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-file-invoice-dollar"></i> Payroll</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Payroll</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/payroll/create') ?>" class="btn btn-success">
            <i class="fas fa-plus me-1"></i>Create Payroll Period
        </a>
        <a href="<?= url('admin/payroll/components') ?>" class="btn btn-info">
            <i class="fas fa-cogs me-1"></i>Salary Components
        </a>
    </div>
</div>

<!-- Payroll Periods -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-calendar-alt me-2"></i>Payroll Periods
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Date Range</th>
                    <th>Employees</th>
                    <th>Gross</th>
                    <th>Deductions</th>
                    <th>Net Pay</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($periods)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                <h4>No Payroll Periods</h4>
                                <p>Get started by creating your first payroll period</p>
                                <a href="<?= url('admin/payroll/create') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Create First Payroll
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($periods as $period): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($period['period_name']) ?></strong>
                                <?php if ($period['pay_date']): ?>
                                    <br><small class="text-muted">Pay Date: <?= date('M d, Y', strtotime($period['pay_date'])) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= date('M d', strtotime($period['start_date'])) ?> - <?= date('M d, Y', strtotime($period['end_date'])) ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= $period['total_employees'] ?? 0 ?> employees</span>
                            </td>
                            <td><?= CURRENCY_SYMBOL ?><?= number_format($period['total_gross'] ?? 0, 2) ?></td>
                            <td class="text-danger">-<?= CURRENCY_SYMBOL ?><?= number_format($period['total_deductions'] ?? 0, 2) ?></td>
                            <td><strong><?= CURRENCY_SYMBOL ?><?= number_format($period['total_net'] ?? 0, 2) ?></strong></td>
                            <td>
                                <?php
                                $statusBadges = [
                                    'draft' => 'secondary',
                                    'processing' => 'warning',
                                    'approved' => 'info',
                                    'paid' => 'success'
                                ];
                                $badge = $statusBadges[$period['status']] ?? 'secondary';
                                $statusLabels = [
                                    'draft' => 'Draft',
                                    'processing' => 'Processed',
                                    'approved' => 'Approved',
                                    'paid' => 'Paid'
                                ];
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= $statusLabels[$period['status']] ?? $period['status'] ?></span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('admin/payroll/view/' . $period['id']) ?>" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($period['status'] === 'draft'): ?>
                                        <form action="<?= url('admin/payroll/process/' . $period['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Process payroll for all employees?')">
                                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                            <button type="submit" class="btn btn-sm btn-warning" title="Process">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($period['status'] === 'processing'): ?>
                                        <form action="<?= url('admin/payroll/approve/' . $period['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Approve this payroll?')">
                                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($period['status'] === 'approved'): ?>
                                        <form action="<?= url('admin/payroll/mark-paid/' . $period['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Mark all salaries as paid?')">
                                            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                            <button type="submit" class="btn btn-sm btn-primary" title="Mark as Paid">
                                                <i class="fas fa-money-check-alt"></i>
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
