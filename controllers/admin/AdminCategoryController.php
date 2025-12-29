<?php

class AdminCategoryController extends Controller
{
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();

        // Check admin authentication
        if (!Auth::check() || !Auth::isAdmin()) {
            $this->redirect('admin/login');
        }
    }

    public function index()
    {
        $storeId = Session::get('admin_store_id', 1);
        $categories = $this->categoryModel->getAll($storeId, true);

        $this->view('admin/categories/index', [
            'categories' => $categories,
            'pageTitle' => 'Categories'
        ], 'admin');
    }

    public function create()
    {
        $storeId = Session::get('admin_store_id', 1);
        $parentCategories = $this->categoryModel->getParentCategories($storeId);

        $this->view('admin/categories/create', [
            'parents' => $parentCategories,
            'pageTitle' => 'Add Category'
        ], 'admin');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/categories');
        }

        // Verify CSRF
        if (!Session::validateCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid request');
            $this->redirect('admin/categories/create');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'name' => trim($_POST['name'] ?? ''),
            'slug' => $this->generateSlug($_POST['name'] ?? '', $_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'icon' => trim($_POST['icon'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status' => isset($_POST['status']) ? 1 : 0
        ];

        // Validate
        if (empty($data['name'])) {
            Session::flash('error', 'Category name is required');
            $this->redirect('admin/categories/create');
        }

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = $this->uploadImage($_FILES['image'], 'categories');
            if ($uploadResult['success']) {
                $data['image'] = $uploadResult['path'];
            }
        }

        $categoryId = $this->categoryModel->create($data);

        if ($categoryId) {
            Session::flash('success', 'Category created successfully');
            $this->redirect('admin/categories');
        } else {
            Session::flash('error', 'Failed to create category');
            $this->redirect('admin/categories/create');
        }
    }

    public function edit($id)
    {
        $storeId = Session::get('admin_store_id', 1);
        $category = $this->categoryModel->find($id);

        if (!$category || $category['store_id'] != $storeId) {
            Session::flash('error', 'Category not found');
            $this->redirect('admin/categories');
        }

        $parentCategories = $this->categoryModel->getParentCategories($storeId);

        // Remove current category from parent options
        $parentCategories = array_filter($parentCategories, function($cat) use ($id) {
            return $cat['id'] != $id;
        });

        $this->view('admin/categories/edit', [
            'category' => $category,
            'parents' => $parentCategories,
            'pageTitle' => 'Edit Category'
        ], 'admin');
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/categories');
        }

        // Verify CSRF
        if (!Session::validateCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid request');
            $this->redirect('admin/categories/edit/' . $id);
        }

        $storeId = Session::get('admin_store_id', 1);
        $category = $this->categoryModel->find($id);

        if (!$category || $category['store_id'] != $storeId) {
            Session::flash('error', 'Category not found');
            $this->redirect('admin/categories');
        }

        $data = [
            'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'name' => trim($_POST['name'] ?? ''),
            'slug' => $this->generateSlug($_POST['name'] ?? '', $_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'icon' => trim($_POST['icon'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'status' => isset($_POST['status']) ? 1 : 0
        ];

        // Validate
        if (empty($data['name'])) {
            Session::flash('error', 'Category name is required');
            $this->redirect('admin/categories/edit/' . $id);
        }

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = $this->uploadImage($_FILES['image'], 'categories');
            if ($uploadResult['success']) {
                $data['image'] = $uploadResult['path'];
            }
        }

        $updated = $this->categoryModel->update($id, $data);

        if ($updated) {
            Session::flash('success', 'Category updated successfully');
        } else {
            Session::flash('error', 'Failed to update category');
        }

        $this->redirect('admin/categories');
    }

    public function delete($id)
    {
        $storeId = Session::get('admin_store_id', 1);
        $category = $this->categoryModel->find($id);

        if (!$category || $category['store_id'] != $storeId) {
            Session::flash('error', 'Category not found');
            $this->redirect('admin/categories');
        }

        // Check if category has products
        $db = new Database();
        $result = $db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE category_id = ?",
            [$id]
        );
        $productCount = $result['count'] ?? 0;

        if ($productCount > 0) {
            Session::flash('error', 'Cannot delete category with products. Move products first.');
            $this->redirect('admin/categories');
        }

        // Check if category has subcategories
        $result = $db->fetch(
            "SELECT COUNT(*) as count FROM categories WHERE parent_id = ?",
            [$id]
        );
        $subCount = $result['count'] ?? 0;

        if ($subCount > 0) {
            Session::flash('error', 'Cannot delete category with subcategories. Delete subcategories first.');
            $this->redirect('admin/categories');
        }

        $deleted = $this->categoryModel->delete($id);

        if ($deleted) {
            Session::flash('success', 'Category deleted successfully');
        } else {
            Session::flash('error', 'Failed to delete category');
        }

        $this->redirect('admin/categories');
    }

    private function generateSlug($name, $providedSlug = '')
    {
        if (!empty($providedSlug)) {
            return strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($providedSlug)));
        }
        return strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($name)));
    }

    private function uploadImage($file, $folder)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = MAX_IMAGE_SIZE;

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File too large'];
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $uploadDir = UPLOAD_PATH . '/' . $folder . '/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $path = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $path)) {
            return ['success' => true, 'path' => $folder . '/' . $filename];
        }

        return ['success' => false, 'error' => 'Upload failed'];
    }
}
