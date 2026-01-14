<?php
/**
 * Business Settings Model
 * Manage business information, links, and credentials
 */

class BusinessSetting extends Model
{
    protected string $table = 'business_settings';

    /**
     * Get all settings for a store
     */
    public function getByStore(int $storeId): array
    {
        $settings = $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE store_id = ? ORDER BY setting_group, setting_key",
            [$storeId]
        );

        // Convert to key-value array
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }

        return $result;
    }

    /**
     * Get settings grouped by category
     */
    public function getGrouped(int $storeId): array
    {
        $settings = $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE store_id = ? ORDER BY setting_group, setting_key",
            [$storeId]
        );

        $grouped = [];
        foreach ($settings as $setting) {
            $group = $setting['setting_group'];
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $setting;
        }

        return $grouped;
    }

    /**
     * Get a single setting value
     */
    public function getSetting(int $storeId, string $key, $default = null)
    {
        $setting = $this->db->fetchOne(
            "SELECT setting_value FROM {$this->table} WHERE store_id = ? AND setting_key = ?",
            [$storeId, $key]
        );

        return $setting ? $setting['setting_value'] : $default;
    }

    /**
     * Update or create a setting
     */
    public function setSetting(int $storeId, string $key, $value, string $group = 'general', string $description = ''): bool
    {
        $existing = $this->db->fetchOne(
            "SELECT id FROM {$this->table} WHERE store_id = ? AND setting_key = ?",
            [$storeId, $key]
        );

        if ($existing) {
            // Update existing
            $this->db->query(
                "UPDATE {$this->table} SET setting_value = ?, setting_group = ?, description = ? WHERE id = ?",
                [$value, $group, $description, $existing['id']]
            );
        } else {
            // Insert new
            $this->db->insert($this->table, [
                'store_id' => $storeId,
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => $group,
                'description' => $description
            ]);
        }

        return true;
    }

    /**
     * Update multiple settings at once
     */
    public function updateBulk(int $storeId, array $settings): bool
    {
        foreach ($settings as $key => $value) {
            // Get existing setting to preserve group and description
            $existing = $this->db->fetchOne(
                "SELECT setting_group, description FROM {$this->table} WHERE store_id = ? AND setting_key = ?",
                [$storeId, $key]
            );

            if ($existing) {
                $this->setSetting(
                    $storeId,
                    $key,
                    $value,
                    $existing['setting_group'],
                    $existing['description']
                );
            } else {
                $this->setSetting($storeId, $key, $value);
            }
        }

        return true;
    }

    /**
     * Delete a setting
     */
    public function deleteSetting(int $storeId, string $key): bool
    {
        $this->db->delete($this->table, "store_id = ? AND setting_key = ?", [$storeId, $key]);
        return true;
    }

    /**
     * Get settings by group
     */
    public function getByGroup(int $storeId, string $group): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE store_id = ? AND setting_group = ? ORDER BY setting_key",
            [$storeId, $group]
        );
    }

    /**
     * Get all available groups
     */
    public function getGroups(int $storeId): array
    {
        $groups = $this->db->fetchAll(
            "SELECT DISTINCT setting_group FROM {$this->table} WHERE store_id = ? ORDER BY setting_group",
            [$storeId]
        );

        return array_column($groups, 'setting_group');
    }
}
