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
        $storeId = Session::get('admin_store_id', 1);

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
        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'pageTitle' => 'Add Product - Admin',
            'categories' => $this->categoryModel->getAllAdmin($storeId),
            'colors' => $this->getColors($storeId),
            'stores' => (new Store())->getActive()
        ];

        $this->view('admin/products/create', $data, 'admin');
    }

    /**
     * Store new product
     */
    public function store(): void
    {
        // Ensure this is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/products/create');
            return;
        }

        // Validate required fields
        $name = $this->post('name');
        if (empty($name)) {
            Session::setFlash('Product name is required', 'error');
            $this->redirect('admin/products/create');
            return;
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'category_id' => $this->post('category_id') ?: null,
            'name' => $name,
            'slug' => $this->post('slug') ?: slugify($name),
            'description' => $this->post('description'),
            'short_description' => $this->post('short_description'),
            'price' => (float) $this->post('price'),
            'sale_price' => $this->post('sale_price') ? (float) $this->post('sale_price') : null,
            'cost_price' => $this->post('cost_price') ? (float) $this->post('cost_price') : null,
            'sku' => $this->post('sku'),
            'stock_quantity' => 0, // Will be calculated from variants
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

        // Handle variants (new format: variants[colorId][size] = quantity)
        $variants = $this->post('variants', []);
        $totalStock = $this->saveVariants($productId, $variants, $storeId);

        // Update total stock
        $this->productModel->update($productId, ['stock_quantity' => $totalStock]);

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

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'pageTitle' => 'Edit Product - Admin',
            'product' => $product,
            'images' => $this->productModel->getImages($id),
            'variants' => $this->productModel->getVariants($id),
            'categories' => $this->categoryModel->getAllAdmin($storeId),
            'colors' => $this->getColors($storeId),
            'stores' => (new Store())->getActive()
        ];

        $this->view('admin/products/edit', $data, 'admin');
    }

    /**
     * Update product
     */
    public function update(int $id): void
    {
        // Ensure this is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/products/edit/' . $id);
            return;
        }

        // Validate required fields
        $name = $this->post('name');
        if (empty($name)) {
            Session::setFlash('Product name is required', 'error');
            $this->redirect('admin/products/edit/' . $id);
            return;
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'category_id' => $this->post('category_id') ?: null,
            'name' => $name,
            'slug' => $this->post('slug') ?: slugify($name),
            'description' => $this->post('description'),
            'short_description' => $this->post('short_description'),
            'price' => (float) $this->post('price'),
            'sale_price' => $this->post('sale_price') ? (float) $this->post('sale_price') : null,
            'cost_price' => $this->post('cost_price') ? (float) $this->post('cost_price') : null,
            'sku' => $this->post('sku'),
            'low_stock_threshold' => (int) $this->post('low_stock_threshold', 5),
            'is_featured' => $this->post('is_featured') ? 1 : 0,
            'is_new' => $this->post('is_new') ? 1 : 0,
            'status' => $this->post('status', 'active'),
            'meta_title' => $this->post('meta_title'),
            'meta_description' => $this->post('meta_description')
        ];

        $this->productModel->update($id, $data);

        // Handle variants update
        $variants = $this->post('variants', []);
        if (!empty($variants)) {
            // Delete existing variants
            $this->db->delete('product_variants', 'product_id = ?', [$id]);

            // Save new variants
            $totalStock = $this->saveVariants($id, $variants, $storeId);

            // Update total stock
            $this->productModel->update($id, ['stock_quantity' => $totalStock]);
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
     * Save product variants
     */
    private function saveVariants(int $productId, array $variants, int $storeId): int
    {
        $totalStock = 0;
        $colors = $this->getColors($storeId);
        $colorMap = [];
        foreach ($colors as $color) {
            $colorMap[$color['id']] = $color;
        }

        foreach ($variants as $colorId => $sizes) {
            $colorInfo = $colorMap[$colorId] ?? null;

            foreach ($sizes as $size => $quantity) {
                $quantity = (int) $quantity;
                if ($quantity < 0) $quantity = 0;

                $totalStock += $quantity;

                $this->productModel->addVariant($productId, [
                    'color_id' => $colorId,
                    'color' => $colorInfo['name'] ?? null,
                    'color_code' => $colorInfo['color_code'] ?? null,
                    'size' => $size,
                    'stock_quantity' => $quantity,
                    'status' => 1
                ]);
            }
        }

        return $totalStock;
    }

    /**
     * Get available colors
     */
    private function getColors(int $storeId = 1): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM product_colors WHERE store_id = ? AND status = 1 ORDER BY sort_order, name",
            [$storeId]
        );
    }

    /**
     * Upload images (separate form)
     */
    public function uploadImages(int $productId): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/products/edit/' . $productId);
            return;
        }

        if (!Session::validateCsrf($_POST['csrf_token'] ?? '')) {
            Session::setFlash('Invalid request', 'error');
            $this->redirect('admin/products/edit/' . $productId);
            return;
        }

        $uploaded = 0;
        $errors = [];

        if (!empty($_FILES['images']['name'][0])) {
            $hasExistingPrimary = $this->productModel->hasPrimaryImage($productId);
            $isFirst = true;

            foreach ($_FILES['images']['name'] as $key => $name) {
                if (empty($name)) continue;

                $error = $_FILES['images']['error'][$key];
                $size = $_FILES['images']['size'][$key];
                $type = $_FILES['images']['type'][$key];

                // Check for upload errors
                if ($error !== UPLOAD_ERR_OK) {
                    $errors[] = "$name: Upload error code $error";
                    continue;
                }

                // Check size
                if ($size > MAX_IMAGE_SIZE) {
                    $errors[] = "$name: File too large (" . round($size/1024/1024, 1) . "MB, max " . (MAX_IMAGE_SIZE/1024/1024) . "MB)";
                    continue;
                }

                // Check type
                if (!in_array($type, ALLOWED_IMAGE_TYPES)) {
                    $errors[] = "$name: Invalid type ($type)";
                    continue;
                }

                $file = [
                    'name' => $name,
                    'type' => $type,
                    'tmp_name' => $_FILES['images']['tmp_name'][$key],
                    'error' => $error,
                    'size' => $size
                ];

                $imagePath = $this->uploadFile($file, 'products');
                if ($imagePath) {
                    $isPrimary = (!$hasExistingPrimary && $isFirst);
                    $this->productModel->addImage($productId, $imagePath, $isPrimary);
                    $uploaded++;
                    $isFirst = false;
                } else {
                    $errors[] = "$name: Failed to save file";
                }
            }
        } else {
            $errors[] = "No files received";
        }

        if ($uploaded > 0) {
            Session::setFlash("$uploaded image(s) uploaded successfully", 'success');
        } else {
            $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Unknown error';
            Session::setFlash("Upload failed: $errorMsg", 'error');
        }

        $this->redirect('admin/products/edit/' . $productId);
    }

    /**
     * Delete product image
     */
    public function deleteImage(int $imageId): void
    {
        $image = $this->db->fetch("SELECT * FROM product_images WHERE id = ?", [$imageId]);

        if ($image) {
            $productId = $image['product_id'];

            // Delete from database
            $this->db->delete('product_images', 'id = ?', [$imageId]);

            // Delete file
            $filePath = ROOT_PATH . '/uploads/' . $image['image_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }

            Session::setFlash('Image deleted', 'success');
            $this->redirect('admin/products/edit/' . $productId);
        } else {
            Session::setFlash('Image not found', 'error');
            $this->redirect('admin/products');
        }
    }

    /**
     * Set primary image
     */
    public function setPrimary(int $productId, int $imageId): void
    {
        // Remove primary from all
        $this->db->query("UPDATE product_images SET is_primary = 0 WHERE product_id = ?", [$productId]);

        // Set new primary
        $this->db->query("UPDATE product_images SET is_primary = 1 WHERE id = ?", [$imageId]);

        Session::setFlash('Primary image updated', 'success');
        $this->redirect('admin/products/edit/' . $productId);
    }
}
