<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('account') ?>">My Account</a></li>
                <li class="breadcrumb-item active">Profile Settings</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <?php include VIEW_PATH . '/user/partials/sidebar.php'; ?>
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                <div class="account-content">
                    <h3 class="mb-4">Profile Settings</h3>

                    <div class="row">
                        <!-- Profile Information -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Personal Information</h5>
                                </div>
                                <div class="card-body">
                                    <form action="<?= url('account/profile') ?>" method="POST">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Full Name *</label>
                                                <input type="text" name="name" class="form-control"
                                                       value="<?= sanitize($user['name'] ?? '') ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" class="form-control"
                                                       value="<?= sanitize($user['email'] ?? '') ?>" disabled>
                                                <small class="text-muted">Email cannot be changed</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone Number</label>
                                                <input type="tel" name="phone" class="form-control"
                                                       value="<?= sanitize($user['phone'] ?? '') ?>"
                                                       placeholder="+880 1XXX-XXXXXX">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Member Since</label>
                                                <input type="text" class="form-control"
                                                       value="<?= formatDate($user['created_at'] ?? '') ?>" disabled>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-2"></i>Save Changes
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Change Password -->
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Change Password</h5>
                                </div>
                                <div class="card-body">
                                    <form action="<?= url('account/password') ?>" method="POST" id="passwordForm">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Current Password *</label>
                                                <input type="password" name="current_password" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">New Password *</label>
                                                <input type="password" name="new_password" class="form-control"
                                                       minlength="6" required>
                                                <small class="text-muted">Minimum 6 characters</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Confirm New Password *</label>
                                                <input type="password" name="confirm_password" class="form-control" required>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-outline-primary">
                                                    <i class="fas fa-lock me-2"></i>Update Password
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Account Summary -->
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                                         style="width: 100px; height: 100px; font-size: 42px;">
                                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <h5 class="mb-1"><?= sanitize($user['name'] ?? 'User') ?></h5>
                                    <p class="text-muted mb-0"><?= sanitize($user['email'] ?? '') ?></p>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">Quick Links</h6>
                                </div>
                                <div class="list-group list-group-flush">
                                    <a href="<?= url('account/orders') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-shopping-bag me-2 text-primary"></i> My Orders
                                    </a>
                                    <a href="<?= url('account/addresses') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-map-marker-alt me-2 text-primary"></i> My Addresses
                                    </a>
                                    <a href="<?= url('wishlist') ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-heart me-2 text-primary"></i> My Wishlist
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPass = this.querySelector('[name="new_password"]').value;
    const confirmPass = this.querySelector('[name="confirm_password"]').value;

    if (newPass !== confirmPass) {
        e.preventDefault();
        alert('New passwords do not match!');
    }
});
</script>
