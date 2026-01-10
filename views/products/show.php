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
                    <div class="main-image" id="mainImageContainer">
                        <?php $primaryImage = $product['images'][0]['image_path'] ?? null; ?>
                        <?php if ($primaryImage): ?>
                        <img src="<?= upload($primaryImage) ?>" alt="<?= sanitize($product['name']) ?>" id="mainProductImage">
                        <div class="zoom-icon" id="zoomIconBtn" title="Click to enlarge">
                            <i class="fas fa-search-plus"></i>
                        </div>
                        <div id="zoomLens"></div>
                        <?php else: ?>
                        <div class="img-placeholder" style="height: 500px;"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </div>
                    <?php if (count($product['images']) > 0): ?>
                    <div class="thumbnail-images" id="thumbnailContainer">
                        <?php foreach ($product['images'] as $index => $image): ?>
                        <img src="<?= upload($image['image_path']) ?>"
                             alt="<?= $image['alt_text'] ?? $product['name'] ?>"
                             class="thumb-img <?= $index === 0 ? 'active' : '' ?>"
                             data-large="<?= upload($image['image_path']) ?>"
                             data-index="<?= $index ?>">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Zoom Result (positioned fixed, outside container) -->
                <div id="zoomResult"></div>
            </div>

            <!-- Lightbox Gallery -->
            <div class="lightbox-overlay" id="lightboxOverlay">
                <div class="lightbox-content" id="lightboxContent">
                    <button class="lightbox-close" id="lightboxClose">&times;</button>
                    <button class="lightbox-nav lightbox-prev" id="lightboxPrev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <img src="" alt="" class="lightbox-image" id="lightboxImage">
                    <button class="lightbox-nav lightbox-next" id="lightboxNext">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <div class="lightbox-thumbnails" id="lightboxThumbnails">
                        <?php foreach ($product['images'] as $index => $image): ?>
                        <img src="<?= upload($image['image_path']) ?>"
                             alt="<?= $image['alt_text'] ?? $product['name'] ?>"
                             class="lb-thumb <?= $index === 0 ? 'active' : '' ?>"
                             data-index="<?= $index ?>">
                        <?php endforeach; ?>
                    </div>
                    <div class="lightbox-counter">
                        <span id="currentSlide">1</span> / <span id="totalSlides"><?= count($product['images']) ?></span>
                    </div>
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
                        // Get unique colors from variants
                        $uniqueColors = [];
                        foreach ($product['variants'] as $variant) {
                            if (!empty($variant['color']) && !isset($uniqueColors[$variant['color']])) {
                                $uniqueColors[$variant['color']] = $variant;
                            }
                        }
                        if (!empty($uniqueColors)):
                        ?>
                        <div class="variant-selector">
                            <label>Color</label>
                            <div class="color-options">
                                <?php foreach ($uniqueColors as $colorName => $variant):
                                    $colorCode = $variant['color_code'] ?? $variant['color'];
                                    $isWhite = strtolower($colorCode) === '#ffffff' || strtolower($colorCode) === 'white' || strtolower($colorCode) === '#fff';
                                ?>
                                <span class="color-option"
                                      style="background: <?= $colorCode ?>; <?= $isWhite ? 'border: 2px solid #ccc;' : '' ?>"
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
                        <?php if ($product['stock_quantity'] > 0): ?>
                        <button type="button" class="btn btn-primary add-to-cart-btn" onclick="addProductToCart()">
                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                        </button>
                        <a href="<?= url('checkout') ?>" class="btn btn-outline-dark buy-now-btn" onclick="addProductToCart(); return true;">
                            <i class="fas fa-bolt me-2"></i> Buy Now
                        </a>
                        <?php else: ?>
                        <button type="button" class="btn btn-secondary add-to-cart-btn" disabled>
                            <i class="fas fa-times-circle me-2"></i> Out of Stock
                        </button>
                        <button type="button" class="btn btn-outline-secondary buy-now-btn" disabled>
                            <i class="fas fa-bell me-2"></i> Notify When Available
                        </button>
                        <?php endif; ?>
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

<script type="text/javascript">
// Product images array - built from data attributes
var productImages = [];
var currentImageIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Build images array from thumbnail data
    var thumbs = document.querySelectorAll('#thumbnailContainer .thumb-img');
    thumbs.forEach(function(thumb) {
        productImages.push(thumb.dataset.large);
    });

    // If no thumbnails, try main image
    if (productImages.length === 0) {
        var mainImg = document.getElementById('mainProductImage');
        if (mainImg) {
            productImages.push(mainImg.src);
        }
    }
});

function addProductToCart() {
    var form = document.getElementById('addToCartForm');
    var formData = new FormData(form);

    fetch(SITE_URL + '/cart/add', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showNotification('Product added to cart!', 'success');
            document.querySelectorAll('.cart-count').forEach(function(el) {
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
            title: document.title,
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        showNotification('Link copied to clipboard!', 'success');
    }
}

// Change main image when clicking thumbnail
function changeMainImage(thumb, index) {
    var mainImage = document.getElementById('mainProductImage');
    var largeSrc = thumb.dataset.large;

    mainImage.src = largeSrc;
    currentImageIndex = index;

    // Update active thumbnail
    document.querySelectorAll('.thumbnail-images img').forEach(function(img) {
        img.classList.remove('active');
    });
    thumb.classList.add('active');

    // Re-init zoom for new image
    initZoom();
}

// Zoom functionality - Modern with animations
function initZoom() {
    var container = document.getElementById('mainImageContainer');
    var img = document.getElementById('mainProductImage');
    var lens = document.getElementById('zoomLens');
    var result = document.getElementById('zoomResult');

    if (!img || !lens || !result || !container) {
        console.log('Zoom elements not found');
        return;
    }

    // Zoom multiplier (higher = more zoom)
    var zoomLevel = 2.8;

    function updateZoom() {
        result.style.backgroundImage = "url('" + img.src + "')";
        result.style.backgroundSize = (img.offsetWidth * zoomLevel) + "px " + (img.offsetHeight * zoomLevel) + "px";
    }

    function showZoom() {
        updateZoom();
        lens.style.display = 'block';
        result.style.display = 'block';
        // Trigger reflow for animation
        lens.offsetHeight;
        result.offsetHeight;
        lens.classList.add('active');
        result.classList.add('active');
    }

    function hideZoom() {
        lens.classList.remove('active');
        result.classList.remove('active');
        // Hide after animation
        setTimeout(function() {
            if (!lens.classList.contains('active')) {
                lens.style.display = 'none';
                result.style.display = 'none';
            }
        }, 250);
    }

    function moveZoom(e) {
        var rect = img.getBoundingClientRect();

        // Mouse position relative to image
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;

        // Keep within bounds
        x = Math.max(0, Math.min(x, rect.width));
        y = Math.max(0, Math.min(y, rect.height));

        // Position lens centered on cursor
        var lensX = x - (lens.offsetWidth / 2);
        var lensY = y - (lens.offsetHeight / 2);
        lens.style.left = lensX + 'px';
        lens.style.top = lensY + 'px';

        // Calculate background position for zoom result
        var bgX = x * zoomLevel - (result.offsetWidth / 2);
        var bgY = y * zoomLevel - (result.offsetHeight / 2);
        result.style.backgroundPosition = "-" + bgX + "px -" + bgY + "px";
    }

    // Remove previous listeners to avoid duplicates
    container.onmouseenter = showZoom;
    container.onmouseleave = hideZoom;
    container.onmousemove = moveZoom;

    console.log('Zoom initialized successfully');
}

// Lightbox functionality
function openLightbox(index = 0) {
    currentImageIndex = index;
    const overlay = document.getElementById('lightboxOverlay');
    const image = document.getElementById('lightboxImage');

    image.src = productImages[currentImageIndex];
    updateLightboxCounter();
    updateLightboxThumbnails();

    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const overlay = document.getElementById('lightboxOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

function navigateLightbox(direction) {
    currentImageIndex += direction;

    if (currentImageIndex >= productImages.length) {
        currentImageIndex = 0;
    } else if (currentImageIndex < 0) {
        currentImageIndex = productImages.length - 1;
    }

    document.getElementById('lightboxImage').src = productImages[currentImageIndex];
    updateLightboxCounter();
    updateLightboxThumbnails();
}

function goToSlide(index) {
    currentImageIndex = index;
    document.getElementById('lightboxImage').src = productImages[currentImageIndex];
    updateLightboxCounter();
    updateLightboxThumbnails();
}

function updateLightboxCounter() {
    document.getElementById('currentSlide').textContent = currentImageIndex + 1;
}

function updateLightboxThumbnails() {
    const thumbs = document.querySelectorAll('#lightboxThumbnails .lb-thumb');
    thumbs.forEach((thumb, i) => {
        thumb.classList.toggle('active', i === currentImageIndex);
    });
}

// Keyboard navigation for lightbox
document.addEventListener('keydown', function(e) {
    const overlay = document.getElementById('lightboxOverlay');
    if (!overlay.classList.contains('active')) return;

    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') navigateLightbox(-1);
    if (e.key === 'ArrowRight') navigateLightbox(1);
});

// Initialize zoom and lightbox on page load
document.addEventListener('DOMContentLoaded', function() {
    const mainImg = document.getElementById('mainProductImage');
    const zoomIcon = document.getElementById('zoomIconBtn');
    const thumbnailContainer = document.getElementById('thumbnailContainer');

    // Lightbox elements
    const lightboxOverlay = document.getElementById('lightboxOverlay');
    const lightboxContent = document.getElementById('lightboxContent');
    const lightboxClose = document.getElementById('lightboxClose');
    const lightboxPrev = document.getElementById('lightboxPrev');
    const lightboxNext = document.getElementById('lightboxNext');
    const lightboxThumbnails = document.getElementById('lightboxThumbnails');

    // Set up main image click handler
    if (mainImg) {
        mainImg.style.cursor = 'pointer';
        mainImg.addEventListener('click', function() {
            openLightbox(currentImageIndex);
        });
    }

    if (zoomIcon) {
        zoomIcon.addEventListener('click', function() {
            openLightbox(currentImageIndex);
        });
    }

    // Set up thumbnail click handlers using event delegation
    if (thumbnailContainer) {
        thumbnailContainer.addEventListener('click', function(e) {
            const thumb = e.target.closest('.thumb-img');
            if (thumb) {
                const index = parseInt(thumb.dataset.index, 10);
                changeMainImage(thumb, index);
            }
        });
    }

    // Lightbox event handlers
    if (lightboxOverlay) {
        lightboxOverlay.addEventListener('click', function(e) {
            if (e.target === lightboxOverlay) {
                closeLightbox();
            }
        });
    }

    if (lightboxContent) {
        lightboxContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }

    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', function() {
            navigateLightbox(-1);
        });
    }

    if (lightboxNext) {
        lightboxNext.addEventListener('click', function() {
            navigateLightbox(1);
        });
    }

    if (lightboxThumbnails) {
        lightboxThumbnails.addEventListener('click', function(e) {
            const thumb = e.target.closest('.lb-thumb');
            if (thumb) {
                const index = parseInt(thumb.dataset.index, 10);
                goToSlide(index);
            }
        });
    }

    // Initialize zoom
    if (mainImg) {
        if (mainImg.complete && mainImg.naturalHeight !== 0) {
            setTimeout(initZoom, 100);
        } else {
            mainImg.addEventListener('load', function() {
                setTimeout(initZoom, 100);
            });
        }
    }
});

// Fallback: also try on window load
window.addEventListener('load', function() {
    setTimeout(initZoom, 200);
});
</script>
