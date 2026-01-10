<?php
/**
 * Employee Salary Structure View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-money-check-alt"></i> Salary Structure</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/employees') ?>">Employees</a></li>
                <li class="breadcrumb-item active">Salary</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <!-- Employee Info -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.5rem; background: var(--gradient-primary);">
                    <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'] ?? '', 0, 1)) ?>
                </div>
                <h4 class="mb-1"><?= htmlspecialchars($employee['first_name'] . ' ' . ($employee['last_name'] ?? '')) ?></h4>
                <p class="text-muted mb-3"><?= htmlspecialchars($employee['designation'] ?? '') ?></p>

                <table class="table table-sm text-start">
                    <tr>
                        <td><strong>Employee ID</strong></td>
                        <td class="text-end"><code><?= htmlspecialchars($employee['employee_id']) ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Basic Salary</strong></td>
                        <td class="text-end text-primary fw-bold"><?= CURRENCY_SYMBOL ?><?= number_format($employee['basic_salary'] ?? 0, 2) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Employment Type</strong></td>
                        <td class="text-end"><?= ucfirst(str_replace('_', ' ', $employee['employment_type'] ?? '-')) ?></td>
                    </tr>
                </table>

                <a href="<?= url('admin/employees/view/' . $employee['id']) ?>" class="btn btn-info w-100">
                    <i class="fas fa-user me-1"></i> View Profile
                </a>
            </div>
        </div>

        <!-- Add Component Form -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="fas fa-plus me-2"></i>Add Component
            </div>
            <form action="<?= url('admin/payroll/employee-salary/update/' . $employee['id']) ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Select Component <span class="text-danger">*</span></label>
                        <select name="component_id" class="form-select" required>
                            <option value="">-- Select --</option>
                            <?php foreach ($components as $comp): ?>
                                <?php
                                // Check if already added
                                $isAdded = false;
                                foreach ($structure as $s) {
                                    if ($s['component_id'] == $comp['id']) {
                                        $isAdded = true;
                                        break;
                                    }
                                }
                                ?>
                                <option value="<?= $comp['id'] ?>" <?= $isAdded ? 'disabled' : '' ?>>
                                    <?= htmlspecialchars($comp['name']) ?>
                                    (<?= ucfirst($comp['type']) ?>)
                                    <?= $isAdded ? ' - Already Added' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                            <input type="number" name="amount" class="form-control" min="0" step="0.01" required>
                        </div>
                        <small class="text-muted">For percentage components, enter the percentage value</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Effective From</label>
                        <input type="date" name="effective_from" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-plus me-1"></i>Add Component
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Salary Structure -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-money-check-alt me-2"></i>Current Salary Structure
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Component</th>
                            <th>Type</th>
                            <th>Calculation</th>
                            <th class="text-end">Amount</th>
                            <th>Effective From</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Basic Salary (always shown) -->
                        <tr class="table-primary">
                            <td><strong>Basic Salary</strong></td>
                            <td><span class="badge bg-success">Earning</span></td>
                            <td>Fixed</td>
                            <td class="text-end"><strong><?= CURRENCY_SYMBOL ?><?= number_format($employee['basic_salary'] ?? 0, 2) ?></strong></td>
                            <td><?= date('M d, Y', strtotime($employee['hire_date'])) ?></td>
                            <td>
                                <a href="<?= url('admin/employees/edit/' . $employee['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                            </td>
                        </tr>
                        <?php
                        $totalEarnings = $employee['basic_salary'] ?? 0;
                        $totalDeductions = 0;

                        if (empty($structure)):
                        ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    No additional components added yet
                                </td>
                            </tr>
                        <?php else: foreach ($structure as $comp):
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
                                <td><?= ucfirst($comp['calculation_type']) ?></td>
                                <td class="text-end <?= $comp['type'] === 'deduction' ? 'text-danger' : '' ?>">
                                    <?= $comp['type'] === 'deduction' ? '-' : '' ?><?= CURRENCY_SYMBOL ?><?= number_format($comp['amount'], 2) ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($comp['effective_from'])) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="editSalaryComponent(<?= $comp['component_id'] ?>, <?= $comp['amount'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-success text-white">
                            <th colspan="3">Total Earnings</th>
                            <th class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($totalEarnings, 2) ?></th>
                            <th colspan="2"></th>
                        </tr>
                        <tr class="bg-danger text-white">
                            <th colspan="3">Total Deductions</th>
                            <th class="text-end">-<?= CURRENCY_SYMBOL ?><?= number_format($totalDeductions, 2) ?></th>
                            <th colspan="2"></th>
                        </tr>
                        <tr class="table-primary">
                            <th colspan="3" class="h5">Net Salary</th>
                            <th class="text-end h5"><?= CURRENCY_SYMBOL ?><?= number_format($totalEarnings - $totalDeductions, 2) ?></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card bg-light">
            <div class="card-body">
                <h6><i class="fas fa-info-circle me-1"></i>How Salary is Calculated</h6>
                <ul class="mb-0 small">
                    <li><strong>Basic Salary:</strong> Set in employee profile</li>
                    <li><strong>Earnings:</strong> All earning components are added</li>
                    <li><strong>Deductions:</strong> All deduction components are subtracted</li>
                    <li><strong>Overtime:</strong> Calculated based on attendance (1.5x hourly rate)</li>
                    <li><strong>Absences:</strong> Unpaid leave days are deducted proportionally from basic</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Edit Salary Component Modal -->
<div class="modal fade" id="editSalaryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('admin/payroll/employee-salary/update/' . $employee['id']) ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <input type="hidden" name="component_id" id="editCompId">
                <div class="modal-header">
                    <h5 class="modal-title">Update Salary Component</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">New Amount</label>
                        <div class="input-group">
                            <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                            <input type="number" name="amount" id="editCompAmount" class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Effective From</label>
                        <input type="date" name="effective_from" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="alert alert-info mb-0">
                        <small><i class="fas fa-info-circle me-1"></i>This will create a new record with the updated amount. Previous records are kept for historical reference.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSalaryComponent(componentId, currentAmount) {
    document.getElementById('editCompId').value = componentId;
    document.getElementById('editCompAmount').value = currentAmount;

    const modal = new bootstrap.Modal(document.getElementById('editSalaryModal'));
    modal.show();
}
</script>
