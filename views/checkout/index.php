<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('cart') ?>">Cart</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <h1 class="mb-4">Checkout</h1>

        <form action="<?= url('checkout/process') ?>" method="POST" id="checkoutForm">
            <?= csrfField() ?>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Shipping Information -->
                    <div class="checkout-section">
                        <h3><i class="fas fa-truck me-2"></i> Shipping Information</h3>

                        <?php if (!empty($addresses)): ?>
                        <div class="mb-4">
                            <label class="form-label">Select saved address</label>
                            <select class="form-select" id="savedAddress" onchange="fillAddress(this.value)">
                                <option value="">Enter new address</option>
                                <?php foreach ($addresses as $addr): ?>
                                <option value="<?= $addr['id'] ?>"
                                        data-name="<?= $addr['name'] ?>"
                                        data-phone="<?= $addr['phone'] ?>"
                                        data-address="<?= $addr['address_line1'] ?>"
                                        data-address2="<?= $addr['address_line2'] ?>"
                                        data-city="<?= $addr['city'] ?>"
                                        data-state="<?= $addr['state'] ?>"
                                        data-postal="<?= $addr['postal_code'] ?>"
                                        data-country="<?= $addr['country'] ?>">
                                    <?= $addr['label'] ?>: <?= $addr['address_line1'] ?>, <?= $addr['city'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="shipping_name"
                                       value="<?= $user['name'] ?? '' ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="tel" class="form-control" name="shipping_phone"
                                       value="<?= $user['phone'] ?? '' ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address *</label>
                            <input type="text" class="form-control" name="shipping_address"
                                   placeholder="Street address" required>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="shipping_address2"
                                   placeholder="Apartment, suite, etc. (optional)">
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">City *</label>
                                <input type="text" class="form-control" name="shipping_city" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">State/Province *</label>
                                <input type="text" class="form-control" name="shipping_state" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Postal Code *</label>
                                <input type="text" class="form-control" name="shipping_postal" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Country *</label>
                            <select class="form-select" name="shipping_country">
                                <option value="United States">United States</option>
                                <option value="Canada">Canada</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Australia">Australia</option>
                                <option value="Germany">Germany</option>
                                <option value="France">France</option>
                            </select>
                        </div>
                    </div>

                    <!-- Shipping Info Notice -->
                    <div class="checkout-section">
                        <h3><i class="fas fa-shipping-fast me-2"></i> Shipping</h3>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Shipping charges will be calculated and confirmed by our team. You will be notified of the final shipping cost.
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-section">
                        <h3><i class="fas fa-credit-card me-2"></i> Payment Method</h3>

                        <?php foreach ($paymentMethods as $index => $method): ?>
                        <div class="payment-method <?= $index === 0 ? 'active' : '' ?>" data-gateway="<?= $method['id'] ?>">
                            <input type="radio" name="payment_method" value="<?= $method['id'] ?>"
                                   id="payment_<?= $method['id'] ?>" <?= $index === 0 ? 'checked' : '' ?>>
                            <label for="payment_<?= $method['id'] ?>" class="mb-0 ms-2">
                                <i class="fas <?= $method['icon'] ?> me-2"></i>
                                <strong><?= $method['name'] ?></strong>
                                <small class="text-muted d-block ms-4"><?= $method['description'] ?></small>
                            </label>
                        </div>
                        <?php endforeach; ?>

                        <!-- Card Payment Form -->
                        <div id="stripe-form" class="payment-form mt-4">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" class="form-control" name="card_number"
                                           placeholder="4242 4242 4242 4242" maxlength="19">
                                    <small class="text-muted">Test: 4242424242424242 (success) or 4000000000000002 (decline)</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" name="card_expiry"
                                           placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" name="card_cvv"
                                           placeholder="123" maxlength="4">
                                </div>
                            </div>
                        </div>

                        <!-- PayPal Form -->
                        <div id="paypal-form" class="payment-form mt-4" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">PayPal Email</label>
                                <input type="email" class="form-control" name="paypal_email"
                                       placeholder="your@email.com">
                            </div>
                        </div>

                        <!-- COD Message -->
                        <div id="cod-form" class="payment-form mt-4" style="display: none;">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                You will pay <?= formatPrice($cart['total']) ?> when your order is delivered.
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="checkout-section">
                        <h3><i class="fas fa-sticky-note me-2"></i> Order Notes (Optional)</h3>
                        <textarea class="form-control" name="notes" rows="3"
                                  placeholder="Any special instructions for your order..."></textarea>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary" style="position: sticky; top: 100px;">
                        <h4>Order Summary</h4>

                        <!-- Order Items -->
                        <div class="mb-4">
                            <?php foreach ($cart['items'] as $item): ?>
                            <?php $price = $item['sale_price'] ?? $item['price']; ?>
                            <div class="d-flex align-items-center mb-3">
                                <?php if ($item['image']): ?>
                                <img src="<?= upload($item['image']) ?>" alt="" style="width: 60px; height: 75px; object-fit: cover; border-radius: 4px;">
                                <?php endif; ?>
                                <div class="ms-3 flex-grow-1">
                                    <h6 class="mb-0" style="font-size: 14px;"><?= truncate($item['name'], 30) ?></h6>
                                    <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                </div>
                                <span class="fw-bold"><?= formatPrice($price * $item['quantity']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?= formatPrice($cart['subtotal']) ?></span>
                        </div>

                        <div class="summary-row">
                            <span>Shipping</span>
                            <span id="shippingAmount" class="text-muted">
                                <small>To be confirmed</small>
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
                            <span class="text-accent" id="orderTotal"><?= formatPrice($cart['total']) ?></span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-4">
                            <i class="fas fa-lock me-2"></i> Place Order
                        </button>

                        <p class="text-center text-muted small mt-3">
                            <i class="fas fa-shield-alt me-1"></i> Your payment information is secure
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
function fillAddress(addressId) {
    if (!addressId) return;

    const option = document.querySelector(`#savedAddress option[value="${addressId}"]`);
    if (!option) return;

    document.querySelector('[name="shipping_name"]').value = option.dataset.name || '';
    document.querySelector('[name="shipping_phone"]').value = option.dataset.phone || '';
    document.querySelector('[name="shipping_address"]').value = option.dataset.address || '';
    document.querySelector('[name="shipping_address2"]').value = option.dataset.address2 || '';
    document.querySelector('[name="shipping_city"]').value = option.dataset.city || '';
    document.querySelector('[name="shipping_state"]').value = option.dataset.state || '';
    document.querySelector('[name="shipping_postal"]').value = option.dataset.postal || '';
    document.querySelector('[name="shipping_country"]').value = option.dataset.country || '';
}

// Format card number with spaces
document.querySelector('[name="card_number"]')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
    let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formatted;
});

// Format expiry date
document.querySelector('[name="card_expiry"]')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2);
    }
    e.target.value = value;
});
</script>
