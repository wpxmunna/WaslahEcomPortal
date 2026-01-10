<?php
/**
 * Expense Category Model
 */

class ExpenseCategory extends Model
{
    protected string $table = 'expense_categories';
    protected array $fillable = [
        'store_id', 'name', 'slug', 'description', 'color', 'icon', 'is_active'
    ];

    /**
     * Get all categories for a store
     */
    public function getByStore(int $storeId, bool $activeOnly = true): array
    {
        $sql = "SELECT * FROM expense_categories WHERE store_id = ?";
        $params = [$storeId];

        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }

        $sql .= " ORDER BY name ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get categories with expense counts
     */
    public function getWithExpenseCount(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT c.*,
                    COUNT(e.id) as expense_count,
                    COALESCE(SUM(e.total_amount), 0) as total_amount
             FROM expense_categories c
             LEFT JOIN expenses e ON c.id = e.category_id
             WHERE c.store_id = ?
             GROUP BY c.id
             ORDER BY c.name ASC",
            [$storeId]
        );
    }

    /**
     * Create category
     */
    public function createCategory(array $data): int
    {
        $data['slug'] = $this->generateSlug($data['name']);
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update category
     */
    public function updateCategory(int $id, array $data): bool
    {
        if (isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->update($this->table, $data, 'id = ?', [$id]);
        return true;
    }

    /**
     * Delete category (only if no expenses)
     */
    public function deleteCategory(int $id): bool
    {
        // Check if category has expenses
        $count = $this->db->fetch(
            "SELECT COUNT(*) as count FROM expenses WHERE category_id = ?",
            [$id]
        );

        if ($count['count'] > 0) {
            return false;
        }

        $this->db->delete($this->table, 'id = ?', [$id]);
        return true;
    }

    /**
     * Generate slug from name
     */
    private function generateSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug, int $storeId): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM expense_categories WHERE slug = ? AND store_id = ?",
            [$slug, $storeId]
        );
    }
}
