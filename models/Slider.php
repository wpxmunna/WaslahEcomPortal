<?php
/**
 * Slider Model
 */

class Slider extends Model
{
    protected string $table = 'sliders';

    /**
     * Get all sliders for a store
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
     * Get active sliders for a store
     */
    public function getActive(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE store_id = ? AND status = 'active'
             ORDER BY sort_order ASC, id ASC",
            [$storeId]
        );
    }

    /**
     * Create a new slider
     */
    public function create(array $data): int
    {
        return $this->db->insert($this->table, [
            'store_id' => $data['store_id'] ?? 1,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'button_link' => $data['button_link'] ?? null,
            'button2_text' => $data['button2_text'] ?? null,
            'button2_link' => $data['button2_link'] ?? null,
            'image' => $data['image'] ?? null,
            'text_position' => $data['text_position'] ?? 'left',
            'text_color' => $data['text_color'] ?? '#ffffff',
            'overlay_opacity' => $data['overlay_opacity'] ?? 0.40,
            'sort_order' => $data['sort_order'] ?? 0,
            'status' => $data['status'] ?? 'active'
        ]);
    }

    /**
     * Update a slider
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        $allowedFields = [
            'title', 'subtitle', 'description', 'button_text', 'button_link',
            'button2_text', 'button2_link', 'image', 'text_position',
            'text_color', 'overlay_opacity', 'sort_order', 'status'
        ];

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
     * Delete a slider
     */
    public function delete(int $id): bool
    {
        // Get slider to delete image if exists
        $slider = $this->find($id);
        if ($slider && $slider['image']) {
            $imagePath = ROOT_PATH . '/uploads/sliders/' . $slider['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->db->delete($this->table, "id = ?", [$id]);
        return true;
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(array $order): bool
    {
        foreach ($order as $position => $id) {
            $this->db->query(
                "UPDATE {$this->table} SET sort_order = ? WHERE id = ?",
                [$position, $id]
            );
        }
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
}
