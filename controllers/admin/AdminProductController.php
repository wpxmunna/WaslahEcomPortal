<?php
/**
 * Admin Product Controller
 */

class AdminProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    /**
     * List products
     */
    public function index(): void
    {
        $page = (int) $this->get('page', 1);
        $storeId = Session::get('current_store_id', 1);

        $products = $this->productModel->getAllAdmin($page, 20, $storeId);

        $data = [
            'pageTitle' => 'Products - Admin',
            'products' => $products,
            'stores' => (new Store())->getActive()
        ];

        $this->view('admin/products/index', $data, 'admin');
    }

    /**
     * Create product form
     */
    public function create(): void
    {
        $storeId = Session::get('current_store_id', 1);

        $data = [
            'pageTitle' => 'Add Product - Admin',
            'categories' => $this->categoryModel->getAllAdmin($storeId),
            'stores' => (new Store())->getActive()
        ];

        $this->view('admin/products/create', $data, 'admin');
    }

    /**
     * Store new product
     */
    public function store(): void
    {
        $storeId = Session::get('current_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'category_id' => $this->post('category_id') ?: null,
            'name' => $this->post('name'),
            'slug' => $this->post('slug') ?: slugify($this->post('name')),
            'description' => $this->post('description'),
            'short_description' => $this->post('short_description'),
            'price' => (float) $this->post('price'),
            'sale_price' => $this->post('sale_price') ? (float) $this->post('sale_price') : null,
            'cost_price' => $this->post('cost_price') ? (float) $this->post('cost_price') : null,
            'sku' => $this->post('sku'),
            'stock_quantity' => (int) $this->post('stock_quantity', 0),
            'low_stock_threshold' => (int) $this->post('low_stock_threshold', 5),
            'is_featured' => $this->post('is_featured') ? 1 : 0,
            'is_new' => $this->post('is_new') ? 1 : 0,
            'status' => $this->post('status', 'active'),
            'meta_title' => $this->post('meta_title'),
            'meta_description' => $this->post('meta_description')
        ];

        $productId = $this->productModel->create($data);

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'products');
            if ($imagePath) {
                $this->productModel->addImage($productId, $imagePath, true);
            }
        }

        // Handle multiple images
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $name) {
                if (empty($name)) continue;

                $file = [
                    'name' => $_FILES['images']['name'][$key],
                    'type' => $_FILES['images']['type'][$key],
                    'tmp_name' => $_FILES['images']['tmp_name'][$key],
                    'error' => $_FILES['images']['error'][$key],
                    'size' => $_FILES['images']['size'][$key]
                ];

                $imagePath = $this->uploadFile($file, 'products');
                if ($imagePath) {
                    $this->productModel->addImage($productId, $imagePath);
                }
            }
        }

        // Handle variants
        $sizes = $this->post('variant_sizes', []);
        $colors = $this->post('variant_colors', []);

        if (!empty($sizes) || !empty($colors)) {
            $this->createVariants($productId, $sizes, $colors);
        }

        $this->redirect('admin/products', 'Product created successfully');
    }

    /**
     * Edit product form
     */
    public function edit(int $id): void
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            $this->redirect('admin/products', 'Product not found', 'error');
            return;
        }

        $storeId = Session::get('current_store_id', 1);

        $data = [
            'pageTitle' => 'Edit Product - Admin',
            'product' => $product,
            'images' => $this->productModel->getImages($id),
            'variants' => $this->productModel->getVariants($id),
            'categories' => $this->categoryModel->getAllAdmin($storeId),
            'stores' => (new Store())->getActive()
        ];

        $this->view('admin/products/edit', $data, 'admin');
    }

    /**
     * Update product
     */
    public function update(int $id): void
    {
        $data = [
            'category_id' => $this->post('category_id') ?: null,
            'name' => $this->post('name'),
            'slug' => $this->post('slug') ?: slugify($this->post('name')),
            'description' => $this->post('description'),
            'short_description' => $this->post('short_description'),
            'price' => (float) $this->post('price'),
            'sale_price' => $this->post('sale_price') ? (float) $this->post('sale_price') : null,
            'cost_price' => $this->post('cost_price') ? (float) $this->post('cost_price') : null,
            'sku' => $this->post('sku'),
            'stock_quantity' => (int) $this->post('stock_quantity', 0),
            'low_stock_threshold' => (int) $this->post('low_stock_threshold', 5),
            'is_featured' => $this->post('is_featured') ? 1 : 0,
            'is_new' => $this->post('is_new') ? 1 : 0,
            'status' => $this->post('status', 'active'),
            'meta_title' => $this->post('meta_title'),
            'meta_description' => $this->post('meta_description')
        ];

        $this->productModel->update($id, $data);

        // Handle new image upload
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'products');
            if ($imagePath) {
                $this->productModel->addImage($id, $imagePath, true);
            }
        }

        $this->redirect('admin/products/edit/' . $id, 'Product updated successfully');
    }

    /**
     * Delete product
     */
    public function delete(int $id): void
    {
        $this->productModel->delete($id);
        $this->redirect('admin/products', 'Product deleted successfully');
    }

    /**
     * Create product variants
     */
    private function createVariants(int $productId, array $sizes, array $colors): void
    {
        if (!empty($sizes) && !empty($colors)) {
            foreach ($sizes as $size) {
                foreach ($colors as $color) {
                    $this->productModel->addVariant($productId, [
                        'size' => $size,
                        'color' => $color['name'] ?? $color,
                        'color_code' => $color['code'] ?? null,
                        'stock_quantity' => 10,
                        'status' => 1
                    ]);
                }
            }
        } elseif (!empty($sizes)) {
            foreach ($sizes as $size) {
                $this->productModel->addVariant($productId, [
                    'size' => $size,
                    'stock_quantity' => 10,
                    'status' => 1
                ]);
            }
        } elseif (!empty($colors)) {
            foreach ($colors as $color) {
                $this->productModel->addVariant($productId, [
                    'color' => $color['name'] ?? $color,
                    'color_code' => $color['code'] ?? null,
                    'stock_quantity' => 10,
                    'status' => 1
                ]);
            }
        }
    }
}
