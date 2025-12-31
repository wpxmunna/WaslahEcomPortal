<?php
/**
 * Product Model
 */

class Product extends Model
{
    protected string $table = 'products';
    protected array $fillable = [
        'store_id', 'category_id', 'name', 'slug', 'description',
        'short_description', 'price', 'sale_price', 'cost_price',
        'sku', 'barcode', 'stock_quantity', 'low_stock_threshold',
        'weight', 'is_featured', 'is_new', 'status',
        'meta_title', 'meta_description'
    ];

    /**
     * Get product by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $product = $this->db->fetch(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.slug = ? AND p.status = 'active'",
            [$slug]
        );

        if ($product) {
            $product['images'] = $this->getImages($product['id']);
            $product['variants'] = $this->getVariants($product['id']);

            // Increment views
            $this->db->query("UPDATE products SET views = views + 1 WHERE id = ?", [$product['id']]);
        }

        return $product;
    }

    /**
     * Get product images
     */
    public function getImages(int $productId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order",
            [$productId]
        );
    }

    /**
     * Get product variants
     */
    public function getVariants(int $productId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM product_variants WHERE product_id = ? AND status = 1",
            [$productId]
        );
    }

    /**
     * Get primary image
     */
    public function getPrimaryImage(int $productId): ?string
    {
        $image = $this->db->fetch(
            "SELECT image_path FROM product_images WHERE product_id = ? ORDER BY is_primary DESC LIMIT 1",
            [$productId]
        );
        return $image['image_path'] ?? null;
    }

    /**
     * Get products by category
     */
    public function getByCategory(int $categoryId, int $page = 1, int $perPage = 12, array $filters = []): array
    {
        // Include subcategory products
        $categoryIds = $this->getCategoryWithChildren($categoryId);
        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $where = "category_id IN ({$placeholders}) AND status = 'active'";
        $params = $categoryIds;

        // Apply filters
        if (!empty($filters['min_price'])) {
            $where .= " AND (COALESCE(sale_price, price)) >= ?";
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where .= " AND (COALESCE(sale_price, price)) <= ?";
            $params[] = $filters['max_price'];
        }

        $orderBy = match($filters['sort'] ?? 'newest') {
            'price_low' => 'COALESCE(sale_price, price) ASC',
            'price_high' => 'COALESCE(sale_price, price) DESC',
            'popular' => 'views DESC',
            default => 'created_at DESC'
        };

        return $this->paginateRaw($page, $perPage, $where, $params, $orderBy);
    }

    /**
     * Get category IDs including children
     */
    private function getCategoryWithChildren(int $categoryId): array
    {
        $ids = [$categoryId];
        $children = $this->db->fetchAll(
            "SELECT id FROM categories WHERE parent_id = ?",
            [$categoryId]
        );
        foreach ($children as $child) {
            $ids[] = $child['id'];
        }
        return $ids;
    }

    /**
     * Paginate with raw query
     */
    private function paginateRaw(int $page, int $perPage, string $where, array $params, string $orderBy): array
    {
        $offset = ($page - 1) * $perPage;

        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE {$where}",
            $params
        );
        $total = $countResult['count'] ?? 0;
        $totalPages = ceil($total / $perPage);

        $products = $this->db->fetchAll(
            "SELECT p.*,
                    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
             FROM products p
             WHERE {$where}
             ORDER BY {$orderBy}
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $products,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages
        ];
    }

    /**
     * Get featured products
     */
    public function getFeatured(int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT p.*,
                    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
             FROM products p
             WHERE p.is_featured = 1 AND p.status = 'active'
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get new arrivals
     */
    public function getNewArrivals(int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT p.*,
                    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
             FROM products p
             WHERE p.status = 'active'
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Search products
     */
    public function search(string $query, int $page = 1, int $perPage = 12): array
    {
        $searchTerm = '%' . $query . '%';
        $where = "(name LIKE ? OR description LIKE ? OR sku LIKE ?) AND status = 'active'";
        $params = [$searchTerm, $searchTerm, $searchTerm];

        return $this->paginateRaw($page, $perPage, $where, $params, 'created_at DESC');
    }

    /**
     * Get related products
     */
    public function getRelated(int $productId, int $categoryId, int $limit = 4): array
    {
        return $this->db->fetchAll(
            "SELECT p.*,
                    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
             FROM products p
             WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
             ORDER BY RAND()
             LIMIT ?",
            [$categoryId, $productId, $limit]
        );
    }

    /**
     * Add product image
     */
    public function addImage(int $productId, string $imagePath, bool $isPrimary = false): int
    {
        if ($isPrimary) {
            $this->db->update('product_images', ['is_primary' => 0], 'product_id = ?', [$productId]);
        }

        return $this->db->insert('product_images', [
            'product_id' => $productId,
            'image_path' => $imagePath,
            'is_primary' => $isPrimary ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Add product variant
     */
    public function addVariant(int $productId, array $data): int
    {
        $data['product_id'] = $productId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->insert('product_variants', $data);
    }

    /**
     * Update stock
     */
    public function updateStock(int $productId, int $quantity, ?int $variantId = null): bool
    {
        if ($variantId) {
            return $this->db->update(
                'product_variants',
                ['stock_quantity' => $quantity],
                'id = ?',
                [$variantId]
            ) > 0;
        }

        return $this->update($productId, ['stock_quantity' => $quantity]);
    }

    /**
     * Get low stock products
     */
    public function getLowStock(int $storeId = 1): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM products
             WHERE store_id = ? AND stock_quantity <= low_stock_threshold AND status = 'active'
             ORDER BY stock_quantity ASC",
            [$storeId]
        );
    }

    /**
     * Get all products for a store (simple list for dropdowns)
     */
    public function getByStore(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, price, sku FROM products WHERE store_id = ? AND status = 'active' ORDER BY name ASC",
            [$storeId]
        );
    }

    /**
     * Get all products for admin
     */
    public function getAllAdmin(int $page = 1, int $perPage = 20, int $storeId = 1): array
    {
        $offset = ($page - 1) * $perPage;

        $total = $this->db->count('products', 'store_id = ?', [$storeId]);
        $totalPages = ceil($total / $perPage);

        $products = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name,
                    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.store_id = ?
             ORDER BY p.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            [$storeId]
        );

        return [
            'data' => $products,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages
        ];
    }
}
