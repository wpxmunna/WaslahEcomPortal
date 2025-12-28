<section class="auth-page">
    <div class="container">
        <div class="auth-card">
            <h2>Create Account</h2>
            <p class="text-center text-muted mb-4">Join Waslah today</p>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('register') ?>">
                <?= csrfField() ?>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="name" required
                           value="<?= $old['name'] ?? '' ?>" placeholder="Enter your name">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" required
                           value="<?= $old['email'] ?? '' ?>" placeholder="Enter your email">
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" name="phone"
                           value="<?= $old['phone'] ?? '' ?>" placeholder="Enter your phone">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required
                           placeholder="Minimum 6 characters" minlength="6">
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirm" required
                           placeholder="Confirm your password">
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#" class="text-accent">Terms of Service</a> and
                        <a href="#" class="text-accent">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-user-plus me-2"></i> Create Account
                </button>
            </form>

            <div class="auth-divider">
                <span>or</span>
            </div>

            <p class="text-center mb-0">
                Already have an account? <a href="<?= url('login') ?>" class="text-accent">Login</a>
            </p>
        </div>
    </div>
</section>
