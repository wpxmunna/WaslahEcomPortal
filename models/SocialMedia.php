<?php
/**
 * Social Media Model
 * Manage social media links for the site
 */

class SocialMedia extends Model
{
    protected string $table = 'social_media';

    /**
     * Get all social media links for a store
     */
    public function getAllForStore(int $storeId, bool $activeOnly = false): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE store_id = ?";
        $params = [$storeId];

        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }

        $sql .= " ORDER BY sort_order ASC, name ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get social media links for header
     */
    public function getHeaderLinks(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE store_id = ? AND is_active = 1 AND show_in_header = 1
             ORDER BY sort_order ASC",
            [$storeId]
        );
    }

    /**
     * Get social media links for footer
     */
    public function getFooterLinks(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE store_id = ? AND is_active = 1 AND show_in_footer = 1
             ORDER BY sort_order ASC",
            [$storeId]
        );
    }

    /**
     * Get a single social media link
     */
    public function getById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }

    /**
     * Create a new social media link
     */
    public function create(array $data): int
    {
        return $this->db->insert($this->table, [
            'store_id' => $data['store_id'],
            'platform' => $data['platform'],
            'name' => $data['name'],
            'url' => $data['url'],
            'icon' => $data['icon'],
            'icon_style' => $data['icon_style'] ?? 'brands',
            'color' => $data['color'] ?? '#000000',
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
            'show_in_header' => $data['show_in_header'] ?? 0,
            'show_in_footer' => $data['show_in_footer'] ?? 1,
            'open_new_tab' => $data['open_new_tab'] ?? 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Update a social media link
     */
    public function update(int $id, array $data): bool
    {
        $updateData = [
            'platform' => $data['platform'],
            'name' => $data['name'],
            'url' => $data['url'],
            'icon' => $data['icon'],
            'icon_style' => $data['icon_style'] ?? 'brands',
            'color' => $data['color'] ?? '#000000',
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
            'show_in_header' => $data['show_in_header'] ?? 0,
            'show_in_footer' => $data['show_in_footer'] ?? 1,
            'open_new_tab' => $data['open_new_tab'] ?? 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->update($this->table, $updateData, 'id = ?', [$id]);
    }

    /**
     * Delete a social media link
     */
    public function delete(int $id): bool
    {
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(int $id): bool
    {
        return $this->db->query(
            "UPDATE {$this->table} SET is_active = NOT is_active, updated_at = NOW() WHERE id = ?",
            [$id]
        );
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(array $order): bool
    {
        foreach ($order as $position => $id) {
            $this->db->update(
                $this->table,
                ['sort_order' => $position],
                'id = ?',
                [$id]
            );
        }
        return true;
    }

    /**
     * Get available platform presets
     */
    public static function getPlatformPresets(): array
    {
        return [
            'facebook' => ['name' => 'Facebook', 'icon' => 'fa-facebook-f', 'color' => '#1877F2'],
            'instagram' => ['name' => 'Instagram', 'icon' => 'fa-instagram', 'color' => '#E4405F'],
            'twitter' => ['name' => 'Twitter/X', 'icon' => 'fa-x-twitter', 'color' => '#000000'],
            'youtube' => ['name' => 'YouTube', 'icon' => 'fa-youtube', 'color' => '#FF0000'],
            'tiktok' => ['name' => 'TikTok', 'icon' => 'fa-tiktok', 'color' => '#000000'],
            'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fa-linkedin-in', 'color' => '#0A66C2'],
            'pinterest' => ['name' => 'Pinterest', 'icon' => 'fa-pinterest-p', 'color' => '#E60023'],
            'whatsapp' => ['name' => 'WhatsApp', 'icon' => 'fa-whatsapp', 'color' => '#25D366'],
            'telegram' => ['name' => 'Telegram', 'icon' => 'fa-telegram', 'color' => '#26A5E4'],
            'snapchat' => ['name' => 'Snapchat', 'icon' => 'fa-snapchat', 'color' => '#FFFC00'],
            'discord' => ['name' => 'Discord', 'icon' => 'fa-discord', 'color' => '#5865F2'],
            'reddit' => ['name' => 'Reddit', 'icon' => 'fa-reddit-alien', 'color' => '#FF4500'],
            'tumblr' => ['name' => 'Tumblr', 'icon' => 'fa-tumblr', 'color' => '#36465D'],
            'twitch' => ['name' => 'Twitch', 'icon' => 'fa-twitch', 'color' => '#9146FF'],
            'spotify' => ['name' => 'Spotify', 'icon' => 'fa-spotify', 'color' => '#1DB954'],
            'github' => ['name' => 'GitHub', 'icon' => 'fa-github', 'color' => '#181717'],
            'dribbble' => ['name' => 'Dribbble', 'icon' => 'fa-dribbble', 'color' => '#EA4C89'],
            'behance' => ['name' => 'Behance', 'icon' => 'fa-behance', 'color' => '#1769FF'],
            'medium' => ['name' => 'Medium', 'icon' => 'fa-medium', 'color' => '#000000'],
            'email' => ['name' => 'Email', 'icon' => 'fa-envelope', 'color' => '#EA4335', 'style' => 'solid'],
            'phone' => ['name' => 'Phone', 'icon' => 'fa-phone', 'color' => '#25D366', 'style' => 'solid'],
            'website' => ['name' => 'Website', 'icon' => 'fa-globe', 'color' => '#4285F4', 'style' => 'solid'],
            'custom' => ['name' => 'Custom', 'icon' => 'fa-link', 'color' => '#6B7280', 'style' => 'solid'],
        ];
    }
}
