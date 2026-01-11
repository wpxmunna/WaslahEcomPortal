<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin - Waslah' ?></title>
    <meta name="theme-color" content="#0F2027">

    <!-- Fonts - Premium Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
    <div class="admin-wrapper" id="adminWrapper">
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
                    <li class="nav-item">
                        <a href="<?= url('admin/sliders') ?>" class="nav-link <?= activeClass('admin/sliders') ?>">
                            <i class="fas fa-images"></i>
                            <span>Sliders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/lookbook') ?>" class="nav-link <?= activeClass('admin/lookbook') ?>">
                            <i class="fab fa-instagram"></i>
                            <span>Lookbook</span>
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

                    <li class="nav-header">Point of Sale</li>
                    <li class="nav-item">
                        <a href="<?= url('admin/pos') ?>" class="nav-link <?= activeClass('admin/pos') ?>">
                            <i class="fas fa-cash-register"></i>
                            <span>POS Terminal</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/pos/transactions') ?>" class="nav-link <?= activeClass('admin/pos/transactions') ?>">
                            <i class="fas fa-receipt"></i>
                            <span>Transactions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/pos/shifts') ?>" class="nav-link <?= activeClass('admin/pos/shifts') ?>">
                            <i class="fas fa-clock"></i>
                            <span>Shifts</span>
                        </a>
                    </li>

                    <?php if ($isFullAdmin): ?>
                    <li class="nav-header">Human Resources</li>
                    <li class="nav-item">
                        <a href="<?= url('admin/employees') ?>" class="nav-link <?= activeClass('admin/employees') ?>">
                            <i class="fas fa-user-tie"></i>
                            <span>Employees</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/attendance') ?>" class="nav-link <?= activeClass('admin/attendance') ?>">
                            <i class="fas fa-calendar-check"></i>
                            <span>Attendance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/payroll') ?>" class="nav-link <?= activeClass('admin/payroll') ?>">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Payroll</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-header">Finance & Accounting</li>
                    <li class="nav-item">
                        <a href="<?= url('admin/expenses') ?>" class="nav-link <?= activeClass('admin/expenses') ?>">
                            <i class="fas fa-receipt"></i>
                            <span>Expenses</span>
                        </a>
                    </li>
                    <?php if ($isFullAdmin): ?>
                    <li class="nav-item">
                        <a href="<?= url('admin/suppliers') ?>" class="nav-link <?= activeClass('admin/suppliers') ?>">
                            <i class="fas fa-truck-loading"></i>
                            <span>Suppliers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/purchase-orders') ?>" class="nav-link <?= activeClass('admin/purchase-orders') ?>">
                            <i class="fas fa-file-invoice"></i>
                            <span>Purchase Orders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/accounting') ?>" class="nav-link <?= activeClass('admin/accounting') ?>">
                            <i class="fas fa-book"></i>
                            <span>Accounting</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/finance-reports') ?>" class="nav-link <?= activeClass('admin/finance-reports') ?>">
                            <i class="fas fa-chart-line"></i>
                            <span>Financial Reports</span>
                        </a>
                    </li>

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
                        <a href="<?= url('admin/social-media') ?>" class="nav-link <?= activeClass('admin/social-media') ?>">
                            <i class="fas fa-share-alt"></i>
                            <span>Social Media</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('admin/meta') ?>" class="nav-link <?= activeClass('admin/meta') ?>">
                            <i class="fab fa-meta"></i>
                            <span>Meta Business</span>
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
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Store</span>
                </a>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <div class="header-right">
                    <!-- Quick Actions -->
                    <a href="<?= url('admin/products/create') ?>" class="btn btn-sm btn-primary d-none d-md-flex">
                        <i class="fas fa-plus"></i>
                        <span>Add Product</span>
                    </a>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="header-user" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="user-avatar">
                                <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                            </span>
                            <div class="user-info d-none d-sm-flex">
                                <span class="user-name"><?= $user['name'] ?? 'Admin' ?></span>
                                <span class="user-role"><?= ucfirst($user['role'] ?? 'Admin') ?></span>
                            </div>
                            <i class="fas fa-chevron-down ms-2 d-none d-sm-inline"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="px-3 py-2 d-sm-none">
                                <div class="fw-semibold"><?= $user['name'] ?? 'Admin' ?></div>
                                <small class="text-muted"><?= ucfirst($user['role'] ?? 'Admin') ?></small>
                            </li>
                            <li class="d-sm-none"><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?= url('admin/settings') ?>">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= url() ?>" target="_blank">
                                    <i class="fas fa-store"></i> View Store
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= url('admin/logout') ?>">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if (!empty($flash)): ?>
            <div class="admin-content pt-0" style="padding-bottom: 0;">
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show mb-0">
                    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-circle' : 'info-circle') ?> me-2"></i>
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

        // Sidebar functionality
        (function() {
            const wrapper = document.getElementById('adminWrapper');
            const toggleBtn = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            // Toggle sidebar
            toggleBtn?.addEventListener('click', function() {
                const isMobile = window.innerWidth <= 991;
                if (isMobile) {
                    wrapper.classList.toggle('sidebar-open');
                } else {
                    wrapper.classList.toggle('sidebar-collapsed');
                    // Save preference
                    localStorage.setItem('sidebarCollapsed', wrapper.classList.contains('sidebar-collapsed'));
                }
            });

            // Close sidebar on overlay click (mobile)
            overlay?.addEventListener('click', function() {
                wrapper.classList.remove('sidebar-open');
            });

            // Restore sidebar state on desktop
            if (window.innerWidth > 991) {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    wrapper.classList.add('sidebar-collapsed');
                }
            }

            // Handle resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth > 991) {
                        wrapper.classList.remove('sidebar-open');
                    }
                }, 250);
            });
        })();

        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

        // Auto-dismiss alerts after 5 seconds
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        });
    </script>
</body>
</html>
