<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin - Waslah' ?></title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Admin CSS -->
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="<?= url('admin') ?>" class="sidebar-brand">
                    <img src="<?= asset('images/logo.png') ?>" alt="Waslah" class="admin-logo">
                    <div class="brand-text">
                        <span>WASLAH</span>
                        <small>Admin Panel</small>
                    </div>
                </a>
            </div>

            <!-- Store Selector -->
            <?php if (!empty($stores)): ?>
            <div class="store-selector">
                <select class="form-select form-select-sm" onchange="switchStore(this.value)">
                    <?php foreach ($stores as $store): ?>
                    <option value="<?= $store['id'] ?>" <?= ($currentStore['id'] ?? 1) == $store['id'] ? 'selected' : '' ?>>
                        <?= $store['name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <?php $isFullAdmin = ($user['role'] ?? '') === 'admin'; ?>
            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item">
                        <a href="<?= url('admin') ?>" class="nav-link <?= activeClass('admin') ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-header">Catalog</li>
                    <li class="nav-item">
                        <a href="<?= url('admin/products') ?>" class="nav-link <?= activeClass('admin/products') ?>">
                            <i class="fas fa-box"></i>
                            <span>Products</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/categories') ?>" class="nav-link <?= activeClass('admin/categories') ?>">
                            <i class="fas fa-tags"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/colors') ?>" class="nav-link <?= activeClass('admin/colors') ?>">
                            <i class="fas fa-palette"></i>
                            <span>Colors</span>
                        </a>
                    </li>

                    <li class="nav-header">Sales</li>
                    <li class="nav-item">
                        <a href="<?= url('admin/orders') ?>" class="nav-link <?= activeClass('admin/orders') ?>">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                            <?php if (($pendingOrders ?? 0) > 0): ?>
                            <span class="badge bg-danger"><?= $pendingOrders ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/customers') ?>" class="nav-link <?= activeClass('admin/customers') ?>">
                            <i class="fas fa-users"></i>
                            <span>Customers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/coupons') ?>" class="nav-link <?= activeClass('admin/coupons') ?>">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Coupons</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/returns') ?>" class="nav-link <?= activeClass('admin/returns') ?>">
                            <i class="fas fa-undo"></i>
                            <span>Returns</span>
                        </a>
                    </li>

                    <?php if ($isFullAdmin): ?>
                    <li class="nav-header">Shipping & Payments</li>
                    <li class="nav-item">
                        <a href="<?= url('admin/pathao') ?>" class="nav-link <?= activeClass('admin/pathao') ?>">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Pathao Courier</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/couriers') ?>" class="nav-link <?= activeClass('admin/couriers') ?>">
                            <i class="fas fa-truck"></i>
                            <span>Couriers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/settings/payment') ?>" class="nav-link <?= activeClass('admin/settings/payment') ?>">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>

                    <li class="nav-header">Multi-Store</li>
                    <li class="nav-item">
                        <a href="<?= url('admin/stores') ?>" class="nav-link <?= activeClass('admin/stores') ?>">
                            <i class="fas fa-store"></i>
                            <span>Stores</span>
                        </a>
                    </li>

                    <li class="nav-header">System</li>
                    <li class="nav-item">
                        <a href="<?= url('admin/users') ?>" class="nav-link <?= activeClass('admin/users') ?>">
                            <i class="fas fa-users-cog"></i>
                            <span>Admin Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/reports') ?>" class="nav-link <?= activeClass('admin/reports') ?>">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/settings') ?>" class="nav-link <?= activeClass('admin/settings') ?>">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= url() ?>" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Store
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="header-right">
                    <div class="dropdown">
                        <a href="#" class="header-user" data-bs-toggle="dropdown">
                            <span class="user-avatar">
                                <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                            </span>
                            <span class="user-name"><?= $user['name'] ?? 'Admin' ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('admin/settings') ?>">
                                <i class="fas fa-cog me-2"></i> Settings
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('admin/logout') ?>">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if (!empty($flash)): ?>
            <div class="container-fluid mt-3">
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
                    <?= $flash['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="admin-content">
                <?= $content ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin JS -->
    <script src="<?= asset('js/admin.js') ?>"></script>

    <script>
        const SITE_URL = '<?= SITE_URL ?>';

        function switchStore(storeId) {
            window.location.href = SITE_URL + '/admin/stores/switch/' + storeId;
        }

        // Sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.admin-wrapper').classList.toggle('sidebar-collapsed');
        });
    </script>
</body>
</html>
