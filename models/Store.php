<?php
/**
 * Store Model (Multi-store support)
 */

class Store extends Model
{
    protected string $table = 'stores';
    protected array $fillable = [
        'name', 'slug', 'description', 'logo', 'email', 'phone',
        'address', 'currency_code', 'currency_symbol', 'tax_rate',
        'status', 'is_default', 'settings'
    ];

    /**
     * Get default store
     */
    public function getDefault(): ?array
    {
        return $this->db->fetch("SELECT * FROM stores WHERE is_default = 1 LIMIT 1");
    }

    /**
     * Get store by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * Get all active stores
     */
    public function getActive(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM stores WHERE status = 1 ORDER BY is_default DESC, name"
        );
    }

    /**
     * Set store as default
     */
    public function setDefault(int $storeId): bool
    {
        $this->db->query("UPDATE stores SET is_default = 0");
        return $this->update($storeId, ['is_default' => 1]);
    }

    /**
     * Get store stats
     */
    public function getStats(int $storeId): array
    {
        return [
            'products' => $this->db->count('products', 'store_id = ?', [$storeId]),
            'orders' => $this->db->count('orders', 'store_id = ?', [$storeId]),
            'customers' => $this->db->count('users', "store_id = ? AND role = 'customer'", [$storeId]),
            'revenue' => $this->db->fetch(
                "SELECT SUM(total_amount) as total FROM orders WHERE store_id = ? AND payment_status = 'paid'",
                [$storeId]
            )['total'] ?? 0
        ];
    }

    /**
     * Get store settings
     */
    public function getSettings(int $storeId): array
    {
        $settings = $this->db->fetchAll(
            "SELECT setting_key, setting_value FROM settings WHERE store_id = ?",
            [$storeId]
        );

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }

        return $result;
    }

    /**
     * Update store setting
     */
    public function setSetting(int $storeId, string $key, string $value): bool
    {
        $existing = $this->db->fetch(
            "SELECT id FROM settings WHERE store_id = ? AND setting_key = ?",
            [$storeId, $key]
        );

        if ($existing) {
            return $this->db->update(
                'settings',
                ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$existing['id']]
            ) > 0;
        }

        $this->db->insert('settings', [
            'store_id' => $storeId,
            'setting_key' => $key,
            'setting_value' => $value,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    /**
     * Clone store
     */
    public function cloneStore(int $sourceStoreId, string $newName, string $newSlug): int
    {
        $source = $this->find($sourceStoreId);

        if (!$source) {
            throw new Exception("Source store not found");
        }

        $newStore = $source;
        unset($newStore['id']);
        $newStore['name'] = $newName;
        $newStore['slug'] = $newSlug;
        $newStore['is_default'] = 0;
        $newStore['created_at'] = date('Y-m-d H:i:s');
        $newStore['updated_at'] = date('Y-m-d H:i:s');

        $newStoreId = $this->db->insert('stores', $newStore);

        // Clone categories
        $categories = $this->db->fetchAll(
            "SELECT * FROM categories WHERE store_id = ?",
            [$sourceStoreId]
        );

        $categoryMap = [];
        foreach ($categories as $cat) {
            $oldId = $cat['id'];
            unset($cat['id']);
            $cat['store_id'] = $newStoreId;
            $cat['created_at'] = date('Y-m-d H:i:s');
            $cat['updated_at'] = date('Y-m-d H:i:s');

            $newId = $this->db->insert('categories', $cat);
            $categoryMap[$oldId] = $newId;
        }

        // Update parent_ids
        foreach ($categoryMap as $oldId => $newId) {
            $oldCat = $this->db->fetch("SELECT parent_id FROM categories WHERE id = ?", [$oldId]);
            if ($oldCat['parent_id'] && isset($categoryMap[$oldCat['parent_id']])) {
                $this->db->update('categories', ['parent_id' => $categoryMap[$oldCat['parent_id']]], 'id = ?', [$newId]);
            }
        }

        // Clone settings
        $settings = $this->db->fetchAll(
            "SELECT * FROM settings WHERE store_id = ?",
            [$sourceStoreId]
        );

        foreach ($settings as $setting) {
            unset($setting['id']);
            $setting['store_id'] = $newStoreId;
            $setting['created_at'] = date('Y-m-d H:i:s');
            $setting['updated_at'] = date('Y-m-d H:i:s');
            $this->db->insert('settings', $setting);
        }

        return $newStoreId;
    }
}
