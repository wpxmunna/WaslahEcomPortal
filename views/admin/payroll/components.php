<?php
/**
 * Salary Components Management View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-cogs"></i> Salary Components</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/payroll') ?>">Payroll</a></li>
                <li class="breadcrumb-item active">Components</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <!-- Add Component Form -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-plus me-2"></i>Add Salary Component
            </div>
            <form action="<?= url('admin/payroll/components/store') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Component Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. House Rent Allowance" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="earning">Earning (Adds to salary)</option>
                            <option value="deduction">Deduction (Subtracts from salary)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Calculation Type</label>
                        <select name="calculation_type" class="form-select" id="calcType">
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage of Basic</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Default Amount/Percentage</label>
                        <div class="input-group">
                            <input type="number" name="default_amount" class="form-control" value="0" min="0" step="0.01">
                            <span class="input-group-text" id="amount-suffix"><?= CURRENCY_SYMBOL ?></span>
                        </div>
                        <small class="text-muted">Leave 0 to set per employee</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_taxable" class="form-check-input" id="isTaxable" value="1">
                            <label class="form-check-label" for="isTaxable">Taxable Component</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i>Add Component
                    </button>
                </div>
            </form>
        </div>

        <!-- Info -->
        <div class="card mb-4 bg-light">
            <div class="card-body">
                <h6><i class="fas fa-info-circle me-1"></i>About Salary Components</h6>
                <ul class="mb-0 small">
                    <li><strong>Earnings:</strong> Allowances, bonuses, etc. that add to salary</li>
                    <li><strong>Deductions:</strong> Tax, insurance, loans that subtract from salary</li>
                    <li><strong>Fixed:</strong> Same amount every month</li>
                    <li><strong>Percentage:</strong> Calculated as % of basic salary</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Components List -->
    <div class="col-lg-8">
        <!-- Earnings -->
        <div class="card mb-4 border-success">
            <div class="card-header bg-success text-white">
                <i class="fas fa-plus-circle me-2"></i>Earning Components
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Calculation</th>
                            <th>Default Value</th>
                            <th>Taxable</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $earnings = array_filter($components, fn($c) => $c['type'] === 'earning');
                        if (empty($earnings)):
                        ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No earning components</td>
                            </tr>
                        <?php else: foreach ($earnings as $comp): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($comp['name']) ?></strong></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= $comp['calculation_type'] === 'percentage' ? 'Percentage' : 'Fixed' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($comp['calculation_type'] === 'percentage'): ?>
                                        <?= number_format($comp['default_amount'], 2) ?>%
                                    <?php else: ?>
                                        <?= CURRENCY_SYMBOL ?><?= number_format($comp['default_amount'], 2) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $comp['is_taxable'] ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="editComponent(<?= htmlspecialchars(json_encode($comp)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Deductions -->
        <div class="card mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-minus-circle me-2"></i>Deduction Components
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Calculation</th>
                            <th>Default Value</th>
                            <th>Taxable</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $deductions = array_filter($components, fn($c) => $c['type'] === 'deduction');
                        if (empty($deductions)):
                        ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No deduction components</td>
                            </tr>
                        <?php else: foreach ($deductions as $comp): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($comp['name']) ?></strong></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= $comp['calculation_type'] === 'percentage' ? 'Percentage' : 'Fixed' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($comp['calculation_type'] === 'percentage'): ?>
                                        <?= number_format($comp['default_amount'], 2) ?>%
                                    <?php else: ?>
                                        <?= CURRENCY_SYMBOL ?><?= number_format($comp['default_amount'], 2) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $comp['is_taxable'] ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="editComponent(<?= htmlspecialchars(json_encode($comp)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Component Modal -->
<div class="modal fade" id="editComponentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editComponentForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Component</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Component Name</label>
                        <input type="text" name="name" id="editCompName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Calculation Type</label>
                        <select name="calculation_type" id="editCompCalc" class="form-select">
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage of Basic</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Default Amount/Percentage</label>
                        <input type="number" name="default_amount" id="editCompAmount" class="form-control" min="0" step="0.01">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_taxable" class="form-check-input" id="editCompTaxable" value="1">
                            <label class="form-check-label" for="editCompTaxable">Taxable Component</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Component</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('calcType').addEventListener('change', function() {
    document.getElementById('amount-suffix').textContent = this.value === 'percentage' ? '%' : '<?= CURRENCY_SYMBOL ?>';
});

function editComponent(comp) {
    document.getElementById('editComponentForm').action = '<?= url('admin/payroll/components/update/') ?>' + comp.id;
    document.getElementById('editCompName').value = comp.name;
    document.getElementById('editCompCalc').value = comp.calculation_type;
    document.getElementById('editCompAmount').value = comp.default_amount;
    document.getElementById('editCompTaxable').checked = comp.is_taxable == 1;

    const modal = new bootstrap.Modal(document.getElementById('editComponentModal'));
    modal.show();
}
</script>
