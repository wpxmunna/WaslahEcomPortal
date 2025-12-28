<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item active">Shop</li>
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
                    <!-- Categories -->
                    <div class="filter-section">
                        <h5>Categories</h5>
                        <ul class="filter-list">
                            <li><a href="<?= url('shop') ?>" class="<?= !isset($category) ? 'active' : '' ?>">All Products</a></li>
                            <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?= url('shop/category/' . $cat['slug']) ?>">
                                    <?= $cat['name'] ?>
                                </a>
                                <?php if (!empty($cat['children'])): ?>
                                <ul class="filter-list ms-3 mt-2">
                                    <?php foreach ($cat['children'] as $child): ?>
                                    <li><a href="<?= url('shop/category/' . $child['slug']) ?>"><?= $child['name'] ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

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

                    <!-- Colors -->
                    <div class="filter-section">
                        <h5>Colors</h5>
                        <div class="color-options">
                            <span class="color-option" style="background: #000"></span>
                            <span class="color-option" style="background: #fff; border: 1px solid #ddd"></span>
                            <span class="color-option" style="background: #e94560"></span>
                            <span class="color-option" style="background: #1a1a2e"></span>
                            <span class="color-option" style="background: #28a745"></span>
                            <span class="color-option" style="background: #ffc107"></span>
                        </div>
                    </div>

                    <!-- Sizes -->
                    <div class="filter-section">
                        <h5>Sizes</h5>
                        <div class="size-options">
                            <span class="size-option">XS</span>
                            <span class="size-option">S</span>
                            <span class="size-option">M</span>
                            <span class="size-option">L</span>
                            <span class="size-option">XL</span>
                            <span class="size-option">XXL</span>
                        </div>
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
                        <select class="form-select form-select-sm" style="width: auto; display: inline-block;" onchange="window.location.href='?sort='+this.value">
                            <option value="newest" <?= $currentSort === 'newest' ? 'selected' : '' ?>>Newest</option>
                            <option value="popular" <?= $currentSort === 'popular' ? 'selected' : '' ?>>Popular</option>
                            <option value="price_low" <?= $currentSort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_high" <?= $currentSort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <?php if (empty($products['data'])): ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No products found</h3>
                    <p>Try adjusting your filters or browse our categories</p>
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
                <?= pagination($products, url('shop')) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
