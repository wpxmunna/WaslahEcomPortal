<section class="auth-page">
    <div class="container">
        <div class="auth-card">
            <h2>Welcome Back</h2>
            <p class="text-center text-muted mb-4">Login to your account</p>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= url('login') ?>">
                <?= csrfField() ?>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" required
                           value="<?= old('email') ?>" placeholder="Enter your email">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required
                           placeholder="Enter your password">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="#" class="text-accent">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>
            </form>

            <div class="auth-divider">
                <span>or</span>
            </div>

            <p class="text-center mb-0">
                Don't have an account? <a href="<?= url('register') ?>" class="text-accent">Create one</a>
            </p>
        </div>
    </div>
</section>
