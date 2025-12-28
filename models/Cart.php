<?php
/**
 * Cart Model
 */

class Cart extends Model
{
    protected string $table = 'cart';

    /**
     * Get or create cart for user
     */
    public function getOrCreate(?int $userId, string $sessionId, int $storeId = 1): array
    {
        if ($userId) {
            $cart = $this->db->fetch(
                "SELECT * FROM cart WHERE user_id = ? AND store_id = ?",
                [$userId, $storeId]
            );
        } else {
            $cart = $this->db->fetch(
                "SELECT * FROM cart WHERE session_id = ? AND store_id = ?",
                [$sessionId, $storeId]
            );
        }

        if (!$cart) {
            $cartId = $this->db->insert('cart', [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'store_id' => $storeId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $cart = $this->find($cartId);
        }

        return $cart;
    }

    /**
     * Get cart items with product details
     */
    public function getItems(int $cartId): array
    {
        return $this->db->fetchAll(
            "SELECT ci.*, p.name, p.slug, p.price, p.sale_price, p.stock_quantity,
                    pv.size, pv.color, pv.price_modifier,
                    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             LEFT JOIN product_variants pv ON ci.variant_id = pv.id
             WHERE ci.cart_id = ?
             ORDER BY ci.created_at DESC",
            [$cartId]
        );
    }

    /**
     * Add item to cart
     */
    public function addItem(int $cartId, int $productId, int $quantity, float $price, ?int $variantId = null): bool
    {
        // Check if item already exists
        $where = "cart_id = ? AND product_id = ?";
        $params = [$cartId, $productId];

        if ($variantId) {
            $where .= " AND variant_id = ?";
            $params[] = $variantId;
        } else {
            $where .= " AND variant_id IS NULL";
        }

        $existing = $this->db->fetch(
            "SELECT * FROM cart_items WHERE {$where}",
            $params
        );

        if ($existing) {
            // Update quantity
            return $this->db->update(
                'cart_items',
                [
                    'quantity' => $existing['quantity'] + $quantity,
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                'id = ?',
                [$existing['id']]
            ) > 0;
        }

        // Add new item
        $this->db->insert('cart_items', [
            'cart_id' => $cartId,
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'price' => $price,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    /**
     * Update item quantity
     */
    public function updateItemQuantity(int $cartItemId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($cartItemId);
        }

        return $this->db->update(
            'cart_items',
            [
                'quantity' => $quantity,
                'updated_at' => date('Y-m-d H:i:s')
            ],
            'id = ?',
            [$cartItemId]
        ) > 0;
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartItemId): bool
    {
        return $this->db->delete('cart_items', 'id = ?', [$cartItemId]) > 0;
    }

    /**
     * Clear cart
     */
    public function clearCart(int $cartId): bool
    {
        return $this->db->delete('cart_items', 'cart_id = ?', [$cartId]) >= 0;
    }

    /**
     * Get cart totals
     */
    public function getTotals(int $cartId): array
    {
        $items = $this->getItems($cartId);

        $subtotal = 0;
        $itemCount = 0;

        foreach ($items as $item) {
            $price = $item['sale_price'] ?? $item['price'];
            $price += $item['price_modifier'] ?? 0;
            $subtotal += $price * $item['quantity'];
            $itemCount += $item['quantity'];
        }

        $shipping = $subtotal >= FREE_SHIPPING_THRESHOLD ? 0 : DEFAULT_SHIPPING_COST;
        $tax = $subtotal * TAX_RATE;
        $total = $subtotal + $shipping + $tax;

        return [
            'items' => $items,
            'item_count' => $itemCount,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total
        ];
    }

    /**
     * Merge guest cart with user cart
     */
    public function mergeGuestCart(string $sessionId, int $userId, int $storeId = 1): void
    {
        $guestCart = $this->db->fetch(
            "SELECT * FROM cart WHERE session_id = ? AND user_id IS NULL AND store_id = ?",
            [$sessionId, $storeId]
        );

        if (!$guestCart) {
            return;
        }

        $userCart = $this->getOrCreate($userId, $sessionId, $storeId);
        $guestItems = $this->getItems($guestCart['id']);

        foreach ($guestItems as $item) {
            $this->addItem(
                $userCart['id'],
                $item['product_id'],
                $item['quantity'],
                $item['price'],
                $item['variant_id']
            );
        }

        // Delete guest cart
        $this->db->delete('cart_items', 'cart_id = ?', [$guestCart['id']]);
        $this->db->delete('cart', 'id = ?', [$guestCart['id']]);
    }

    /**
     * Get cart count
     */
    public function getCount(int $cartId): int
    {
        $result = $this->db->fetch(
            "SELECT SUM(quantity) as count FROM cart_items WHERE cart_id = ?",
            [$cartId]
        );
        return (int) ($result['count'] ?? 0);
    }
}
