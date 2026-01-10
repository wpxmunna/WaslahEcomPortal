<?php
/**
 * Edit Employee View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-user-edit"></i> Edit Employee</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/employees') ?>">Employees</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('admin/employees/view/' . $employee['id']) ?>" class="btn btn-info">
            <i class="fas fa-eye me-1"></i> View Profile
        </a>
    </div>
</div>

<form action="<?= url('admin/employees/update/' . $employee['id']) ?>" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

    <div class="row">
        <!-- Personal Information -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-2"></i>Personal Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($employee['first_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($employee['last_name'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="<?= $employee['date_of_birth'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select</option>
                                    <option value="male" <?= ($employee['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                    <option value="female" <?= ($employee['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                    <option value="other" <?= ($employee['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">National ID / NID</label>
                        <input type="text" name="national_id" class="form-control" value="<?= htmlspecialchars($employee['national_id'] ?? '') ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($employee['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($employee['phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($employee['address'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($employee['city'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-phone-alt me-2"></i>Emergency Contact
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="form-control" value="<?= htmlspecialchars($employee['emergency_contact_name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" class="form-control" value="<?= htmlspecialchars($employee['emergency_contact_phone'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-briefcase me-2"></i>Employment Information
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Employee ID</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($employee['employee_id']) ?>" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-select">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept['id'] ?>" <?= ($employee['department_id'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dept['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" class="form-control" value="<?= htmlspecialchars($employee['designation'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Employment Type</label>
                                <select name="employment_type" class="form-select">
                                    <option value="full_time" <?= ($employee['employment_type'] ?? '') === 'full_time' ? 'selected' : '' ?>>Full Time</option>
                                    <option value="part_time" <?= ($employee['employment_type'] ?? '') === 'part_time' ? 'selected' : '' ?>>Part Time</option>
                                    <option value="contract" <?= ($employee['employment_type'] ?? '') === 'contract' ? 'selected' : '' ?>>Contract</option>
                                    <option value="intern" <?= ($employee['employment_type'] ?? '') === 'intern' ? 'selected' : '' ?>>Intern</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?= ($employee['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="on_leave" <?= ($employee['status'] ?? '') === 'on_leave' ? 'selected' : '' ?>>On Leave</option>
                                    <option value="inactive" <?= ($employee['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="terminated" <?= ($employee['status'] ?? '') === 'terminated' ? 'selected' : '' ?>>Terminated</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hire Date <span class="text-danger">*</span></label>
                                <input type="date" name="hire_date" class="form-control" value="<?= $employee['hire_date'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Termination Date</label>
                                <input type="date" name="termination_date" class="form-control" value="<?= $employee['termination_date'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Basic Salary</label>
                        <div class="input-group">
                            <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                            <input type="number" name="basic_salary" class="form-control" value="<?= $employee['basic_salary'] ?? 0 ?>" min="0" step="0.01">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-university me-2"></i>Payment Information
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($employee['bank_name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank Account Number</label>
                        <input type="text" name="bank_account" class="form-control" value="<?= htmlspecialchars($employee['bank_account'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Banking (bKash/Nagad)</label>
                        <input type="text" name="mobile_banking" class="form-control" value="<?= htmlspecialchars($employee['mobile_banking'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-sticky-note me-2"></i>Notes
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($employee['notes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between">
            <a href="<?= url('admin/employees') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Update Employee
            </button>
        </div>
    </div>
</form>
