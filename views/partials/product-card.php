<?php $isOutOfStock = ($product['stock_quantity'] ?? 0) <= 0; ?>
<div class="product-card <?= $isOutOfStock ? 'out-of-stock' : '' ?>">
    <div class="product-image">
        <a href="<?= url('product/' . $product['slug']) ?>">
            <?php if ($product['image']): ?>
            <img src="<?= upload($product['image']) ?>" alt="<?= sanitize($product['name']) ?>" loading="lazy" decoding="async" width="280" height="280">
            <?php else: ?>
            <div class="img-placeholder" style="height: 100%;"><i class="fas fa-image"></i></div>
            <?php endif; ?>
        </a>

        <?php if ($isOutOfStock): ?>
        <div class="out-of-stock-overlay">
            <span>Out of Stock</span>
        </div>
        <?php endif; ?>

        <div class="product-badges">
            <?php if ($isOutOfStock): ?>
            <span class="badge-sold-out">Sold Out</span>
            <?php elseif ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
            <span class="badge-sale">-<?= discountPercent($product['price'], $product['sale_price']) ?>%</span>
            <?php endif; ?>
            <?php if ($product['is_new'] && !$isOutOfStock): ?>
            <span class="badge-new">New</span>
            <?php endif; ?>
        </div>

        <div class="product-actions">
            <button onclick="toggleWishlist(<?= $product['id'] ?>, this)" title="Add to Wishlist">
                <i class="fas fa-heart"></i>
            </button>
            <a href="<?= url('product/' . $product['slug']) ?>" title="Quick View">
                <i class="fas fa-eye"></i>
            </a>
            <?php if (!$isOutOfStock): ?>
            <button onclick="addToCart(<?= $product['id'] ?>)" title="Add to Cart">
                <i class="fas fa-shopping-cart"></i>
            </button>
            <?php else: ?>
            <button disabled title="Out of Stock" style="opacity: 0.5; cursor: not-allowed;">
                <i class="fas fa-shopping-cart"></i>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-info">
        <div class="product-category"><?= $product['category_name'] ?? 'Fashion' ?></div>
        <h3 class="product-name">
            <a href="<?= url('product/' . $product['slug']) ?>"><?= sanitize($product['name']) ?></a>
        </h3>
        <div class="product-price">
            <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
            <span class="current-price"><?= formatPrice($product['sale_price']) ?></span>
            <span class="original-price"><?= formatPrice($product['price']) ?></span>
            <?php else: ?>
            <span class="current-price"><?= formatPrice($product['price']) ?></span>
            <?php endif; ?>
        </div>
        <?php if ($isOutOfStock): ?>
        <div class="stock-status out-of-stock">
            <i class="fas fa-times-circle"></i> Out of Stock
        </div>
        <?php endif; ?>
    </div>
</div>
