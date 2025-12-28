<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item active">Wishlist</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <?php include VIEW_PATH . '/user/partials/sidebar.php'; ?>
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                <div class="account-content">
                    <h3 class="mb-4">My Wishlist</h3>

                    <?php if (empty($wishlist)): ?>
                    <div class="empty-state">
                        <i class="fas fa-heart"></i>
                        <h4>Your wishlist is empty</h4>
                        <p>Save items you love for later</p>
                        <a href="<?= url('shop') ?>" class="btn btn-primary">Browse Products</a>
                    </div>
                    <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($wishlist as $item): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="<?= url('product/' . $item['slug']) ?>">
                                        <?php if ($item['image']): ?>
                                        <img src="<?= upload($item['image']) ?>" alt="<?= sanitize($item['name']) ?>">
                                        <?php else: ?>
                                        <div class="img-placeholder" style="height: 100%;"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                    </a>
                                    <div class="product-actions" style="opacity: 1; transform: none;">
                                        <form action="<?= url('wishlist/remove') ?>" method="POST" style="display: inline;">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                            <button type="submit" title="Remove" class="text-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <button onclick="addToCart(<?= $item['product_id'] ?>)" title="Add to Cart">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">
                                        <a href="<?= url('product/' . $item['slug']) ?>"><?= sanitize($item['name']) ?></a>
                                    </h3>
                                    <div class="product-price">
                                        <?php if ($item['sale_price'] && $item['sale_price'] < $item['price']): ?>
                                        <span class="current-price"><?= formatPrice($item['sale_price']) ?></span>
                                        <span class="original-price"><?= formatPrice($item['price']) ?></span>
                                        <?php else: ?>
                                        <span class="current-price"><?= formatPrice($item['price']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
