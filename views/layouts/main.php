<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? SITE_NAME . ' - ' . SITE_TAGLINE ?></title>
    <meta name="description" content="<?= $metaDescription ?? 'Waslah - Authenticity in Every Stitch. Quality fashion for Men, Women, and Children.' ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span><i class="fas fa-phone-alt me-2"></i> +1 234 567 890</span>
                    <span class="ms-3"><i class="fas fa-envelope me-2"></i> info@waslah.com</span>
                </div>
                <div class="col-md-6 text-end">
                    <span>Free Shipping on Orders Over <?= formatPrice(FREE_SHIPPING_THRESHOLD) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <a class="navbar-brand" href="<?= url() ?>">
                    <img src="<?= asset('images/logo.png') ?>" alt="Waslah" class="logo-img">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= activeClass('') ?>" href="<?= url() ?>">Home</a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="<?= url('shop/category/' . $category['slug']) ?>" data-bs-toggle="dropdown">
                                <?= $category['name'] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= url('shop/category/' . $category['slug']) ?>">All <?= $category['name'] ?></a></li>
                                <?php
                                $subCategories = (new Category())->getChildren($category['id']);
                                foreach ($subCategories as $sub):
                                ?>
                                <li><a class="dropdown-item" href="<?= url('shop/category/' . $sub['slug']) ?>"><?= $sub['name'] ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php endforeach; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('shop') ?>">Shop</a>
                        </li>
                    </ul>

                    <div class="header-actions">
                        <!-- Search -->
                        <form class="search-form" action="<?= url('search') ?>" method="GET">
                            <input type="text" name="q" placeholder="Search..." class="form-control">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>

                        <!-- User -->
                        <div class="dropdown">
                            <a href="#" class="header-icon" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($isLoggedIn): ?>
                                    <li><a class="dropdown-item" href="<?= url('account') ?>"><i class="fas fa-user me-2"></i> My Account</a></li>
                                    <li><a class="dropdown-item" href="<?= url('account/orders') ?>"><i class="fas fa-box me-2"></i> My Orders</a></li>
                                    <li><a class="dropdown-item" href="<?= url('wishlist') ?>"><i class="fas fa-heart me-2"></i> Wishlist</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="<?= url('login') ?>"><i class="fas fa-sign-in-alt me-2"></i> Login</a></li>
                                    <li><a class="dropdown-item" href="<?= url('register') ?>"><i class="fas fa-user-plus me-2"></i> Register</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- Wishlist -->
                        <a href="<?= url('wishlist') ?>" class="header-icon">
                            <i class="fas fa-heart"></i>
                        </a>

                        <!-- Cart -->
                        <a href="<?= url('cart') ?>" class="header-icon cart-icon">
                            <i class="fas fa-shopping-bag"></i>
                            <?php if ($cartCount > 0): ?>
                            <span class="cart-count"><?= $cartCount ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if ($flash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <img src="<?= asset('images/logo.png') ?>" alt="Waslah" class="footer-logo mb-3">
                        <p>Authenticity in Every Stitch. Quality clothing for Men, Women, and Children.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-pinterest"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5>Quick Links</h5>
                        <ul class="footer-links">
                            <li><a href="<?= url() ?>">Home</a></li>
                            <li><a href="<?= url('shop') ?>">Shop</a></li>
                            <li><a href="<?= url('shop/category/men') ?>">Men</a></li>
                            <li><a href="<?= url('shop/category/women') ?>">Women</a></li>
                            <li><a href="<?= url('shop/category/children') ?>">Children</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5>Customer Service</h5>
                        <ul class="footer-links">
                            <li><a href="<?= url('account') ?>">My Account</a></li>
                            <li><a href="<?= url('account/orders') ?>">Order Tracking</a></li>
                            <li><a href="#">Shipping Info</a></li>
                            <li><a href="#">Returns</a></li>
                            <li><a href="#">FAQs</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <h5>Newsletter</h5>
                        <p>Subscribe to get special offers and updates.</p>
                        <form class="newsletter-form">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Your email">
                                <button class="btn btn-primary" type="submit">Subscribe</button>
                            </div>
                        </form>
                        <div class="payment-methods mt-3">
                            <i class="fab fa-cc-visa fa-2x"></i>
                            <i class="fab fa-cc-mastercard fa-2x"></i>
                            <i class="fab fa-cc-paypal fa-2x"></i>
                            <i class="fab fa-cc-apple-pay fa-2x"></i>
                            <i class="fab fa-cc-amex fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p>&copy; <?= date('Y') ?> Waslah Fashion. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="#">Privacy Policy</a>
                        <a href="#" class="ms-3">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="<?= asset('js/main.js') ?>"></script>

    <script>
        const SITE_URL = '<?= SITE_URL ?>';
    </script>
</body>
</html>
