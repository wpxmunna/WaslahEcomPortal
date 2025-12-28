<?php
/**
 * Cart Controller
 */

class CartController extends Controller
{
    private Cart $cartModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
    }

    /**
     * View cart
     */
    public function index(): void
    {
        $cart = $this->getCart();
        $cartData = $this->cartModel->getTotals($cart['id']);

        $data = [
            'pageTitle' => 'Shopping Cart - ' . SITE_NAME,
            'cart' => $cartData
        ];

        $this->view('cart/index', $data);
    }

    /**
     * Add to cart
     */
    public function add(): void
    {
        $productId = (int) $this->post('product_id');
        $quantity = (int) $this->post('quantity', 1);
        $variantId = $this->post('variant_id') ? (int) $this->post('variant_id') : null;

        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Invalid product']);
            return;
        }

        $productModel = new Product();
        $product = $productModel->find($productId);

        if (!$product || $product['status'] !== 'active') {
            $this->json(['success' => false, 'message' => 'Product not available']);
            return;
        }

        // Get price
        $price = $product['sale_price'] ?? $product['price'];

        // Check variant if provided
        if ($variantId) {
            $variant = $this->db->fetch(
                "SELECT * FROM product_variants WHERE id = ? AND product_id = ?",
                [$variantId, $productId]
            );

            if ($variant) {
                $price += $variant['price_modifier'];
            }
        }

        // Check stock
        $availableStock = $variantId
            ? ($variant['stock_quantity'] ?? 0)
            : $product['stock_quantity'];

        if ($quantity > $availableStock) {
            $this->json(['success' => false, 'message' => 'Not enough stock available']);
            return;
        }

        $cart = $this->getCart();
        $this->cartModel->addItem($cart['id'], $productId, $quantity, $price, $variantId);

        $cartCount = $this->cartModel->getCount($cart['id']);

        $this->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cartCount' => $cartCount
        ]);
    }

    /**
     * Update cart item
     */
    public function update(): void
    {
        $itemId = (int) $this->post('item_id');
        $quantity = (int) $this->post('quantity');

        if (!$itemId) {
            $this->json(['success' => false, 'message' => 'Invalid item']);
            return;
        }

        $this->cartModel->updateItemQuantity($itemId, $quantity);

        $this->json(['success' => true, 'message' => 'Cart updated']);
    }

    /**
     * Remove item from cart
     */
    public function remove(): void
    {
        $itemId = (int) $this->post('item_id');

        if (!$itemId) {
            $this->json(['success' => false, 'message' => 'Invalid item']);
            return;
        }

        $this->cartModel->removeItem($itemId);

        $this->json(['success' => true, 'message' => 'Item removed']);
    }

    /**
     * Clear cart
     */
    public function clear(): void
    {
        $cart = $this->getCart();
        $this->cartModel->clearCart($cart['id']);

        $this->redirect('cart', 'Cart cleared');
    }

    /**
     * Get cart count (AJAX)
     */
    public function count(): void
    {
        $cart = $this->getCart();
        $count = $this->cartModel->getCount($cart['id']);

        $this->json(['count' => $count]);
    }

    /**
     * Get or create cart
     */
    private function getCart(): array
    {
        $userId = Session::getUserId();
        $sessionId = session_id();

        return $this->cartModel->getOrCreate($userId, $sessionId);
    }
}
