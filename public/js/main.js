/**
 * Waslah Fashion - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // Cart Functions
    // ============================================

    // Add to Cart
    window.addToCart = function(productId, variantId = null, quantity = 1) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        if (variantId) {
            formData.append('variant_id', variantId);
        }

        fetch(SITE_URL + '/cart/add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cartCount);
                showNotification('Product added to cart!', 'success');
            } else {
                showNotification(data.message || 'Error adding to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error adding to cart', 'error');
        });
    };

    // Update Cart Item Quantity
    window.updateCartItem = function(itemId, quantity) {
        const formData = new FormData();
        formData.append('item_id', itemId);
        formData.append('quantity', quantity);

        fetch(SITE_URL + '/cart/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showNotification(data.message || 'Error updating cart', 'error');
            }
        });
    };

    // Remove Cart Item
    window.removeCartItem = function(itemId) {
        if (!confirm('Remove this item from cart?')) return;

        const formData = new FormData();
        formData.append('item_id', itemId);

        fetch(SITE_URL + '/cart/remove', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    };

    // Update Cart Count
    function updateCartCount(count) {
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(el => {
            if (count > 0) {
                el.textContent = count;
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
            }
        });
    }

    // ============================================
    // Wishlist Functions
    // ============================================

    window.toggleWishlist = function(productId, button) {
        const formData = new FormData();
        formData.append('product_id', productId);

        fetch(SITE_URL + '/wishlist/add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'added') {
                    button.classList.add('text-danger');
                    showNotification('Added to wishlist!', 'success');
                } else {
                    button.classList.remove('text-danger');
                    showNotification('Removed from wishlist', 'info');
                }
            } else if (data.redirect) {
                window.location.href = SITE_URL + '/login';
            }
        });
    };

    // ============================================
    // Product Gallery
    // ============================================

    const thumbnails = document.querySelectorAll('.thumbnail-images img');
    const mainImage = document.querySelector('.main-image img');

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            mainImage.src = this.dataset.large || this.src;
        });
    });

    // ============================================
    // Quantity Selector
    // ============================================

    const qtyInput = document.querySelector('.qty-input');
    const qtyMinus = document.querySelector('.qty-minus');
    const qtyPlus = document.querySelector('.qty-plus');

    if (qtyInput && qtyMinus && qtyPlus) {
        qtyMinus.addEventListener('click', function() {
            let value = parseInt(qtyInput.value) || 1;
            if (value > 1) {
                qtyInput.value = value - 1;
            }
        });

        qtyPlus.addEventListener('click', function() {
            let value = parseInt(qtyInput.value) || 1;
            const max = parseInt(qtyInput.max) || 99;
            if (value < max) {
                qtyInput.value = value + 1;
            }
        });
    }

    // ============================================
    // Size & Color Selection
    // ============================================

    const sizeOptions = document.querySelectorAll('.size-option');
    sizeOptions.forEach(option => {
        option.addEventListener('click', function() {
            sizeOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('selectedSize').value = this.dataset.size;
        });
    });

    const colorOptions = document.querySelectorAll('.color-option');
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            colorOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('selectedColor').value = this.dataset.color;
        });
    });

    // ============================================
    // Payment Method Selection
    // ============================================

    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            paymentMethods.forEach(m => m.classList.remove('active'));
            this.classList.add('active');
            this.querySelector('input[type="radio"]').checked = true;

            // Show/hide payment forms
            const gateway = this.dataset.gateway;
            document.querySelectorAll('.payment-form').forEach(form => {
                form.style.display = 'none';
            });
            const activeForm = document.getElementById(gateway + '-form');
            if (activeForm) {
                activeForm.style.display = 'block';
            }
        });
    });

    // ============================================
    // Search
    // ============================================

    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const input = this.querySelector('input[name="q"]');
            if (!input.value.trim()) {
                e.preventDefault();
            }
        });
    }

    // ============================================
    // Price Range Filter
    // ============================================

    const priceFilter = document.getElementById('priceFilterForm');
    if (priceFilter) {
        priceFilter.addEventListener('submit', function(e) {
            e.preventDefault();
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;
            const url = new URL(window.location.href);
            if (minPrice) url.searchParams.set('min_price', minPrice);
            if (maxPrice) url.searchParams.set('max_price', maxPrice);
            window.location.href = url.toString();
        });
    }

    // ============================================
    // Notifications
    // ============================================

    window.showNotification = function(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} notification-toast`;
        notification.innerHTML = message;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    };

    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // ============================================
    // Form Validation
    // ============================================

    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // ============================================
    // Image Loading Handler
    // ============================================

    // Handle lazy loaded images - add loaded class for fade-in
    function handleImageLoad(img) {
        img.classList.add('loaded');
    }

    // Process all lazy images
    document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        if (img.complete) {
            handleImageLoad(img);
        } else {
            img.addEventListener('load', () => handleImageLoad(img));
            img.addEventListener('error', () => {
                img.classList.add('loaded');
                img.style.background = '#f0f0f0';
            });
        }
    });

    // Observe new images added dynamically
    const imgObserver = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1) {
                    const imgs = node.tagName === 'IMG' ? [node] : node.querySelectorAll?.('img[loading="lazy"]') || [];
                    imgs.forEach(img => {
                        if (img.complete) {
                            handleImageLoad(img);
                        } else {
                            img.addEventListener('load', () => handleImageLoad(img));
                        }
                    });
                }
            });
        });
    });
    imgObserver.observe(document.body, { childList: true, subtree: true });

    // Lazy Loading Images with data-src
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    observer.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // ============================================
    // Smooth Scroll
    // ============================================

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

});
