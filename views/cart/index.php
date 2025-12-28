<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item active">Shopping Cart</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <h1 class="mb-4">Shopping Cart</h1>

        <?php if (empty($cart['items'])): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added any items to your cart yet</p>
            <a href="<?= url('shop') ?>" class="btn btn-primary">Start Shopping</a>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="cart-table">
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50%">Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart['items'] as $item): ?>
                            <?php
                            $price = $item['sale_price'] ?? $item['price'];
                            $price += $item['price_modifier'] ?? 0;
                            $itemTotal = $price * $item['quantity'];
                            ?>
                            <tr>
                                <td>
                                    <div class="cart-product">
                                        <?php if ($item['image']): ?>
                                        <img src="<?= upload($item['image']) ?>" alt="<?= sanitize($item['name']) ?>">
                                        <?php else: ?>
                                        <div class="img-placeholder" style="width: 80px; height: 100px;"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-1"><a href="<?= url('product/' . $item['slug']) ?>"><?= sanitize($item['name']) ?></a></h6>
                                            <?php if ($item['size'] || $item['color']): ?>
                                            <small class="text-muted">
                                                <?= $item['size'] ? 'Size: ' . $item['size'] : '' ?>
                                                <?= $item['color'] ? ' / Color: ' . $item['color'] : '' ?>
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <?= formatPrice($price) ?>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <button class="qty-btn" onclick="updateCartItem(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)">-</button>
                                        <input type="text" class="qty-input mx-2" value="<?= $item['quantity'] ?>" readonly style="width: 50px;">
                                        <button class="qty-btn" onclick="updateCartItem(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)">+</button>
                                    </div>
                                </td>
                                <td class="align-middle fw-bold">
                                    <?= formatPrice($itemTotal) ?>
                                </td>
                                <td class="align-middle">
                                    <button class="btn btn-link text-danger" onclick="removeCartItem(<?= $item['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= url('shop') ?>" class="btn btn-outline-dark">
                        <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                    </a>
                    <a href="<?= url('cart/clear') ?>" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-2"></i> Clear Cart
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4>Order Summary</h4>

                    <div class="summary-row">
                        <span>Subtotal (<?= $cart['item_count'] ?> items)</span>
                        <span><?= formatPrice($cart['subtotal']) ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>
                            <?php if ($cart['shipping'] > 0): ?>
                            <?= formatPrice($cart['shipping']) ?>
                            <?php else: ?>
                            <span class="text-success">Free</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <?php if ($cart['tax'] > 0): ?>
                    <div class="summary-row">
                        <span>Tax</span>
                        <span><?= formatPrice($cart['tax']) ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span class="text-accent"><?= formatPrice($cart['total']) ?></span>
                    </div>

                    <?php if ($cart['subtotal'] < FREE_SHIPPING_THRESHOLD): ?>
                    <div class="alert alert-info mt-3 small">
                        <i class="fas fa-truck me-2"></i>
                        Add <?= formatPrice(FREE_SHIPPING_THRESHOLD - $cart['subtotal']) ?> more for free shipping!
                    </div>
                    <?php endif; ?>

                    <a href="<?= url('checkout') ?>" class="btn btn-primary w-100 mt-3">
                        Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                    </a>

                    <div class="text-center mt-3">
                        <img src="https://cdn-icons-png.flaticon.com/32/349/349221.png" alt="Visa" class="me-1">
                        <img src="https://cdn-icons-png.flaticon.com/32/349/349228.png" alt="Mastercard" class="me-1">
                        <img src="https://cdn-icons-png.flaticon.com/32/349/349230.png" alt="PayPal">
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
