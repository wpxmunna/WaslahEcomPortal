<?php
/**
 * Employee Details View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-user-tie"></i> <?= htmlspecialchars($employee['first_name'] . ' ' . ($employee['last_name'] ?? '')) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/employees') ?>">Employees</a></li>
                <li class="breadcrumb-item active">View</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/employees/edit/' . $employee['id']) ?>" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="<?= url('admin/payroll/employee-salary/' . $employee['id']) ?>" class="btn btn-success">
            <i class="fas fa-money-bill-wave me-1"></i> Salary
        </a>
    </div>
</div>

<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2rem; background: <?= $employee['status'] === 'active' ? 'var(--gradient-success)' : 'var(--gradient-dark)' ?>;">
                    <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'] ?? '', 0, 1)) ?>
                </div>
                <h4 class="mb-1"><?= htmlspecialchars($employee['first_name'] . ' ' . ($employee['last_name'] ?? '')) ?></h4>
                <p class="text-muted mb-3"><?= htmlspecialchars($employee['designation'] ?? 'No Designation') ?></p>

                <table class="table table-sm text-start">
                    <tr>
                        <td class="fw-bold">Employee ID</td>
                        <td class="text-end"><code><?= htmlspecialchars($employee['employee_id']) ?></code></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Department</td>
                        <td class="text-end"><?= htmlspecialchars($employee['department_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Status</td>
                        <td class="text-end">
                            <?php
                            $statusBadges = [
                                'active' => 'success',
                                'on_leave' => 'warning',
                                'inactive' => 'secondary',
                                'terminated' => 'danger'
                            ];
                            $badge = $statusBadges[$employee['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $badge ?>"><?= ucfirst(str_replace('_', ' ', $employee['status'])) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Employment Type</td>
                        <td class="text-end"><?= ucfirst(str_replace('_', ' ', $employee['employment_type'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Hire Date</td>
                        <td class="text-end"><?= date('M d, Y', strtotime($employee['hire_date'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-address-card me-2"></i>Contact Information
            </div>
            <div class="card-body">
                <?php if ($employee['email']): ?>
                    <p><i class="fas fa-envelope text-muted me-2"></i><?= htmlspecialchars($employee['email']) ?></p>
                <?php endif; ?>
                <?php if ($employee['phone']): ?>
                    <p><i class="fas fa-phone text-muted me-2"></i><?= htmlspecialchars($employee['phone']) ?></p>
                <?php endif; ?>
                <?php if ($employee['address']): ?>
                    <p><i class="fas fa-map-marker-alt text-muted me-2"></i><?= htmlspecialchars($employee['address']) ?><?= $employee['city'] ? ', ' . htmlspecialchars($employee['city']) : '' ?></p>
                <?php endif; ?>
                <?php if ($employee['emergency_contact_name']): ?>
                    <hr>
                    <strong class="text-danger"><i class="fas fa-phone-alt me-1"></i> Emergency Contact</strong>
                    <p class="mb-0"><?= htmlspecialchars($employee['emergency_contact_name']) ?></p>
                    <p><?= htmlspecialchars($employee['emergency_contact_phone'] ?? '') ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-university me-2"></i>Payment Information
            </div>
            <div class="card-body">
                <p><strong>Basic Salary:</strong> <?= CURRENCY_SYMBOL ?><?= number_format($employee['basic_salary'] ?? 0, 2) ?></p>
                <?php if ($employee['bank_name']): ?>
                    <p><strong>Bank:</strong> <?= htmlspecialchars($employee['bank_name']) ?></p>
                    <p><strong>Account:</strong> <?= htmlspecialchars($employee['bank_account'] ?? '') ?></p>
                <?php endif; ?>
                <?php if ($employee['mobile_banking']): ?>
                    <p><strong>Mobile Banking:</strong> <?= htmlspecialchars($employee['mobile_banking']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Details -->
    <div class="col-md-8">
        <!-- Attendance Summary -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-calendar-check me-2"></i>This Month's Attendance</span>
                <a href="<?= url('admin/attendance?employee=' . $employee['id']) ?>" class="btn btn-sm btn-info">
                    View All
                </a>
            </div>
            <div class="card-body">
                <?php
                $present = 0;
                $late = 0;
                $absent = 0;
                $leave = 0;
                $totalHours = 0;
                foreach ($monthlyAttendance as $att) {
                    if ($att['status'] === 'present') $present++;
                    elseif ($att['status'] === 'late') { $present++; $late++; }
                    elseif ($att['status'] === 'absent') $absent++;
                    elseif ($att['status'] === 'leave') $leave++;
                    $totalHours += $att['work_hours'] ?? 0;
                }
                ?>
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="h2 text-success mb-0"><?= $present ?></div>
                        <small class="text-muted">Present</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h2 text-warning mb-0"><?= $late ?></div>
                        <small class="text-muted">Late</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h2 text-danger mb-0"><?= $absent ?></div>
                        <small class="text-muted">Absent</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h2 text-info mb-0"><?= $leave ?></div>
                        <small class="text-muted">Leave</small>
                    </div>
                </div>
                <hr>
                <p class="text-center mb-0">
                    <strong>Total Hours Worked:</strong> <?= number_format($totalHours, 1) ?> hours
                </p>
            </div>
        </div>

        <!-- Salary Structure -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-money-check-alt me-2"></i>Salary Structure</span>
                <a href="<?= url('admin/payroll/employee-salary/' . $employee['id']) ?>" class="btn btn-sm btn-success">
                    <i class="fas fa-edit me-1"></i> Manage
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Component</th>
                            <th>Type</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td><span class="badge bg-success">Earning</span></td>
                            <td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($employee['basic_salary'] ?? 0, 2) ?></td>
                        </tr>
                        <?php
                        $totalEarnings = $employee['basic_salary'] ?? 0;
                        $totalDeductions = 0;
                        foreach ($salaryStructure as $comp):
                            if ($comp['type'] === 'earning') {
                                $totalEarnings += $comp['amount'];
                            } else {
                                $totalDeductions += $comp['amount'];
                            }
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($comp['name']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $comp['type'] === 'earning' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($comp['type']) ?>
                                    </span>
                                </td>
                                <td class="text-end <?= $comp['type'] === 'deduction' ? 'text-danger' : '' ?>">
                                    <?= $comp['type'] === 'deduction' ? '-' : '' ?><?= CURRENCY_SYMBOL ?><?= number_format($comp['amount'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2">Total Earnings</th>
                            <th class="text-end text-success"><?= CURRENCY_SYMBOL ?><?= number_format($totalEarnings, 2) ?></th>
                        </tr>
                        <tr>
                            <th colspan="2">Total Deductions</th>
                            <th class="text-end text-danger">-<?= CURRENCY_SYMBOL ?><?= number_format($totalDeductions, 2) ?></th>
                        </tr>
                        <tr class="table-primary">
                            <th colspan="2">Net Salary</th>
                            <th class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($totalEarnings - $totalDeductions, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Personal Info -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-id-card me-2"></i>Personal Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Date of Birth</dt>
                            <dd class="col-sm-7"><?= $employee['date_of_birth'] ? date('M d, Y', strtotime($employee['date_of_birth'])) : '-' ?></dd>
                            <dt class="col-sm-5">Gender</dt>
                            <dd class="col-sm-7"><?= ucfirst($employee['gender'] ?? '-') ?></dd>
                            <dt class="col-sm-5">National ID</dt>
                            <dd class="col-sm-7"><?= htmlspecialchars($employee['national_id'] ?? '-') ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">Created</dt>
                            <dd class="col-sm-7"><?= date('M d, Y', strtotime($employee['created_at'])) ?></dd>
                            <dt class="col-sm-5">Updated</dt>
                            <dd class="col-sm-7"><?= date('M d, Y', strtotime($employee['updated_at'] ?? $employee['created_at'])) ?></dd>
                        </dl>
                    </div>
                </div>
                <?php if ($employee['notes']): ?>
                    <hr>
                    <strong>Notes:</strong>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($employee['notes'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
