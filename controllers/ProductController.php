<?php
/**
 * Product Controller
 */

class ProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    /**
     * Shop page - All products
     */
    public function index(): void
    {
        $page = (int) $this->get('page', 1);
        $sort = $this->get('sort', 'newest');
        $minPrice = $this->get('min_price');
        $maxPrice = $this->get('max_price');

        $filters = [
            'sort' => $sort,
            'min_price' => $minPrice,
            'max_price' => $maxPrice
        ];

        $products = $this->productModel->paginate(
            $page,
            PRODUCTS_PER_PAGE,
            "status = 'active'",
            [],
            $this->getSortOrder($sort)
        );

        // Add images to products
        foreach ($products['data'] as &$product) {
            $product['image'] = $this->productModel->getPrimaryImage($product['id']);
        }

        $data = [
            'pageTitle' => 'Shop - ' . SITE_NAME,
            'products' => $products,
            'categories' => $this->categoryModel->getWithChildren(),
            'currentSort' => $sort,
            'filters' => $filters
        ];

        $this->view('products/index', $data);
    }

    /**
     * Category page
     */
    public function category(string $slug): void
    {
        $category = $this->categoryModel->findBySlug($slug);

        if (!$category) {
            $this->redirect('shop', 'Category not found', 'error');
            return;
        }

        $page = (int) $this->get('page', 1);
        $sort = $this->get('sort', 'newest');
        $filters = [
            'sort' => $sort,
            'min_price' => $this->get('min_price'),
            'max_price' => $this->get('max_price')
        ];

        $products = $this->productModel->getByCategory($category['id'], $page, PRODUCTS_PER_PAGE, $filters);

        $data = [
            'pageTitle' => $category['name'] . ' - ' . SITE_NAME,
            'category' => $category,
            'products' => $products,
            'categories' => $this->categoryModel->getWithChildren(),
            'breadcrumb' => $this->categoryModel->getBreadcrumb($category['id']),
            'currentSort' => $sort,
            'filters' => $filters
        ];

        $this->view('products/category', $data);
    }

    /**
     * Product detail page
     */
    public function show(string $slug): void
    {
        $product = $this->productModel->findBySlug($slug);

        if (!$product) {
            $this->redirect('shop', 'Product not found', 'error');
            return;
        }

        $relatedProducts = $this->productModel->getRelated(
            $product['id'],
            $product['category_id'],
            4
        );

        $data = [
            'pageTitle' => $product['name'] . ' - ' . SITE_NAME,
            'metaDescription' => $product['short_description'] ?? truncate($product['description'], 160),
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ];

        $this->view('products/show', $data);
    }

    /**
     * Search products
     */
    public function search(): void
    {
        $query = trim($this->get('q', ''));
        $page = (int) $this->get('page', 1);

        if (empty($query)) {
            $this->redirect('shop');
            return;
        }

        $products = $this->productModel->search($query, $page, PRODUCTS_PER_PAGE);

        $data = [
            'pageTitle' => 'Search: ' . $query . ' - ' . SITE_NAME,
            'products' => $products,
            'searchQuery' => $query,
            'categories' => $this->categoryModel->getWithChildren()
        ];

        $this->view('products/search', $data);
    }

    /**
     * AJAX filter products
     */
    public function filter(): void
    {
        $categoryId = $this->get('category_id');
        $page = (int) $this->get('page', 1);
        $filters = [
            'sort' => $this->get('sort', 'newest'),
            'min_price' => $this->get('min_price'),
            'max_price' => $this->get('max_price')
        ];

        if ($categoryId) {
            $products = $this->productModel->getByCategory($categoryId, $page, PRODUCTS_PER_PAGE, $filters);
        } else {
            $products = $this->productModel->paginate(
                $page,
                PRODUCTS_PER_PAGE,
                "status = 'active'",
                [],
                $this->getSortOrder($filters['sort'])
            );
        }

        $this->json(['success' => true, 'data' => $products]);
    }

    /**
     * Get sort order SQL
     */
    private function getSortOrder(string $sort): string
    {
        return match($sort) {
            'price_low' => 'COALESCE(sale_price, price) ASC',
            'price_high' => 'COALESCE(sale_price, price) DESC',
            'popular' => 'views DESC',
            'name_asc' => 'name ASC',
            'name_desc' => 'name DESC',
            default => 'created_at DESC'
        };
    }
}
