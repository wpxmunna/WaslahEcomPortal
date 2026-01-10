<?php
/**
 * Employee Payslip View
 */
?>

<div class="page-header no-print">
    <div>
        <h1><i class="fas fa-file-invoice"></i> Payslip</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/payroll') ?>">Payroll</a></li>
                <li class="breadcrumb-item active">Payslip</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/payroll/view/' . $payslip['payroll_period_id']) ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Payroll
        </a>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print me-1"></i>Print Payslip
        </button>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body" id="payslip-content">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h3 class="mb-1"><?= htmlspecialchars($storeName ?? 'COMPANY NAME') ?></h3>
                    <p class="text-muted mb-0"><?= htmlspecialchars($storeAddress ?? '') ?></p>
                    <hr>
                    <h4>SALARY SLIP</h4>
                    <p class="mb-0"><strong><?= htmlspecialchars($payslip['period_name']) ?></strong></p>
                    <small class="text-muted"><?= date('M d, Y', strtotime($payslip['start_date'])) ?> - <?= date('M d, Y', strtotime($payslip['end_date'])) ?></small>
                </div>

                <!-- Employee Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="fw-bold" width="40%">Employee Name:</td>
                                <td><?= htmlspecialchars($payslip['first_name'] . ' ' . ($payslip['last_name'] ?? '')) ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Employee ID:</td>
                                <td><?= htmlspecialchars($payslip['emp_code'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Department:</td>
                                <td><?= htmlspecialchars($payslip['department_name'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Designation:</td>
                                <td><?= htmlspecialchars($payslip['designation'] ?? '-') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="fw-bold" width="40%">Working Days:</td>
                                <td><?= $payslip['working_days'] ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Present Days:</td>
                                <td><?= $payslip['present_days'] ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Absent Days:</td>
                                <td><?= $payslip['absent_days'] ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Overtime Hours:</td>
                                <td><?= number_format($payslip['overtime_hours'], 1) ?> hrs</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Earnings & Deductions -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">Earnings</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Basic Salary</td>
                                            <td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($payslip['basic_salary'], 2) ?></td>
                                        </tr>
                                        <?php
                                        $totalEarnings = $payslip['basic_salary'];
                                        foreach ($payslip['components'] as $comp):
                                            if ($comp['component_type'] === 'earning'):
                                                $totalEarnings += $comp['amount'];
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($comp['component_name']) ?></td>
                                                <td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($comp['amount'], 2) ?></td>
                                            </tr>
                                        <?php endif; endforeach; ?>
                                        <?php if ($payslip['overtime_amount'] > 0): ?>
                                            <tr>
                                                <td>Overtime</td>
                                                <td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($payslip['overtime_amount'], 2) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot class="bg-success text-white">
                                        <tr>
                                            <th>Total Earnings</th>
                                            <th class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($payslip['gross_earnings'], 2) ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title mb-0">Deductions</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <?php
                                        $hasDeductions = false;
                                        foreach ($payslip['components'] as $comp):
                                            if ($comp['component_type'] === 'deduction'):
                                                $hasDeductions = true;
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($comp['component_name']) ?></td>
                                                <td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($comp['amount'], 2) ?></td>
                                            </tr>
                                        <?php endif; endforeach; ?>
                                        <?php if (!$hasDeductions): ?>
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">No deductions</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot class="bg-danger text-white">
                                        <tr>
                                            <th>Total Deductions</th>
                                            <th class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($payslip['total_deductions'], 2) ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Net Pay -->
                <div class="alert alert-primary text-center mt-4">
                    <h4 class="mb-0">
                        Net Salary: <strong><?= CURRENCY_SYMBOL ?><?= number_format($payslip['net_salary'], 2) ?></strong>
                    </h4>
                </div>

                <!-- Payment Info -->
                <div class="row mb-4">
                    <div class="col-12">
                        <table class="table table-bordered table-sm">
                            <tr>
                                <td class="fw-bold bg-light" width="20%">Payment Method</td>
                                <td><?= ucfirst(str_replace('_', ' ', $payslip['payment_method'])) ?></td>
                                <td class="fw-bold bg-light" width="20%">Payment Status</td>
                                <td>
                                    <?php if (($payslip['payment_status'] ?? 'pending') === 'paid'): ?>
                                        <span class="badge bg-success">Paid</span>
                                        <?php if ($payslip['paid_at']): ?>
                                            on <?= date('M d, Y', strtotime($payslip['paid_at'])) ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if ($payslip['bank_name']): ?>
                                <tr>
                                    <td class="fw-bold bg-light">Bank</td>
                                    <td><?= htmlspecialchars($payslip['bank_name']) ?></td>
                                    <td class="fw-bold bg-light">Account</td>
                                    <td><?= htmlspecialchars($payslip['bank_account'] ?? '-') ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($payslip['mobile_banking']): ?>
                                <tr>
                                    <td class="fw-bold bg-light">Mobile Banking</td>
                                    <td colspan="3"><?= htmlspecialchars($payslip['mobile_banking']) ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <!-- Signature -->
                <div class="row mt-5">
                    <div class="col-6 text-center">
                        <div class="border-top pt-2">
                            <strong>Employee Signature</strong>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="border-top pt-2">
                            <strong>Authorized Signature</strong>
                        </div>
                    </div>
                </div>

                <div class="text-center text-muted mt-4">
                    <small>This is a computer-generated document. No signature is required.</small><br>
                    <small>Generated on <?= date('M d, Y h:i A') ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    #payslip-content {
        padding: 0 !important;
    }
    body {
        font-size: 12px;
    }
}
</style>
