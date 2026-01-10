<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Waslah</title>
    <meta name="theme-color" content="#0F2027">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #0F2027;
            --primary-mid: #203A43;
            --primary-light: #2C5364;
            --accent-gold: #D4AF37;
            --accent-gold-light: #F5D061;
            --white: #FFFFFF;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-400: #9CA3AF;
            --gray-600: #4B5563;
            --gray-800: #1F2937;
            --success: #10B981;
            --danger: #EF4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            background: var(--gray-50);
            overflow-x: hidden;
        }

        /* Main Container */
        .login-container {
            display: flex;
            min-height: 100vh;
        }

        /* Left Panel - Branding */
        .login-branding {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-mid) 50%, var(--primary-light) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .login-branding::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            animation: patternMove 30s linear infinite;
        }

        @keyframes patternMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }

        /* Floating Shapes */
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--accent-gold-light) 100%);
            opacity: 0.1;
            animation: float 20s ease-in-out infinite;
        }

        .floating-shape:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .floating-shape:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: -50px;
            animation-delay: -5s;
        }

        .floating-shape:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 20%;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, -20px) rotate(5deg); }
            50% { transform: translate(-10px, 20px) rotate(-5deg); }
            75% { transform: translate(-20px, -10px) rotate(3deg); }
        }

        .branding-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 400px;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: var(--white);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .brand-logo img {
            width: 50px;
            height: auto;
        }

        .brand-logo i {
            font-size: 36px;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .branding-content h1 {
            font-size: 42px;
            font-weight: 800;
            color: var(--white);
            letter-spacing: 4px;
            margin-bottom: 12px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .branding-content .tagline {
            font-size: 14px;
            color: var(--accent-gold);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 40px;
            font-weight: 600;
        }

        .branding-content p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            margin-bottom: 40px;
        }

        .features-list {
            text-align: left;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            color: rgba(255, 255, 255, 0.9);
        }

        .feature-item i {
            width: 40px;
            height: 40px;
            background: rgba(212, 175, 55, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-gold);
            font-size: 16px;
        }

        .feature-item span {
            font-size: 14px;
            font-weight: 500;
        }

        /* Right Panel - Login Form */
        .login-form-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            background: var(--white);
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .form-header {
            margin-bottom: 40px;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 8px;
        }

        .form-header p {
            font-size: 15px;
            color: var(--gray-600);
        }

        /* Alert Styles */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .alert-danger i {
            color: var(--danger);
        }

        .alert-success {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 16px 16px 16px 48px;
            font-size: 15px;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            background: var(--gray-50);
            transition: all 0.3s ease;
            color: var(--gray-800);
        }

        .form-control::placeholder {
            color: var(--gray-400);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-gold);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
        }

        .form-control:focus + i,
        .input-wrapper:focus-within i {
            color: var(--accent-gold);
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 4px;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--gray-600);
        }

        /* Remember Me & Forgot Password */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent-gold);
            cursor: pointer;
        }

        .remember-me span {
            font-size: 14px;
            color: var(--gray-600);
        }

        .forgot-password {
            font-size: 14px;
            color: var(--accent-gold);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--primary-mid);
        }

        /* Submit Button */
        .btn-login {
            width: 100%;
            padding: 16px 24px;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary-dark);
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--accent-gold-light) 100%);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .btn-login i {
            font-size: 16px;
            transition: transform 0.3s ease;
        }

        .btn-login:hover i {
            transform: translateX(4px);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 32px 0;
            gap: 16px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-200);
        }

        .divider span {
            font-size: 12px;
            color: var(--gray-400);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Back to Store */
        .back-to-store {
            text-align: center;
        }

        .back-to-store a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--gray-600);
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 12px 24px;
            border-radius: 8px;
        }

        .back-to-store a:hover {
            color: var(--primary-mid);
            background: var(--gray-100);
        }

        .back-to-store a i {
            transition: transform 0.3s ease;
        }

        .back-to-store a:hover i {
            transform: translateX(-4px);
        }

        /* Footer */
        .login-footer {
            margin-top: 40px;
            text-align: center;
        }

        .login-footer p {
            font-size: 12px;
            color: var(--gray-400);
        }

        /* Loading State */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .login-branding {
                padding: 40px;
            }

            .branding-content h1 {
                font-size: 36px;
            }

            .login-form-panel {
                padding: 40px;
            }
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-branding {
                padding: 40px 30px;
                min-height: auto;
            }

            .branding-content {
                max-width: 100%;
            }

            .branding-content h1 {
                font-size: 32px;
                letter-spacing: 3px;
            }

            .branding-content p,
            .features-list {
                display: none;
            }

            .login-form-panel {
                padding: 40px 24px;
                flex: none;
            }

            .login-form-wrapper {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .login-branding {
                padding: 30px 20px;
            }

            .brand-logo {
                width: 60px;
                height: 60px;
                border-radius: 16px;
                margin-bottom: 20px;
            }

            .brand-logo i {
                font-size: 28px;
            }

            .branding-content h1 {
                font-size: 26px;
                letter-spacing: 2px;
            }

            .branding-content .tagline {
                font-size: 12px;
                letter-spacing: 2px;
                margin-bottom: 0;
            }

            .login-form-panel {
                padding: 30px 20px;
            }

            .form-header h2 {
                font-size: 24px;
            }

            .form-control {
                padding: 14px 14px 14px 44px;
            }

            .btn-login {
                padding: 14px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Panel - Branding -->
        <div class="login-branding">
            <div class="floating-shape"></div>
            <div class="floating-shape"></div>
            <div class="floating-shape"></div>

            <div class="branding-content">
                <div class="brand-logo">
                    <i class="fas fa-store"></i>
                </div>
                <h1>WASLAH</h1>
                <p class="tagline">Admin Portal</p>
                <p>Manage your e-commerce store with powerful tools designed for efficiency and growth.</p>

                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Real-time Analytics & Reports</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-box"></i>
                        <span>Complete Inventory Management</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-users"></i>
                        <span>Customer Relationship Tools</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure & Role-based Access</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="login-form-panel">
            <div class="login-form-wrapper">
                <div class="form-header">
                    <h2>Welcome Back</h2>
                    <p>Sign in to access your admin dashboard</p>
                </div>

                <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('admin/login') ?>" id="loginForm">
                    <?= csrfField() ?>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email"
                                   class="form-control"
                                   name="email"
                                   required
                                   placeholder="Enter your email"
                                   value="<?= $_POST['email'] ?? 'admin@waslah.com' ?>"
                                   autocomplete="email">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input type="password"
                                   class="form-control"
                                   name="password"
                                   id="password"
                                   required
                                   placeholder="Enter your password"
                                   value="admin123"
                                   autocomplete="current-password">
                            <i class="fas fa-lock"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <span>Sign In</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="divider">
                    <span>or</span>
                </div>

                <div class="back-to-store">
                    <a href="<?= url() ?>">
                        <i class="fas fa-arrow-left"></i>
                        Back to Store
                    </a>
                </div>

                <div class="login-footer">
                    <p>&copy; <?= date('Y') ?> Waslah. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password Toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form Submit Loading State
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fas fa-spinner"></i> <span>Signing In...</span>';
        });

        // Input Focus Animation
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>
