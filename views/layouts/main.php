<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? siteName() . ' - ' . siteTagline() ?></title>
    <meta name="description" content="<?= $metaDescription ?? siteName() . ' - ' . siteTagline() . '. Quality fashion for Men, Women, and Children.' ?>">
    <meta name="theme-color" content="#0F2027">

    <!-- Fonts - Premium Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <!-- Modern Header -->
    <header class="site-header">
        <!-- Top Strip -->
        <div class="header-top">
            <div class="container">
                <div class="header-top-inner">
                    <div class="header-top-left">
                        <span class="promo-text">
                            <i class="fas fa-truck"></i>
                            Free Shipping on Orders Over <?= formatPrice(FREE_SHIPPING_THRESHOLD) ?>
                        </span>
                    </div>
                    <div class="header-top-right">
                        <?php
                        $headerPhone = getBusinessSetting('business_phone');
                        $headerEmail = getBusinessSetting('business_email');
                        ?>
                        <?php if ($headerPhone): ?>
                        <a href="tel:<?= htmlspecialchars(str_replace(' ', '', $headerPhone)) ?>">
                            <i class="fas fa-phone-alt"></i> <?= htmlspecialchars($headerPhone) ?>
                        </a>
                        <?php endif; ?>
                        <?php if ($headerEmail): ?>
                        <a href="mailto:<?= htmlspecialchars($headerEmail) ?>">
                            <i class="fas fa-envelope"></i> <?= htmlspecialchars($headerEmail) ?>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($socialLinksHeader)): ?>
                        <span class="header-social-links">
                            <?php foreach ($socialLinksHeader as $link): ?>
                            <a href="<?= htmlspecialchars($link['url']) ?>"
                               <?= $link['open_new_tab'] ? 'target="_blank" rel="noopener"' : '' ?>
                               title="<?= htmlspecialchars($link['name']) ?>">
                                <i class="fa-<?= $link['icon_style'] ?> <?= htmlspecialchars($link['icon']) ?>"></i>
                            </a>
                            <?php endforeach; ?>
                        </span>
                        <?php else: ?>
                        <?php
                        // Fallback to business settings if Social Media Manager not configured
                        $facebookUrl = getBusinessSetting('facebook_page_url');
                        $whatsappLink = getBusinessSetting('whatsapp_link');
                        ?>
                        <?php if ($facebookUrl || $whatsappLink): ?>
                        <span class="header-social-links">
                            <?php if ($facebookUrl): ?>
                            <a href="<?= htmlspecialchars($facebookUrl) ?>" target="_blank" rel="noopener" title="Facebook">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <?php endif; ?>
                            <?php if ($whatsappLink): ?>
                            <a href="<?= htmlspecialchars($whatsappLink) ?>" target="_blank" rel="noopener" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <?php endif; ?>
                        </span>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <div class="header-main">
            <div class="container">
                <div class="header-inner">
                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>

                    <!-- Logo -->
                    <a href="<?= url() ?>" class="site-logo">
                        <img src="<?= siteLogo() ?>" alt="<?= siteName() ?>">
                    </a>

                    <!-- Main Navigation -->
                    <nav class="main-nav" id="mainNav">
                        <ul class="nav-menu">
                            <li class="nav-item">
                                <a href="<?= url() ?>" class="nav-link <?= activeClass('') ?>">Home</a>
                            </li>
                            <?php foreach ($categories as $category): ?>
                            <li class="nav-item has-dropdown">
                                <a href="<?= url('shop/category/' . $category['slug']) ?>" class="nav-link">
                                    <?= $category['name'] ?>
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?= url('shop/category/' . $category['slug']) ?>">All <?= $category['name'] ?></a></li>
                                    <?php
                                    $subCategories = (new Category())->getChildren($category['id']);
                                    foreach ($subCategories as $sub):
                                    ?>
                                    <li><a href="<?= url('shop/category/' . $sub['slug']) ?>"><?= $sub['name'] ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <?php endforeach; ?>
                            <li class="nav-item">
                                <a href="<?= url('shop') ?>" class="nav-link">Shop All</a>
                            </li>
                        </ul>
                    </nav>

                    <!-- Header Actions -->
                    <div class="header-actions">
                        <!-- Search Toggle -->
                        <button class="action-btn search-toggle" id="searchToggle" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>

                        <!-- User Account -->
                        <div class="action-btn user-dropdown">
                            <button class="dropdown-trigger" aria-label="Account">
                                <i class="fas fa-user"></i>
                            </button>
                            <div class="dropdown-content">
                                <?php if ($isLoggedIn): ?>
                                <div class="dropdown-header">
                                    <span>Welcome back!</span>
                                </div>
                                <a href="<?= url('account') ?>"><i class="fas fa-user"></i> My Account</a>
                                <a href="<?= url('account/orders') ?>"><i class="fas fa-box"></i> My Orders</a>
                                <a href="<?= url('wishlist') ?>"><i class="fas fa-heart"></i> Wishlist</a>
                                <div class="dropdown-divider"></div>
                                <a href="<?= url('logout') ?>" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                                <?php else: ?>
                                <div class="dropdown-header">
                                    <span>My Account</span>
                                </div>
                                <a href="<?= url('login') ?>"><i class="fas fa-sign-in-alt"></i> Sign In</a>
                                <a href="<?= url('register') ?>"><i class="fas fa-user-plus"></i> Create Account</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Wishlist -->
                        <a href="<?= url('wishlist') ?>" class="action-btn" aria-label="Wishlist">
                            <i class="fas fa-heart"></i>
                        </a>

                        <!-- Cart -->
                        <a href="<?= url('cart') ?>" class="action-btn cart-btn" aria-label="Cart">
                            <i class="fas fa-shopping-bag"></i>
                            <?php if ($cartCount > 0): ?>
                            <span class="cart-badge"><?= $cartCount ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Overlay -->
        <div class="search-overlay" id="searchOverlay">
            <div class="search-overlay-inner">
                <form action="<?= url('search') ?>" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="Search for products..." autocomplete="off">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <button class="search-close" id="searchClose"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
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
                        <img src="<?= siteLogo() ?>" alt="<?= siteName() ?>" class="footer-logo mb-3">
                        <p><?= siteTagline() ?>. Quality clothing for Men, Women, and Children.</p>
                        <div class="social-links">
                            <?php if (!empty($socialLinksFooter)): ?>
                            <?php foreach ($socialLinksFooter as $link): ?>
                            <a href="<?= htmlspecialchars($link['url']) ?>"
                               <?= $link['open_new_tab'] ? 'target="_blank" rel="noopener"' : '' ?>
                               title="<?= htmlspecialchars($link['name']) ?>"
                               style="background-color: <?= htmlspecialchars($link['color']) ?>">
                                <i class="fa-<?= $link['icon_style'] ?> <?= htmlspecialchars($link['icon']) ?>"></i>
                            </a>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <?php
                            // Fallback to business settings if Social Media Manager not configured
                            $footerFacebook = getBusinessSetting('facebook_page_url');
                            $footerWhatsapp = getBusinessSetting('whatsapp_link');
                            ?>
                            <?php if ($footerFacebook): ?>
                            <a href="<?= htmlspecialchars($footerFacebook) ?>" target="_blank" rel="noopener" title="Facebook" style="background-color: #1877f2">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <?php endif; ?>
                            <?php if ($footerWhatsapp): ?>
                            <a href="<?= htmlspecialchars($footerWhatsapp) ?>" target="_blank" rel="noopener" title="WhatsApp" style="background-color: #25d366">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <?php endif; ?>
                            <?php endif; ?>
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
                        <h5>Contact Us</h5>
                        <?php
                        $businessEmail = getBusinessSetting('business_email');
                        $businessPhone = getBusinessSetting('business_phone');
                        $whatsappNumber = getBusinessSetting('whatsapp_number');
                        $whatsappLink = getBusinessSetting('whatsapp_link');
                        $businessAddress = getBusinessSetting('business_address');
                        ?>
                        <?php if ($businessEmail): ?>
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:<?= htmlspecialchars($businessEmail) ?>" class="text-white text-decoration-none">
                                <?= htmlspecialchars($businessEmail) ?>
                            </a>
                        </p>
                        <?php endif; ?>
                        <?php if ($businessPhone): ?>
                        <p class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:<?= htmlspecialchars($businessPhone) ?>" class="text-white text-decoration-none">
                                <?= htmlspecialchars($businessPhone) ?>
                            </a>
                        </p>
                        <?php endif; ?>
                        <?php if ($whatsappNumber && $whatsappLink): ?>
                        <p class="mb-2">
                            <i class="fab fa-whatsapp me-2"></i>
                            <a href="<?= htmlspecialchars($whatsappLink) ?>" target="_blank" class="text-white text-decoration-none">
                                <?= htmlspecialchars($whatsappNumber) ?>
                            </a>
                        </p>
                        <?php endif; ?>
                        <?php if ($businessAddress): ?>
                        <p class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <?= nl2br(htmlspecialchars($businessAddress)) ?>
                        </p>
                        <?php endif; ?>
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
                        <p>&copy; <?= date('Y') ?> <?= siteName() ?>. All rights reserved.</p>
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

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Custom JS -->
    <script src="<?= asset('js/main.js') ?>"></script>

    <script>
        const SITE_URL = '<?= SITE_URL ?>';

        // Initialize AOS animations
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });

        // Mobile Menu Toggle
        (function() {
            const menuToggle = document.getElementById('mobileMenuToggle');
            const mainNav = document.getElementById('mainNav');
            const menuOverlay = document.getElementById('mobileMenuOverlay');

            if (menuToggle && mainNav) {
                menuToggle.addEventListener('click', function() {
                    this.classList.toggle('active');
                    mainNav.classList.toggle('active');
                    menuOverlay.classList.toggle('active');
                    document.body.style.overflow = mainNav.classList.contains('active') ? 'hidden' : '';
                });

                if (menuOverlay) {
                    menuOverlay.addEventListener('click', function() {
                        menuToggle.classList.remove('active');
                        mainNav.classList.remove('active');
                        this.classList.remove('active');
                        document.body.style.overflow = '';
                    });
                }

                // Mobile dropdown toggle
                document.querySelectorAll('.nav-item.has-dropdown > .nav-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        if (window.innerWidth <= 991) {
                            e.preventDefault();
                            this.parentElement.classList.toggle('dropdown-open');
                        }
                    });
                });
            }
        })();

        // Search Overlay
        (function() {
            const searchToggle = document.getElementById('searchToggle');
            const searchOverlay = document.getElementById('searchOverlay');
            const searchClose = document.getElementById('searchClose');
            const searchInput = searchOverlay ? searchOverlay.querySelector('input') : null;

            if (searchToggle && searchOverlay) {
                searchToggle.addEventListener('click', function() {
                    searchOverlay.classList.add('active');
                    if (searchInput) {
                        setTimeout(() => searchInput.focus(), 300);
                    }
                });

                if (searchClose) {
                    searchClose.addEventListener('click', function() {
                        searchOverlay.classList.remove('active');
                    });
                }

                // Close on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
                        searchOverlay.classList.remove('active');
                    }
                });
            }
        })();

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
