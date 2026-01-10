<?php
/**
 * Create Payroll Period View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-calendar-plus"></i> Create Payroll Period</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/payroll') ?>">Payroll</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-calendar-plus me-2"></i>New Payroll Period
            </div>
            <form action="<?= url('admin/payroll/store') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Period Name <span class="text-danger">*</span></label>
                        <input type="text" name="period_name" class="form-control" value="<?= htmlspecialchars($suggestedName) ?>" required>
                        <small class="text-muted">e.g., "January 2026", "Q1 2026"</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="<?= $suggestedStart ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" value="<?= $suggestedEnd ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pay Date</label>
                        <input type="date" name="pay_date" class="form-control">
                        <small class="text-muted">The date when salaries will be disbursed</small>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-1"></i>What happens next?</h6>
                        <ol class="mb-0">
                            <li>This creates a payroll period in <strong>Draft</strong> status</li>
                            <li>Click <strong>Process</strong> to calculate salaries for all employees</li>
                            <li>Review and <strong>Approve</strong> the payroll</li>
                            <li><strong>Mark as Paid</strong> after disbursing salaries</li>
                        </ol>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= url('admin/payroll') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Period
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
