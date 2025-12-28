<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Waslah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }
        .error-container {
            text-align: center;
            padding: 40px;
        }
        .error-code {
            font-size: 150px;
            font-weight: 700;
            line-height: 1;
            background: linear-gradient(45deg, #e94560, #f8b4c0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-message {
            font-size: 24px;
            margin-bottom: 30px;
        }
        .btn-home {
            background: #e94560;
            color: #fff;
            padding: 12px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            background: #d13a52;
            color: #fff;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-message">Oops! Page Not Found</h1>
        <p class="mb-4">The page you're looking for doesn't exist or has been moved.</p>
        <a href="<?= SITE_URL ?? '/' ?>" class="btn-home">
            <i class="fas fa-home me-2"></i> Back to Home
        </a>
    </div>
</body>
</html>
