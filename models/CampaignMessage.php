<?php
/**
 * Campaign Message Model
 * Manage social media campaign/promotional messages
 */

class CampaignMessage
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all campaign messages for a store
     */
    public function getAllForStore(int $storeId, ?string $platform = null, ?string $type = null): array
    {
        $sql = "SELECT cm.*, u.name as created_by_name
                FROM campaign_messages cm
                LEFT JOIN users u ON cm.created_by = u.id
                WHERE cm.store_id = ?";
        $params = [$storeId];

        if ($platform && $platform !== 'all') {
            $sql .= " AND (cm.platform = ? OR cm.platform = 'all')";
            $params[] = $platform;
        }

        if ($type) {
            $sql .= " AND cm.message_type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY cm.is_pinned DESC, cm.created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get active campaign messages for a platform
     */
    public function getActiveForPlatform(int $storeId, string $platform): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM campaign_messages
             WHERE store_id = ? AND is_active = 1
             AND (platform = ? OR platform = 'all')
             AND (expires_at IS NULL OR expires_at > NOW())
             ORDER BY is_pinned DESC, created_at DESC",
            [$storeId, $platform]
        );
    }

    /**
     * Get single campaign message by ID
     */
    public function getById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT cm.*, u.name as created_by_name
             FROM campaign_messages cm
             LEFT JOIN users u ON cm.created_by = u.id
             WHERE cm.id = ?",
            [$id]
        );
    }

    /**
     * Create new campaign message
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO campaign_messages (
                    store_id, title, platform, message_type, content,
                    short_content, hashtags, call_to_action, cta_url,
                    image_path, scheduled_at, expires_at, is_active,
                    is_pinned, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['store_id'],
            $data['title'],
            $data['platform'] ?? 'all',
            $data['message_type'] ?? 'promotion',
            $data['content'],
            $data['short_content'] ?? null,
            $data['hashtags'] ?? null,
            $data['call_to_action'] ?? null,
            $data['cta_url'] ?? null,
            $data['image_path'] ?? null,
            $data['scheduled_at'] ?? null,
            $data['expires_at'] ?? null,
            $data['is_active'] ?? 1,
            $data['is_pinned'] ?? 0,
            $data['created_by'] ?? null
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Update campaign message
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE campaign_messages SET
                    title = ?, platform = ?, message_type = ?, content = ?,
                    short_content = ?, hashtags = ?, call_to_action = ?,
                    cta_url = ?, image_path = ?, scheduled_at = ?,
                    expires_at = ?, is_active = ?, is_pinned = ?,
                    updated_at = NOW()
                WHERE id = ?";

        $params = [
            $data['title'],
            $data['platform'] ?? 'all',
            $data['message_type'] ?? 'promotion',
            $data['content'],
            $data['short_content'] ?? null,
            $data['hashtags'] ?? null,
            $data['call_to_action'] ?? null,
            $data['cta_url'] ?? null,
            $data['image_path'] ?? null,
            $data['scheduled_at'] ?? null,
            $data['expires_at'] ?? null,
            $data['is_active'] ?? 1,
            $data['is_pinned'] ?? 0,
            $id
        ];

        return $this->db->execute($sql, $params);
    }

    /**
     * Delete campaign message
     */
    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM campaign_messages WHERE id = ?", [$id]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(int $id): bool
    {
        return $this->db->execute(
            "UPDATE campaign_messages SET is_active = NOT is_active WHERE id = ?",
            [$id]
        );
    }

    /**
     * Toggle pinned status
     */
    public function togglePinned(int $id): bool
    {
        return $this->db->execute(
            "UPDATE campaign_messages SET is_pinned = NOT is_pinned WHERE id = ?",
            [$id]
        );
    }

    /**
     * Increment copy count
     */
    public function incrementCopyCount(int $id): bool
    {
        return $this->db->execute(
            "UPDATE campaign_messages SET copy_count = copy_count + 1 WHERE id = ?",
            [$id]
        );
    }

    /**
     * Duplicate a campaign message
     */
    public function duplicate(int $id): int|false
    {
        $original = $this->getById($id);
        if (!$original) {
            return false;
        }

        $data = $original;
        $data['title'] = $original['title'] . ' (Copy)';
        $data['is_pinned'] = 0;
        $data['copy_count'] = 0;
        unset($data['id'], $data['created_at'], $data['updated_at'], $data['created_by_name']);

        return $this->create($data);
    }

    /**
     * Get message types
     */
    public static function getMessageTypes(): array
    {
        return [
            'promotion' => ['name' => 'Promotion', 'icon' => 'fa-bullhorn', 'color' => '#e74c3c'],
            'announcement' => ['name' => 'Announcement', 'icon' => 'fa-megaphone', 'color' => '#3498db'],
            'greeting' => ['name' => 'Greeting', 'icon' => 'fa-heart', 'color' => '#e91e63'],
            'offer' => ['name' => 'Special Offer', 'icon' => 'fa-tag', 'color' => '#27ae60'],
            'event' => ['name' => 'Event', 'icon' => 'fa-calendar-star', 'color' => '#9b59b6'],
            'custom' => ['name' => 'Custom', 'icon' => 'fa-pen', 'color' => '#95a5a6']
        ];
    }

    /**
     * Get platforms
     */
    public static function getPlatforms(): array
    {
        return [
            'all' => ['name' => 'All Platforms', 'icon' => 'fa-globe', 'color' => '#34495e'],
            'facebook' => ['name' => 'Facebook', 'icon' => 'fa-facebook-f', 'color' => '#1877F2'],
            'instagram' => ['name' => 'Instagram', 'icon' => 'fa-instagram', 'color' => '#E4405F'],
            'whatsapp' => ['name' => 'WhatsApp', 'icon' => 'fa-whatsapp', 'color' => '#25D366'],
            'telegram' => ['name' => 'Telegram', 'icon' => 'fa-telegram', 'color' => '#26A5E4'],
            'twitter' => ['name' => 'Twitter/X', 'icon' => 'fa-x-twitter', 'color' => '#000000']
        ];
    }

    /**
     * Get campaign statistics
     */
    public function getStats(int $storeId): array
    {
        $stats = $this->db->fetch(
            "SELECT
                COUNT(*) as total,
                SUM(is_active) as active,
                SUM(is_pinned) as pinned,
                SUM(copy_count) as total_copies
             FROM campaign_messages
             WHERE store_id = ?",
            [$storeId]
        );

        $byPlatform = $this->db->fetchAll(
            "SELECT platform, COUNT(*) as count
             FROM campaign_messages
             WHERE store_id = ?
             GROUP BY platform",
            [$storeId]
        );

        $byType = $this->db->fetchAll(
            "SELECT message_type, COUNT(*) as count
             FROM campaign_messages
             WHERE store_id = ?
             GROUP BY message_type",
            [$storeId]
        );

        return [
            'total' => (int) ($stats['total'] ?? 0),
            'active' => (int) ($stats['active'] ?? 0),
            'pinned' => (int) ($stats['pinned'] ?? 0),
            'total_copies' => (int) ($stats['total_copies'] ?? 0),
            'by_platform' => array_column($byPlatform, 'count', 'platform'),
            'by_type' => array_column($byType, 'count', 'message_type')
        ];
    }
}
