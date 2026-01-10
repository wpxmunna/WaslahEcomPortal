<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<!-- Hero Slider Section -->
<section class="hero-slider">
    <div class="swiper heroSwiper">
        <div class="swiper-wrapper">
            <?php
            // Default sliders if none in database
            $defaultSliders = [
                [
                    'title' => 'Elegance Redefined',
                    'subtitle' => 'New Collection 2025',
                    'description' => 'Discover the perfect blend of tradition and modernity',
                    'button_text' => 'Shop Women',
                    'button_link' => 'shop/category/women',
                    'button2_text' => 'Shop Men',
                    'button2_link' => 'shop/category/men',
                    'image' => null,
                    'text_position' => 'left',
                    'text_color' => '#ffffff',
                    'overlay_opacity' => 0.40,
                    'default_bg' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1200&q=80'
                ],
                [
                    'title' => 'Authenticity in Every Stitch',
                    'subtitle' => 'Premium Quality',
                    'description' => 'Handcrafted with love, designed for you',
                    'button_text' => 'Explore Collection',
                    'button_link' => 'shop',
                    'button2_text' => null,
                    'button2_link' => null,
                    'image' => null,
                    'text_position' => 'left',
                    'text_color' => '#ffffff',
                    'overlay_opacity' => 0.40,
                    'default_bg' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1200&q=80'
                ],
                [
                    'title' => 'Style for Little Ones',
                    'subtitle' => 'Kids Collection',
                    'description' => 'Comfort meets fashion for your children',
                    'button_text' => 'Shop Kids',
                    'button_link' => 'shop/category/children',
                    'button2_text' => null,
                    'button2_link' => null,
                    'image' => null,
                    'text_position' => 'left',
                    'text_color' => '#ffffff',
                    'overlay_opacity' => 0.40,
                    'default_bg' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&q=80'
                ]
            ];

            // Use database sliders if available, otherwise use defaults
            $displaySliders = !empty($sliders) ? $sliders : $defaultSliders;
            $slideIndex = 0;

            // Fallback images for sliders without uploaded images
            $fallbackImages = [
                'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1200&q=80',
                'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1200&q=80',
                'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&q=80',
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&q=80'
            ];

            foreach ($displaySliders as $slider):
                // Determine background image
                if (!empty($slider['image'])) {
                    $bgImage = upload('sliders/' . $slider['image']);
                } elseif (!empty($slider['default_bg'])) {
                    $bgImage = $slider['default_bg'];
                } else {
                    // Use different fallback images based on slide position
                    $bgImage = $fallbackImages[$slideIndex % count($fallbackImages)];
                }

                $textPosition = $slider['text_position'] ?? 'left';
                $textColor = $slider['text_color'] ?? '#ffffff';
                $overlayOpacity = $slider['overlay_opacity'] ?? 0.40;
            ?>
            <div class="swiper-slide">
                <div class="hero-slide" style="background-image: url('<?= $bgImage ?>');">
                    <div class="hero-overlay" style="background: rgba(0,0,0,<?= $overlayOpacity ?>);"></div>
                    <div class="container">
                        <div class="hero-content text-<?= $textPosition ?>" style="color: <?= $textColor ?>;">
                            <?php if (!empty($slider['subtitle'])): ?>
                            <span class="hero-subtitle"><?= sanitize($slider['subtitle']) ?></span>
                            <?php endif; ?>
                            <h1 class="hero-title"><?= nl2br(sanitize($slider['title'])) ?></h1>
                            <?php if (!empty($slider['description'])): ?>
                            <p class="hero-desc"><?= sanitize($slider['description']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($slider['button_text']) || !empty($slider['button2_text'])): ?>
                            <div class="hero-buttons">
                                <?php if (!empty($slider['button_text'])): ?>
                                <a href="<?= url($slider['button_link'] ?? 'shop') ?>" class="btn-hero-primary"><?= sanitize($slider['button_text']) ?></a>
                                <?php endif; ?>
                                <?php if (!empty($slider['button2_text'])): ?>
                                <a href="<?= url($slider['button2_link'] ?? 'shop') ?>" class="btn-hero-secondary"><?= sanitize($slider['button2_text']) ?></a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php $slideIndex++; endforeach; ?>
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
<section class="new-arrivals-section">
    <div class="container">
        <div class="section-header centered" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-sparkles me-2"></i>Just Landed</span>
            <h2 class="section-title">New Arrivals</h2>
            <p class="section-desc">Discover the latest additions to our collection</p>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($newArrivals, 0, 8) as $product): ?>
            <div class="col-lg-3 col-md-4 col-6" data-aos="fade-up">
                <?php include ROOT_PATH . '/views/partials/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?= url('shop?sort=newest') ?>" class="btn btn-outline-dark btn-lg px-5">
                View All New Arrivals <i class="fas fa-arrow-right ms-2"></i>
            </a>
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
<section class="featured-section">
    <div class="container">
        <div class="section-header centered" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-gem me-2"></i>Curated For You</span>
            <h2 class="section-title">Featured Collection</h2>
            <p class="section-desc">Hand-picked pieces that define elegance and style</p>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($featuredProducts, 0, 8) as $product): ?>
            <div class="col-lg-3 col-md-4 col-6" data-aos="fade-up">
                <?php include ROOT_PATH . '/views/partials/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?= url('shop') ?>" class="btn btn-outline-dark btn-lg px-5">
                View All Products <i class="fas fa-arrow-right ms-2"></i>
            </a>
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

        <?php
        // Default lookbook items if none in database
        $defaultLookbook = [
            ['image' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=500&q=80', 'caption' => 'Summer Collection', 'link' => '', 'is_featured' => 1],
            ['image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=300&q=80', 'caption' => 'Street Style', 'link' => '', 'is_featured' => 0],
            ['image' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=300&q=80', 'caption' => 'Elegant Look', 'link' => '', 'is_featured' => 0],
            ['image' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=300&q=80', 'caption' => 'Casual Vibes', 'link' => '', 'is_featured' => 0],
            ['image' => 'https://images.unsplash.com/photo-1485968579169-11d4a1fa432d?w=300&q=80', 'caption' => 'Evening Wear', 'link' => '', 'is_featured' => 0],
        ];

        $displayLookbook = !empty($lookbookItems) ? $lookbookItems : $defaultLookbook;
        $lookbookIndex = 0;
        ?>

        <div class="lookbook-grid">
            <?php foreach (array_slice($displayLookbook, 0, 5) as $item):
                // Determine image URL
                if (filter_var($item['image'], FILTER_VALIDATE_URL)) {
                    $imageUrl = $item['image'];
                } elseif (!empty($item['image'])) {
                    $imageUrl = upload('lookbook/' . $item['image']);
                } else {
                    $imageUrl = 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=500&q=80';
                }

                // First item or featured item gets 'large' class
                $isLarge = ($lookbookIndex === 0) || (!empty($item['is_featured']));
                $itemClass = $isLarge ? 'lookbook-item large' : 'lookbook-item';
                $linkUrl = !empty($item['link']) ? $item['link'] : '#';
            ?>
            <<?= !empty($item['link']) ? 'a href="' . sanitize($item['link']) . '" target="_blank"' : 'div' ?> class="<?= $itemClass ?>">
                <img src="<?= $imageUrl ?>" alt="<?= sanitize($item['caption'] ?? 'Lookbook') ?>" loading="lazy" decoding="async">
                <div class="lookbook-overlay">
                    <i class="fab fa-instagram"></i>
                    <?php if (!empty($item['caption'])): ?>
                    <span class="lookbook-caption"><?= sanitize($item['caption']) ?></span>
                    <?php endif; ?>
                </div>
            </<?= !empty($item['link']) ? 'a' : 'div' ?>>
            <?php $lookbookIndex++; endforeach; ?>
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
