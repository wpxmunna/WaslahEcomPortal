<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<!-- Hero Slider Section -->
<section class="hero-slider">
    <div class="swiper heroSwiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1200&q=80');">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="hero-content">
                            <span class="hero-subtitle">New Collection 2025</span>
                            <h1 class="hero-title">Elegance<br>Redefined</h1>
                            <p class="hero-desc">Discover the perfect blend of tradition and modernity</p>
                            <div class="hero-buttons">
                                <a href="<?= url('shop/category/women') ?>" class="btn-hero-primary">Shop Women</a>
                                <a href="<?= url('shop/category/men') ?>" class="btn-hero-secondary">Shop Men</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1200&q=80');">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="hero-content">
                            <span class="hero-subtitle">Premium Quality</span>
                            <h1 class="hero-title">Authenticity in<br>Every Stitch</h1>
                            <p class="hero-desc">Handcrafted with love, designed for you</p>
                            <div class="hero-buttons">
                                <a href="<?= url('shop') ?>" class="btn-hero-primary">Explore Collection</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="swiper-slide">
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&q=80');">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="hero-content">
                            <span class="hero-subtitle">Kids Collection</span>
                            <h1 class="hero-title">Style for<br>Little Ones</h1>
                            <p class="hero-desc">Comfort meets fashion for your children</p>
                            <div class="hero-buttons">
                                <a href="<?= url('shop/category/children') ?>" class="btn-hero-primary">Shop Kids</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>

<!-- Marquee Banner -->
<div class="marquee-banner">
    <div class="marquee-track">
        <div class="marquee-content">
            <span>FREE SHIPPING ON ORDERS OVER <?= formatPrice(FREE_SHIPPING_THRESHOLD) ?></span>
            <span class="separator">✦</span>
            <span>NEW ARRIVALS EVERY WEEK</span>
            <span class="separator">✦</span>
            <span>EASY 30-DAY RETURNS</span>
            <span class="separator">✦</span>
            <span>100% AUTHENTIC PRODUCTS</span>
            <span class="separator">✦</span>
            <span>FREE SHIPPING ON ORDERS OVER <?= formatPrice(FREE_SHIPPING_THRESHOLD) ?></span>
            <span class="separator">✦</span>
            <span>NEW ARRIVALS EVERY WEEK</span>
            <span class="separator">✦</span>
            <span>EASY 30-DAY RETURNS</span>
            <span class="separator">✦</span>
            <span>100% AUTHENTIC PRODUCTS</span>
            <span class="separator">✦</span>
        </div>
    </div>
</div>

<!-- Categories Grid Section -->
<section class="categories-section">
    <div class="container">
        <div class="row g-4">
            <?php
            $categoryImages = [
                'men' => 'https://images.unsplash.com/photo-1617137968427-85924c800a22?w=600&q=80',
                'women' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=600&q=80',
                'children' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=600&q=80'
            ];
            $index = 0;
            foreach ($mainCategories as $category):
                $colClass = $index === 0 ? 'col-lg-6' : 'col-lg-3';
                $imageUrl = $category['image'] ? upload($category['image']) : ($categoryImages[strtolower($category['slug'])] ?? 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=600&q=80');
            ?>
            <div class="<?= $colClass ?> col-md-6">
                <a href="<?= url('shop/category/' . $category['slug']) ?>" class="category-block">
                    <div class="category-image">
                        <img src="<?= $imageUrl ?>" alt="<?= $category['name'] ?>" loading="<?= $index < 2 ? 'eager' : 'lazy' ?>" decoding="async">
                    </div>
                    <div class="category-info">
                        <h3><?= $category['name'] ?></h3>
                        <span class="category-link">Shop Now <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            </div>
            <?php $index++; endforeach; ?>
        </div>
    </div>
</section>

<!-- New Arrivals Section -->
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <div class="section-header-left">
                <span class="section-tag">Just In</span>
                <h2 class="section-title">New Arrivals</h2>
            </div>
            <a href="<?= url('shop?sort=newest') ?>" class="section-link">
                View All <i class="fas fa-long-arrow-alt-right"></i>
            </a>
        </div>

        <div class="products-grid">
            <?php $productIndex = 0; foreach (array_slice($newArrivals, 0, 8) as $product): ?>
            <div class="product-item">
                <div class="product-card-modern">
                    <div class="product-image">
                        <a href="<?= url('product/' . $product['slug']) ?>">
                            <?php
                            $imgSrc = $product['image'] ? upload($product['image']) : 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400&q=80';
                            ?>
                            <img src="<?= $imgSrc ?>" alt="<?= sanitize($product['name']) ?>" class="img-primary" loading="<?= $productIndex < 4 ? 'eager' : 'lazy' ?>" decoding="async" width="320" height="320">
                        </a>
                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                        <span class="product-badge sale">-<?= discountPercent($product['price'], $product['sale_price']) ?>%</span>
                        <?php endif; ?>
                        <?php if (strtotime($product['created_at']) > strtotime('-7 days')): ?>
                        <span class="product-badge new">New</span>
                        <?php endif; ?>
                        <div class="product-actions">
                            <button class="action-btn" onclick="addToWishlist(<?= $product['id'] ?>)" title="Add to Wishlist">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="action-btn" onclick="quickView(<?= $product['id'] ?>)" title="Quick View">
                                <i class="far fa-eye"></i>
                            </button>
                            <button class="action-btn" onclick="addToCart(<?= $product['id'] ?>)" title="Add to Cart">
                                <i class="fas fa-shopping-bag"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="product-category"><?= $product['category_name'] ?? 'Fashion' ?></span>
                        <h3 class="product-name">
                            <a href="<?= url('product/' . $product['slug']) ?>"><?= sanitize($product['name']) ?></a>
                        </h3>
                        <div class="product-price">
                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                            <span class="price-current"><?= formatPrice($product['sale_price']) ?></span>
                            <span class="price-original"><?= formatPrice($product['price']) ?></span>
                            <?php else: ?>
                            <span class="price-current"><?= formatPrice($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php $productIndex++; endforeach; ?>
        </div>
    </div>
</section>

<!-- Brand Story Banner -->
<section class="brand-story">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="brand-story-image">
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80" alt="Our Story" loading="lazy" decoding="async">
                    <div class="experience-badge">
                        <span class="years">10+</span>
                        <span class="text">Years of Excellence</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="brand-story-content">
                    <span class="story-tag">Our Story</span>
                    <h2>Crafting Fashion with Purpose</h2>
                    <p class="lead">At Waslah, we believe that fashion is more than just clothing - it's a form of self-expression that connects tradition with contemporary style.</p>
                    <p>Every piece in our collection is carefully curated to bring you the finest quality fabrics, timeless designs, and authentic craftsmanship from skilled artisans across Bangladesh.</p>
                    <div class="story-features">
                        <div class="story-feature">
                            <i class="fas fa-leaf"></i>
                            <span>Sustainable Materials</span>
                        </div>
                        <div class="story-feature">
                            <i class="fas fa-hand-holding-heart"></i>
                            <span>Ethical Production</span>
                        </div>
                        <div class="story-feature">
                            <i class="fas fa-gem"></i>
                            <span>Premium Quality</span>
                        </div>
                    </div>
                    <a href="<?= url('shop') ?>" class="btn-brand">Discover Our Collection</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="products-section bg-light">
    <div class="container">
        <div class="section-header centered">
            <span class="section-tag">Curated For You</span>
            <h2 class="section-title">Featured Collection</h2>
            <p class="section-desc">Hand-picked pieces that define elegance and style</p>
        </div>

        <div class="products-grid">
            <?php foreach (array_slice($featuredProducts, 0, 8) as $product): ?>
            <div class="product-item">
                <div class="product-card-modern">
                    <div class="product-image">
                        <a href="<?= url('product/' . $product['slug']) ?>">
                            <?php
                            $imgSrc = $product['image'] ? upload($product['image']) : 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400&q=80';
                            ?>
                            <img src="<?= $imgSrc ?>" alt="<?= sanitize($product['name']) ?>" class="img-primary" loading="lazy" decoding="async" width="320" height="320">
                        </a>
                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                        <span class="product-badge sale">-<?= discountPercent($product['price'], $product['sale_price']) ?>%</span>
                        <?php endif; ?>
                        <div class="product-actions">
                            <button class="action-btn" onclick="addToWishlist(<?= $product['id'] ?>)" title="Add to Wishlist">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="action-btn" onclick="quickView(<?= $product['id'] ?>)" title="Quick View">
                                <i class="far fa-eye"></i>
                            </button>
                            <button class="action-btn" onclick="addToCart(<?= $product['id'] ?>)" title="Add to Cart">
                                <i class="fas fa-shopping-bag"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="product-category"><?= $product['category_name'] ?? 'Fashion' ?></span>
                        <h3 class="product-name">
                            <a href="<?= url('product/' . $product['slug']) ?>"><?= sanitize($product['name']) ?></a>
                        </h3>
                        <div class="product-price">
                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                            <span class="price-current"><?= formatPrice($product['sale_price']) ?></span>
                            <span class="price-original"><?= formatPrice($product['price']) ?></span>
                            <?php else: ?>
                            <span class="price-current"><?= formatPrice($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?= url('shop') ?>" class="btn-view-all">View All Products</a>
        </div>
    </div>
</section>

<!-- Lookbook / Instagram Section -->
<section class="lookbook-section">
    <div class="container">
        <div class="section-header centered">
            <span class="section-tag">@waslah.fashion</span>
            <h2 class="section-title">Follow Our Style</h2>
            <p class="section-desc">Get inspired by our latest looks</p>
        </div>

        <div class="lookbook-grid">
            <div class="lookbook-item large">
                <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?w=500&q=80" alt="Lookbook" loading="lazy" decoding="async">
                <div class="lookbook-overlay">
                    <i class="fab fa-instagram"></i>
                </div>
            </div>
            <div class="lookbook-item">
                <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=300&q=80" alt="Lookbook" loading="lazy" decoding="async">
                <div class="lookbook-overlay">
                    <i class="fab fa-instagram"></i>
                </div>
            </div>
            <div class="lookbook-item">
                <img src="https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=300&q=80" alt="Lookbook" loading="lazy" decoding="async">
                <div class="lookbook-overlay">
                    <i class="fab fa-instagram"></i>
                </div>
            </div>
            <div class="lookbook-item">
                <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=300&q=80" alt="Lookbook" loading="lazy" decoding="async">
                <div class="lookbook-overlay">
                    <i class="fab fa-instagram"></i>
                </div>
            </div>
            <div class="lookbook-item">
                <img src="https://images.unsplash.com/photo-1485968579169-11d4a1fa432d?w=300&q=80" alt="Lookbook" loading="lazy" decoding="async">
                <div class="lookbook-overlay">
                    <i class="fab fa-instagram"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Bar -->
<section class="features-bar">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="feature-text">
                    <h4>Free Shipping</h4>
                    <p>On orders over <?= formatPrice(FREE_SHIPPING_THRESHOLD) ?></p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <div class="feature-text">
                    <h4>Easy Returns</h4>
                    <p>30-day return policy</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="feature-text">
                    <h4>Secure Payment</h4>
                    <p>100% secure checkout</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="feature-text">
                    <h4>24/7 Support</h4>
                    <p>Dedicated support team</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-wrapper">
            <div class="newsletter-content">
                <span class="newsletter-tag">Newsletter</span>
                <h2>Stay in Style</h2>
                <p>Subscribe to get exclusive offers, new arrivals, and style tips delivered to your inbox.</p>
            </div>
            <form class="newsletter-form-modern" action="#" method="POST">
                <div class="form-group">
                    <input type="email" placeholder="Enter your email address" required>
                    <button type="submit">Subscribe <i class="fas fa-paper-plane"></i></button>
                </div>
                <p class="newsletter-note">By subscribing, you agree to our Privacy Policy</p>
            </form>
        </div>
    </div>
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
// Hero Slider
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.heroSwiper', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
});

// Add to Cart
function addToCart(productId) {
    fetch('<?= url('cart/add') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.cartCount || parseInt(cartCount.textContent) + 1;
            }
            showToast('Product added to cart!', 'success');
        } else {
            showToast(data.message || 'Error adding to cart', 'error');
        }
    });
}

// Add to Wishlist
function addToWishlist(productId) {
    fetch('<?= url('wishlist/add') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Added to wishlist!', 'success');
        } else {
            showToast(data.message || 'Please login to add to wishlist', 'warning');
        }
    });
}

// Quick View
function quickView(productId) {
    window.location.href = '<?= url('product') ?>/' + productId;
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = 'toast-notification ' + type;
    toast.textContent = message;
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
