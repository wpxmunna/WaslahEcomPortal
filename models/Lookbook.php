<?php
/**
 * Lookbook Model
 */

class Lookbook extends Model
{
    protected string $table = 'lookbook';

    /**
     * Get all lookbook items for a store
     */
    public function getByStore(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE store_id = ?
             ORDER BY sort_order ASC, id ASC",
            [$storeId]
        );
    }

    /**
     * Get active lookbook items for a store
     */
    public function getActive(int $storeId, int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE store_id = ? AND status = 'active'
             ORDER BY sort_order ASC, id ASC
             LIMIT ?",
            [$storeId, $limit]
        );
    }

    /**
     * Get featured item (large image)
     */
    public function getFeatured(int $storeId): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table}
             WHERE store_id = ? AND status = 'active' AND is_featured = 1
             ORDER BY sort_order ASC
             LIMIT 1",
            [$storeId]
        );
    }

    /**
     * Create a new lookbook item
     */
    public function create(array $data): int
    {
        return $this->db->insert($this->table, [
            'store_id' => $data['store_id'] ?? 1,
            'image' => $data['image'],
            'link' => $data['link'] ?? null,
            'caption' => $data['caption'] ?? null,
            'is_featured' => $data['is_featured'] ?? 0,
            'sort_order' => $data['sort_order'] ?? 0,
            'status' => $data['status'] ?? 'active'
        ]);
    }

    /**
     * Update a lookbook item
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        $allowedFields = ['image', 'link', 'caption', 'is_featured', 'sort_order', 'status'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $id;

        $this->db->query(
            "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?",
            $values
        );
        return true;
    }

    /**
     * Delete a lookbook item
     */
    public function delete(int $id): bool
    {
        // Get item to delete image if it's a local file
        $item = $this->find($id);
        if ($item && $item['image'] && !filter_var($item['image'], FILTER_VALIDATE_URL)) {
            $imagePath = ROOT_PATH . '/public/uploads/lookbook/' . $item['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->db->delete($this->table, "id = ?", [$id]);
        return true;
    }

    /**
     * Toggle status
     */
    public function toggleStatus(int $id): bool
    {
        $this->db->query(
            "UPDATE {$this->table} SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?",
            [$id]
        );
        return true;
    }

    /**
     * Set featured item (unset others)
     */
    public function setFeatured(int $id, int $storeId): bool
    {
        // Unset all featured items for this store
        $this->db->query(
            "UPDATE {$this->table} SET is_featured = 0 WHERE store_id = ?",
            [$storeId]
        );

        // Set this one as featured
        $this->db->query(
            "UPDATE {$this->table} SET is_featured = 1 WHERE id = ?",
            [$id]
        );
        return true;
    }
}
