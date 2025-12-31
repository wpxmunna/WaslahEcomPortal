<div class="page-header">
    <h1>Add Admin User</h1>
    <a href="<?= url('admin/users') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<form action="<?= url('admin/users/store') ?>" method="POST">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">User Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="name" required
                                   placeholder="John Doe">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" class="form-control" name="email" required
                                   placeholder="admin@example.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone"
                               placeholder="+1 234 567 890">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" name="password" required
                                   minlength="6" placeholder="Minimum 6 characters">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control" name="confirm_password" required
                                   minlength="6" placeholder="Confirm password">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Role & Status</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select class="form-select" name="role" required>
                            <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">
                            Administrators have full access. Managers have limited access.
                        </small>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" checked>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                </div>
            </div>

            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <h6><i class="fas fa-shield-alt text-primary me-2"></i> Security Tips</h6>
                    <ul class="small text-muted mb-0">
                        <li>Use a strong, unique password</li>
                        <li>Only grant admin role when necessary</li>
                        <li>Regularly review admin user list</li>
                        <li>Disable accounts instead of deleting when possible</li>
                    </ul>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-user-plus me-2"></i> Create Admin User
            </button>
        </div>
    </div>
</form>
