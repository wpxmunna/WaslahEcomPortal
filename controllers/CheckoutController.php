<?php
/**
 * Checkout Controller
 */

class CheckoutController extends Controller
{
    private Cart $cartModel;
    private Order $orderModel;
    private Coupon $couponModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        $this->couponModel = new Coupon();
    }

    /**
     * Checkout page
     */
    public function index(): void
    {
        $cart = $this->getCart();
        $cartData = $this->cartModel->getTotals($cart['id']);

        if (empty($cartData['items'])) {
            $this->redirect('cart', 'Your cart is empty', 'warning');
            return;
        }

        // Get user addresses if logged in
        $addresses = [];
        if (Session::isLoggedIn()) {
            $userModel = new User();
            $user = $userModel->getWithAddresses(Session::getUserId());
            $addresses = $user['addresses'] ?? [];
        }

        // Get available couriers
        $courierModel = new Courier();
        $couriers = $courierModel->getActive();

        $data = [
            'pageTitle' => 'Checkout - ' . SITE_NAME,
            'cart' => $cartData,
            'addresses' => $addresses,
            'couriers' => $couriers,
            'paymentMethods' => PaymentGateway::getAvailableMethods()
        ];

        $this->view('checkout/index', $data);
    }

    /**
     * Process checkout
     */
    public function process(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('checkout');
            return;
        }

        $cart = $this->getCart();
        $cartData = $this->cartModel->getTotals($cart['id']);

        if (empty($cartData['items'])) {
            $this->redirect('cart', 'Your cart is empty', 'warning');
            return;
        }

        // Validate stock for all items before checkout
        $productModel = new Product();
        $outOfStockItems = [];

        foreach ($cartData['items'] as $item) {
            $product = $productModel->find($item['product_id']);

            if (!$product || $product['status'] !== 'active') {
                $outOfStockItems[] = $item['product_name'] . ' (no longer available)';
                continue;
            }

            // Check variant stock if applicable
            if (!empty($item['variant_id'])) {
                $variant = $this->db->fetch(
                    "SELECT stock_quantity FROM product_variants WHERE id = ?",
                    [$item['variant_id']]
                );
                $availableStock = $variant['stock_quantity'] ?? 0;
            } else {
                $availableStock = $product['stock_quantity'];
            }

            if ($availableStock <= 0) {
                $outOfStockItems[] = $item['product_name'] . ' (out of stock)';
            } elseif ($item['quantity'] > $availableStock) {
                $outOfStockItems[] = $item['product_name'] . ' (only ' . $availableStock . ' available)';
            }
        }

        if (!empty($outOfStockItems)) {
            $message = 'Cannot complete checkout. The following items have stock issues: ' . implode(', ', $outOfStockItems);
            $this->redirect('cart', $message, 'error');
            return;
        }

        // Handle coupon discount
        $couponId = (int) $this->post('coupon_id', 0);
        $discountAmount = (float) $this->post('discount_amount', 0);
        $couponCode = '';
        $couponType = null;
        $giftProduct = null;
        $isFreeShipping = false;

        // Validate coupon again on server side
        if ($couponId > 0) {
            $coupon = $this->couponModel->find($couponId);
            if ($coupon) {
                $storeId = Session::get('current_store_id', 1);
                $validation = $this->couponModel->validate($coupon['code'], $storeId, $cartData['subtotal']);
                if ($validation['valid']) {
                    $couponCode = $coupon['code'];
                    $couponType = $coupon['type'];

                    // Handle different coupon types
                    switch ($coupon['type']) {
                        case 'free_shipping':
                            $isFreeShipping = true;
                            $discountAmount = 0; // Shipping handled separately
                            break;

                        case 'gift_item':
                            $giftProduct = $this->couponModel->getGiftProduct($couponId);
                            $discountAmount = 0;
                            break;

                        default:
                            $discountAmount = $this->couponModel->calculateDiscount($coupon, $cartData['subtotal']);
                    }
                } else {
                    $couponId = 0;
                    $discountAmount = 0;
                }
            } else {
                $couponId = 0;
                $discountAmount = 0;
            }
        }

        // Prepare order data
        $orderData = [
            'store_id' => Session::get('current_store_id', 1),
            'user_id' => Session::getUserId(),
            'payment_method' => $this->post('payment_method', 'cod'),

            // Coupon
            'coupon_id' => $couponId ?: null,
            'coupon_code' => $couponCode,
            'discount_amount' => $discountAmount,

            // Shipping address
            'shipping_name' => $this->post('shipping_name'),
            'shipping_phone' => $this->post('shipping_phone'),
            'shipping_address_line1' => $this->post('shipping_address'),
            'shipping_address_line2' => $this->post('shipping_address2'),
            'shipping_city' => $this->post('shipping_city'),
            'shipping_state' => $this->post('shipping_state'),
            'shipping_postal_code' => $this->post('shipping_postal'),
            'shipping_country' => $this->post('shipping_country', 'United States'),

            // Billing (same as shipping for now)
            'billing_name' => $this->post('shipping_name'),
            'billing_phone' => $this->post('shipping_phone'),
            'billing_address_line1' => $this->post('shipping_address'),
            'billing_address_line2' => $this->post('shipping_address2'),
            'billing_city' => $this->post('shipping_city'),
            'billing_state' => $this->post('shipping_state'),
            'billing_postal_code' => $this->post('shipping_postal'),
            'billing_country' => $this->post('shipping_country', 'United States'),

            'notes' => $this->post('notes')
        ];

        try {
            // If free shipping coupon, set shipping to 0 in cart data
            if ($isFreeShipping) {
                $cartData['shipping'] = 0;
                $cartData['total'] = $cartData['subtotal'] + $cartData['tax']; // Recalculate without shipping
            }

            // Create order
            $orderId = $this->orderModel->createFromCart($cartData, $orderData);

            // Add gift item to order if applicable
            if ($giftProduct && $orderId) {
                $this->orderModel->addGiftItem($orderId, $giftProduct, $couponCode);
            }

            // Increment coupon usage if used
            if ($couponId > 0) {
                $this->couponModel->incrementUsage($couponId);
            }

            // Get order details
            $order = $this->orderModel->find($orderId);

            // Process payment
            $paymentGateway = new PaymentGateway($orderData['payment_method']);
            $paymentData = [
                'card_number' => $this->post('card_number'),
                'card_expiry' => $this->post('card_expiry'),
                'card_cvv' => $this->post('card_cvv'),
                'paypal_email' => $this->post('paypal_email'),
                'method' => $orderData['payment_method']
            ];

            $paymentResult = $paymentGateway->processPayment($orderId, $paymentData);

            // Create shipment
            $courierId = (int) $this->post('courier_id', 1);
            $courierService = new CourierService();
            $courierService->createShipment($orderId, $courierId);

            // Clear cart
            $this->cartModel->clearCart($cart['id']);

            // Store order in session for confirmation page
            Session::set('last_order_id', $orderId);

            $this->redirect('order/success/' . $order['order_number']);

        } catch (Exception $e) {
            logMessage('Checkout error: ' . $e->getMessage(), 'error');
            $this->redirect('checkout', 'An error occurred. Please try again.', 'error');
        }
    }

    /**
     * Order success page
     */
    public function success(string $orderNumber): void
    {
        $order = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order) {
            $this->redirect('', 'Order not found', 'error');
            return;
        }

        // Verify user owns this order (if logged in)
        if (Session::isLoggedIn() && $order['user_id'] && $order['user_id'] !== Session::getUserId()) {
            $this->redirect('', 'Order not found', 'error');
            return;
        }

        $data = [
            'pageTitle' => 'Order Confirmed - ' . SITE_NAME,
            'order' => $order
        ];

        $this->view('checkout/success', $data);
    }

    /**
     * Track order
     */
    public function track(string $orderNumber): void
    {
        $order = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order) {
            $this->redirect('', 'Order not found', 'error');
            return;
        }

        $data = [
            'pageTitle' => 'Track Order - ' . SITE_NAME,
            'order' => $order
        ];

        $this->view('checkout/track', $data);
    }

    /**
     * Apply coupon (AJAX)
     */
    public function applyCoupon(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['valid' => false, 'message' => 'Invalid request']);
            return;
        }

        $code = trim($_POST['code'] ?? '');
        $subtotal = (float) ($_POST['subtotal'] ?? 0);
        $storeId = Session::get('current_store_id', 1);

        if (empty($code)) {
            echo json_encode(['valid' => false, 'message' => 'Please enter a coupon code']);
            return;
        }

        $result = $this->couponModel->validate($code, $storeId, $subtotal);

        if (!$result['valid']) {
            echo json_encode($result);
            return;
        }

        $coupon = $result['coupon'];
        $discount = $this->couponModel->calculateDiscount($coupon, $subtotal);

        // Build response based on coupon type
        $response = [
            'valid' => true,
            'message' => $result['message'],
            'coupon_id' => $coupon['id'],
            'discount' => $discount,
            'discount_type' => $coupon['type'],
            'discount_value' => $coupon['value']
        ];

        // Add type-specific data
        switch ($coupon['type']) {
            case 'free_shipping':
                $response['message'] = 'Free shipping applied!';
                $response['is_free_shipping'] = true;
                break;

            case 'gift_item':
                $giftProduct = $this->couponModel->getGiftProduct($coupon['id']);
                $response['message'] = 'Gift item will be added to your order!';
                $response['is_gift_item'] = true;
                if ($giftProduct) {
                    $response['gift_product'] = [
                        'name' => $giftProduct['name'],
                        'image' => $giftProduct['image'] ? upload($giftProduct['image']) : null,
                        'price' => $giftProduct['price']
                    ];
                }
                break;

            case 'buy_x_get_y':
                $response['message'] = "Buy {$coupon['buy_quantity']} Get {$coupon['get_quantity']} Free!";
                $response['buy_quantity'] = $coupon['buy_quantity'];
                $response['get_quantity'] = $coupon['get_quantity'];
                break;
        }

        echo json_encode($response);
    }

    /**
     * Get cart
     */
    private function getCart(): array
    {
        $userId = Session::getUserId();
        $sessionId = session_id();

        return $this->cartModel->getOrCreate($userId, $sessionId);
    }
}
