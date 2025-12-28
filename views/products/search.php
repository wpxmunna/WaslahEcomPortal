<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('shop') ?>">Shop</a></li>
                <li class="breadcrumb-item active">Search: <?= sanitize($searchQuery) ?></li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="shop-sidebar">
                    <div class="filter-section">
                        <h5>Search Results</h5>
                        <p class="text-muted">Found <?= $products['total'] ?> results for "<?= sanitize($searchQuery) ?>"</p>
                    </div>

                    <div class="filter-section">
                        <h5>Categories</h5>
                        <ul class="filter-list">
                            <?php foreach ($categories as $cat): ?>
                            <li><a href="<?= url('shop/category/' . $cat['slug']) ?>"><?= $cat['name'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="col-lg-9">
                <h2 class="mb-4">Search Results for "<?= sanitize($searchQuery) ?>"</h2>

                <?php if (empty($products['data'])): ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No products found</h3>
                    <p>Try different keywords or browse our categories</p>
                    <a href="<?= url('shop') ?>" class="btn btn-primary">View All Products</a>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products['data'] as $product): ?>
                    <div class="col-lg-4 col-md-6 col-6">
                        <?php include VIEW_PATH . '/partials/product-card.php'; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?= pagination($products, url('search') . '?q=' . urlencode($searchQuery)) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
