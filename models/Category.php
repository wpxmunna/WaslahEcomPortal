<?php
/**
 * Category Model
 */

class Category extends Model
{
    protected string $table = 'categories';
    protected array $fillable = [
        'store_id', 'parent_id', 'name', 'slug', 'description',
        'image', 'icon', 'sort_order', 'status'
    ];

    /**
     * Get category by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT c.*, p.name as parent_name
             FROM categories c
             LEFT JOIN categories p ON c.parent_id = p.id
             WHERE c.slug = ? AND c.status = 1",
            [$slug]
        );
    }

    /**
     * Get main categories with children
     */
    public function getWithChildren(int $storeId = 1): array
    {
        $parents = $this->db->fetchAll(
            "SELECT * FROM categories
             WHERE store_id = ? AND parent_id IS NULL AND status = 1
             ORDER BY sort_order",
            [$storeId]
        );

        foreach ($parents as &$parent) {
            $parent['children'] = $this->db->fetchAll(
                "SELECT * FROM categories
                 WHERE parent_id = ? AND status = 1
                 ORDER BY sort_order",
                [$parent['id']]
            );
        }

        return $parents;
    }

    /**
     * Get parent categories
     */
    public function getParents(int $storeId = 1): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM categories
             WHERE store_id = ? AND parent_id IS NULL AND status = 1
             ORDER BY sort_order",
            [$storeId]
        );
    }

    /**
     * Get children of a category
     */
    public function getChildren(int $parentId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM categories WHERE parent_id = ? AND status = 1 ORDER BY sort_order",
            [$parentId]
        );
    }

    /**
     * Get category breadcrumb
     */
    public function getBreadcrumb(int $categoryId): array
    {
        $breadcrumb = [];
        $category = $this->find($categoryId);

        while ($category) {
            array_unshift($breadcrumb, $category);
            $category = $category['parent_id'] ? $this->find($category['parent_id']) : null;
        }

        return $breadcrumb;
    }

    /**
     * Get product count for category
     */
    public function getProductCount(int $categoryId): int
    {
        // Include subcategory products
        $ids = [$categoryId];
        $children = $this->getChildren($categoryId);
        foreach ($children as $child) {
            $ids[] = $child['id'];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE category_id IN ({$placeholders}) AND status = 'active'",
            $ids
        );

        return $result['count'] ?? 0;
    }

    /**
     * Get all categories for admin
     */
    public function getAllAdmin(int $storeId = 1): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, p.name as parent_name,
                    (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
             FROM categories c
             LEFT JOIN categories p ON c.parent_id = p.id
             WHERE c.store_id = ?
             ORDER BY c.parent_id IS NULL DESC, c.sort_order",
            [$storeId]
        );
    }

    /**
     * Get all categories (alias for getAllAdmin)
     */
    public function getAll(int $storeId = 1, bool $includeInactive = false): array
    {
        $statusCondition = $includeInactive ? '' : 'AND c.status = 1';
        return $this->db->fetchAll(
            "SELECT c.*, p.name as parent_name,
                    (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
             FROM categories c
             LEFT JOIN categories p ON c.parent_id = p.id
             WHERE c.store_id = ? {$statusCondition}
             ORDER BY c.parent_id IS NULL DESC, c.sort_order",
            [$storeId]
        );
    }

    /**
     * Get parent categories for dropdown
     */
    public function getParentCategories(int $storeId = 1): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM categories
             WHERE store_id = ? AND parent_id IS NULL
             ORDER BY sort_order",
            [$storeId]
        );
    }
}
