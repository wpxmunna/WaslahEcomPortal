<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Waslah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-brand {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-brand h1 {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 3px;
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        .login-brand p {
            color: #666;
            font-size: 14px;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
        }
        .btn-login {
            background: #e94560;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-login:hover {
            background: #d13a52;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <h1>WASLAH</h1>
            <p>Admin Panel</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('admin/login') ?>">
            <?= csrfField() ?>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" required
                       placeholder="admin@waslah.com" value="admin@waslah.com">
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required
                       placeholder="Enter password" value="admin123">
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100">
                Login to Admin
            </button>
        </form>

        <p class="text-center text-muted mt-4 small">
            <a href="<?= url() ?>">Back to Store</a>
        </p>
    </div>
</body>
</html>
