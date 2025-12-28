<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('shop') ?>">Shop</a></li>
                <?php foreach ($breadcrumb as $crumb): ?>
                <li class="breadcrumb-item <?= $crumb['id'] === $category['id'] ? 'active' : '' ?>">
                    <?php if ($crumb['id'] === $category['id']): ?>
                        <?= $crumb['name'] ?>
                    <?php else: ?>
                        <a href="<?= url('shop/category/' . $crumb['slug']) ?>"><?= $crumb['name'] ?></a>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
</section>

<!-- Category Header -->
<section class="py-4 bg-light">
    <div class="container">
        <h1 class="mb-2"><?= $category['name'] ?></h1>
        <?php if ($category['description']): ?>
        <p class="text-muted mb-0"><?= $category['description'] ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="shop-sidebar">
                    <!-- Subcategories -->
                    <?php
                    $subCategories = (new Category())->getChildren($category['id']);
                    if (!empty($subCategories)):
                    ?>
                    <div class="filter-section">
                        <h5>Subcategories</h5>
                        <ul class="filter-list">
                            <?php foreach ($subCategories as $sub): ?>
                            <li><a href="<?= url('shop/category/' . $sub['slug']) ?>"><?= $sub['name'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Price Range -->
                    <div class="filter-section">
                        <h5>Price Range</h5>
                        <form id="priceFilterForm" class="price-range">
                            <input type="number" id="minPrice" placeholder="Min" value="<?= $filters['min_price'] ?? '' ?>">
                            <span>-</span>
                            <input type="number" id="maxPrice" placeholder="Max" value="<?= $filters['max_price'] ?? '' ?>">
                            <button type="submit" class="btn btn-sm btn-primary">Go</button>
                        </form>
                    </div>

                    <!-- All Categories -->
                    <div class="filter-section">
                        <h5>All Categories</h5>
                        <ul class="filter-list">
                            <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?= url('shop/category/' . $cat['slug']) ?>"
                                   class="<?= $cat['id'] === $category['id'] || $cat['id'] === $category['parent_id'] ? 'active' : '' ?>">
                                    <?= $cat['name'] ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="col-lg-9">
                <!-- Toolbar -->
                <div class="shop-toolbar">
                    <div>
                        <span>Showing <?= count($products['data']) ?> of <?= $products['total'] ?> products</span>
                    </div>
                    <div>
                        <select class="form-select form-select-sm" style="width: auto; display: inline-block;"
                                onchange="window.location.href='?sort='+this.value">
                            <option value="newest" <?= $currentSort === 'newest' ? 'selected' : '' ?>>Newest</option>
                            <option value="popular" <?= $currentSort === 'popular' ? 'selected' : '' ?>>Popular</option>
                            <option value="price_low" <?= $currentSort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_high" <?= $currentSort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <?php if (empty($products['data'])): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No products in this category</h3>
                    <p>Check back later or browse other categories</p>
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
                <?= pagination($products, url('shop/category/' . $category['slug'])) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
