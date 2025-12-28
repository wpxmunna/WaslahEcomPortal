<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('shop') ?>">Shop</a></li>
                <?php if ($product['category_name']): ?>
                <li class="breadcrumb-item"><a href="<?= url('shop/category/' . $product['category_slug']) ?>"><?= $product['category_name'] ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= sanitize($product['name']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row">
            <!-- Product Gallery -->
            <div class="col-lg-6 mb-4">
                <div class="product-gallery">
                    <div class="main-image">
                        <?php $primaryImage = $product['images'][0]['image_path'] ?? null; ?>
                        <?php if ($primaryImage): ?>
                        <img src="<?= upload($primaryImage) ?>" alt="<?= sanitize($product['name']) ?>" id="mainProductImage">
                        <?php else: ?>
                        <div class="img-placeholder" style="height: 500px;"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </div>
                    <?php if (count($product['images']) > 1): ?>
                    <div class="thumbnail-images">
                        <?php foreach ($product['images'] as $index => $image): ?>
                        <img src="<?= upload($image['image_path']) ?>"
                             alt="<?= $image['alt_text'] ?? $product['name'] ?>"
                             class="<?= $index === 0 ? 'active' : '' ?>"
                             data-large="<?= upload($image['image_path']) ?>">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <div class="product-details">
                    <h1><?= sanitize($product['name']) ?></h1>

                    <div class="product-meta">
                        <?php if ($product['sku']): ?>
                        <span><strong>SKU:</strong> <?= $product['sku'] ?></span>
                        <?php endif; ?>
                        <span><strong>Category:</strong> <?= $product['category_name'] ?></span>
                        <span>
                            <strong>Availability:</strong>
                            <?php if ($product['stock_quantity'] > 0): ?>
                            <span class="text-success">In Stock (<?= $product['stock_quantity'] ?>)</span>
                            <?php else: ?>
                            <span class="text-danger">Out of Stock</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="product-price-detail mb-4">
                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                        <span class="current-price"><?= formatPrice($product['sale_price']) ?></span>
                        <span class="original-price ms-2"><?= formatPrice($product['price']) ?></span>
                        <span class="badge bg-danger ms-2">-<?= discountPercent($product['price'], $product['sale_price']) ?>%</span>
                        <?php else: ?>
                        <span class="current-price"><?= formatPrice($product['price']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="product-description">
                        <p><?= nl2br(sanitize($product['short_description'] ?? '')) ?></p>
                    </div>

                    <form id="addToCartForm">
                        <?= csrfField() ?>
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" id="selectedSize" name="size" value="">
                        <input type="hidden" id="selectedColor" name="color" value="">
                        <input type="hidden" id="selectedVariant" name="variant_id" value="">

                        <!-- Size Selector -->
                        <?php
                        $sizes = array_unique(array_filter(array_column($product['variants'], 'size')));
                        if (!empty($sizes)):
                        ?>
                        <div class="variant-selector">
                            <label>Size</label>
                            <div class="size-options">
                                <?php foreach ($sizes as $size): ?>
                                <span class="size-option" data-size="<?= $size ?>"><?= strtoupper($size) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Color Selector -->
                        <?php
                        $colors = array_filter($product['variants'], fn($v) => !empty($v['color']));
                        if (!empty($colors)):
                        ?>
                        <div class="variant-selector">
                            <label>Color</label>
                            <div class="color-options">
                                <?php foreach ($colors as $variant): ?>
                                <span class="color-option"
                                      style="background: <?= $variant['color_code'] ?? $variant['color'] ?>"
                                      data-color="<?= $variant['color'] ?>"
                                      data-variant="<?= $variant['id'] ?>"
                                      title="<?= $variant['color'] ?>"></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Quantity -->
                        <div class="quantity-selector">
                            <label>Quantity</label>
                            <button type="button" class="qty-btn qty-minus">-</button>
                            <input type="number" class="qty-input" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                            <button type="button" class="qty-btn qty-plus">+</button>
                        </div>

                        <!-- Actions -->
                        <button type="button" class="btn btn-primary add-to-cart-btn" onclick="addProductToCart()">
                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                        </button>
                        <a href="<?= url('checkout') ?>" class="btn btn-outline-dark buy-now-btn" onclick="addProductToCart(); return true;">
                            <i class="fas fa-bolt me-2"></i> Buy Now
                        </a>
                    </form>

                    <!-- Additional Info -->
                    <div class="mt-4">
                        <div class="d-flex gap-3">
                            <button class="btn btn-link text-dark" onclick="toggleWishlist(<?= $product['id'] ?>, this)">
                                <i class="fas fa-heart me-1"></i> Add to Wishlist
                            </button>
                            <button class="btn btn-link text-dark" onclick="shareProduct()">
                                <i class="fas fa-share-alt me-1"></i> Share
                            </button>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="row text-center">
                            <div class="col-4">
                                <i class="fas fa-truck text-accent mb-2"></i>
                                <p class="small mb-0">Free Shipping</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-undo text-accent mb-2"></i>
                                <p class="small mb-0">30-Day Returns</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-shield-alt text-accent mb-2"></i>
                                <p class="small mb-0">Secure Payment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">Description</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">Reviews</button>
                    </li>
                </ul>
                <div class="tab-content p-4 border border-top-0">
                    <div class="tab-pane fade show active" id="description">
                        <?= nl2br(sanitize($product['description'] ?? 'No description available.')) ?>
                    </div>
                    <div class="tab-pane fade" id="reviews">
                        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5">
            <h3 class="mb-4">Related Products</h3>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $product): ?>
                <div class="col-lg-3 col-md-4 col-6">
                    <?php include VIEW_PATH . '/partials/product-card.php'; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function addProductToCart() {
    const form = document.getElementById('addToCartForm');
    const formData = new FormData(form);

    fetch(SITE_URL + '/cart/add', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added to cart!', 'success');
            // Update cart count
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = data.cartCount;
                el.style.display = 'flex';
            });
        } else {
            showNotification(data.message || 'Error adding to cart', 'error');
        }
    });
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '<?= sanitize($product['name']) ?>',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        showNotification('Link copied to clipboard!', 'success');
    }
}
</script>
