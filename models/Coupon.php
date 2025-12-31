<?php
/**
 * Coupon Model
 */

class Coupon extends Model
{
    protected string $table = 'coupons';

    // Discount types
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FREE_SHIPPING = 'free_shipping';
    const TYPE_GIFT_ITEM = 'gift_item';
    const TYPE_BUY_X_GET_Y = 'buy_x_get_y';

    /**
     * Get discount type labels
     */
    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_FIXED => 'Fixed Amount',
            self::TYPE_PERCENTAGE => 'Percentage',
            self::TYPE_FREE_SHIPPING => 'Free Shipping',
            self::TYPE_GIFT_ITEM => 'Gift Item',
            self::TYPE_BUY_X_GET_Y => 'Buy X Get Y Free',
        ];
    }

    /**
     * Get all coupons for a store
     */
    public function getByStore(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, p.name as gift_product_name
             FROM {$this->table} c
             LEFT JOIN products p ON c.gift_product_id = p.id
             WHERE c.store_id = ? ORDER BY c.created_at DESC",
            [$storeId]
        );
    }

    /**
     * Find coupon by code
     */
    public function findByCode(string $code, int $storeId): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE code = ? AND store_id = ? AND status = 1",
            [strtoupper($code), $storeId]
        );
    }

    /**
     * Validate coupon for use
     */
    public function validate(string $code, int $storeId, float $cartTotal): array
    {
        $coupon = $this->findByCode($code, $storeId);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code'];
        }

        // Check if expired
        if ($coupon['expires_at'] && strtotime($coupon['expires_at']) < time()) {
            return ['valid' => false, 'message' => 'This coupon has expired'];
        }

        // Check if not yet started
        if ($coupon['starts_at'] && strtotime($coupon['starts_at']) > time()) {
            return ['valid' => false, 'message' => 'This coupon is not yet active'];
        }

        // Check usage limit
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit'];
        }

        // Check minimum amount
        if ($coupon['minimum_amount'] > 0 && $cartTotal < $coupon['minimum_amount']) {
            return [
                'valid' => false,
                'message' => 'Minimum order amount of ' . formatPrice($coupon['minimum_amount']) . ' required'
            ];
        }

        // Calculate discount
        $discount = $this->calculateDiscount($coupon, $cartTotal);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Coupon applied successfully!'
        ];
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(array $coupon, float $cartTotal, float $shippingAmount = 0): float
    {
        $discount = 0;

        switch ($coupon['type']) {
            case self::TYPE_PERCENTAGE:
                $discount = ($cartTotal * $coupon['value']) / 100;
                // Apply maximum discount cap if set
                if ($coupon['maximum_discount'] && $discount > $coupon['maximum_discount']) {
                    $discount = (float) $coupon['maximum_discount'];
                }
                break;

            case self::TYPE_FIXED:
                $discount = (float) $coupon['value'];
                break;

            case self::TYPE_FREE_SHIPPING:
                // Return the shipping amount as discount
                $discount = $shippingAmount;
                break;

            case self::TYPE_GIFT_ITEM:
                // Gift item doesn't reduce the cart total, it adds a free item
                // The value here represents the value of the gift (for display)
                $discount = 0;
                break;

            case self::TYPE_BUY_X_GET_Y:
                // This is handled separately in the cart/checkout
                // The discount value would be calculated based on qualifying items
                $discount = (float) $coupon['value'];
                break;

            default:
                $discount = (float) $coupon['value'];
        }

        // Discount cannot exceed cart total (except for free shipping which comes from shipping amount)
        if ($coupon['type'] !== self::TYPE_FREE_SHIPPING && $discount > $cartTotal) {
            $discount = $cartTotal;
        }

        return round($discount, 2);
    }

    /**
     * Get gift product details
     */
    public function getGiftProduct(int $couponId): ?array
    {
        $coupon = $this->find($couponId);
        if (!$coupon || !$coupon['gift_product_id']) {
            return null;
        }

        return $this->db->fetch(
            "SELECT p.*,
                    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
             FROM products p WHERE p.id = ?",
            [$coupon['gift_product_id']]
        );
    }

    /**
     * Check if coupon is free shipping type
     */
    public function isFreeShipping(array $coupon): bool
    {
        return $coupon['type'] === self::TYPE_FREE_SHIPPING;
    }

    /**
     * Check if coupon has gift item
     */
    public function hasGiftItem(array $coupon): bool
    {
        return $coupon['type'] === self::TYPE_GIFT_ITEM && !empty($coupon['gift_product_id']);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(int $couponId): bool
    {
        return $this->db->query(
            "UPDATE {$this->table} SET used_count = used_count + 1 WHERE id = ?",
            [$couponId]
        );
    }

    /**
     * Create coupon
     */
    public function create(array $data): int
    {
        $data['code'] = strtoupper($data['code']);
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update coupon
     */
    public function update(int $id, array $data): bool
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }
        return $this->db->update($this->table, $data, 'id = ?', [$id]);
    }

    /**
     * Delete coupon
     */
    public function delete(int $id): bool
    {
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }

    /**
     * Check if code exists
     */
    public function codeExists(string $code, int $storeId, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE code = ? AND store_id = ?";
        $params = [strtoupper($code), $storeId];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        return (bool) $this->db->fetch($sql, $params);
    }
}
