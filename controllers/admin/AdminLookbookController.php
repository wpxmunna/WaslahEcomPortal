<?php
/**
 * Admin Lookbook Controller
 */

class AdminLookbookController extends Controller
{
    private Lookbook $lookbookModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->lookbookModel = new Lookbook();
    }

    /**
     * List all lookbook items
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $items = $this->lookbookModel->getByStore($storeId);

        $this->view('admin/lookbook/index', [
            'pageTitle' => 'Lookbook Gallery - Admin',
            'items' => $items
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->view('admin/lookbook/create', [
            'pageTitle' => 'Add Lookbook Image - Admin'
        ], 'admin');
    }

    /**
     * Store new lookbook item
     */
    public function store(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $link = trim($this->post('link', ''));
        $caption = trim($this->post('caption', ''));
        $isFeatured = $this->post('is_featured') ? 1 : 0;
        $sortOrder = (int) $this->post('sort_order', 0);
        $status = $this->post('status', 'active');

        // Handle image upload or URL
        $image = null;
        $imageUrl = trim($this->post('image_url', ''));

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $this->uploadImage($_FILES['image']);
            if (!$image) {
                $this->redirect('admin/lookbook/create', 'Failed to upload image', 'error');
                return;
            }
        } elseif (!empty($imageUrl)) {
            // Use external URL
            $image = $imageUrl;
        }

        if (empty($image)) {
            $this->redirect('admin/lookbook/create', 'Image is required', 'error');
            return;
        }

        $itemId = $this->lookbookModel->create([
            'store_id' => $storeId,
            'image' => $image,
            'link' => $link ?: null,
            'caption' => $caption ?: null,
            'is_featured' => $isFeatured,
            'sort_order' => $sortOrder,
            'status' => $status
        ]);

        if ($itemId) {
            // If set as featured, update others
            if ($isFeatured) {
                $this->lookbookModel->setFeatured($itemId, $storeId);
            }
            $this->redirect('admin/lookbook', 'Image added successfully');
        } else {
            $this->redirect('admin/lookbook/create', 'Failed to add image', 'error');
        }
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $item = $this->lookbookModel->find($id);

        if (!$item || $item['store_id'] != $storeId) {
            $this->redirect('admin/lookbook', 'Item not found', 'error');
            return;
        }

        $this->view('admin/lookbook/edit', [
            'pageTitle' => 'Edit Lookbook Image - Admin',
            'item' => $item
        ], 'admin');
    }

    /**
     * Update lookbook item
     */
    public function update(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $item = $this->lookbookModel->find($id);

        if (!$item || $item['store_id'] != $storeId) {
            $this->redirect('admin/lookbook', 'Item not found', 'error');
            return;
        }

        $link = trim($this->post('link', ''));
        $caption = trim($this->post('caption', ''));
        $isFeatured = $this->post('is_featured') ? 1 : 0;
        $sortOrder = (int) $this->post('sort_order', 0);
        $status = $this->post('status', 'active');

        $updateData = [
            'link' => $link ?: null,
            'caption' => $caption ?: null,
            'is_featured' => $isFeatured,
            'sort_order' => $sortOrder,
            'status' => $status
        ];

        // Handle image upload or URL
        $imageUrl = trim($this->post('image_url', ''));

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $newImage = $this->uploadImage($_FILES['image']);
            if ($newImage) {
                // Delete old local image
                if ($item['image'] && !filter_var($item['image'], FILTER_VALIDATE_URL)) {
                    $oldPath = ROOT_PATH . '/public/uploads/lookbook/' . $item['image'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $updateData['image'] = $newImage;
            }
        } elseif (!empty($imageUrl) && $imageUrl !== $item['image']) {
            // Delete old local image if changing to URL
            if ($item['image'] && !filter_var($item['image'], FILTER_VALIDATE_URL)) {
                $oldPath = ROOT_PATH . '/public/uploads/lookbook/' . $item['image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $updateData['image'] = $imageUrl;
        }

        $this->lookbookModel->update($id, $updateData);

        // If set as featured, update others
        if ($isFeatured) {
            $this->lookbookModel->setFeatured($id, $storeId);
        }

        $this->redirect('admin/lookbook', 'Image updated successfully');
    }

    /**
     * Delete lookbook item
     */
    public function delete(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $item = $this->lookbookModel->find($id);

        if (!$item || $item['store_id'] != $storeId) {
            $this->redirect('admin/lookbook', 'Item not found', 'error');
            return;
        }

        $this->lookbookModel->delete($id);
        $this->redirect('admin/lookbook', 'Image deleted successfully');
    }

    /**
     * Toggle status (AJAX)
     */
    public function toggle(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $item = $this->lookbookModel->find($id);

        if (!$item || $item['store_id'] != $storeId) {
            $this->json(['success' => false, 'message' => 'Item not found']);
            return;
        }

        $this->lookbookModel->toggleStatus($id);
        $newStatus = $item['status'] === 'active' ? 'inactive' : 'active';

        $this->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $newStatus === 'active' ? 'Image activated' : 'Image deactivated'
        ]);
    }

    /**
     * Set as featured (AJAX)
     */
    public function featured(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $item = $this->lookbookModel->find($id);

        if (!$item || $item['store_id'] != $storeId) {
            $this->json(['success' => false, 'message' => 'Item not found']);
            return;
        }

        $this->lookbookModel->setFeatured($id, $storeId);

        $this->json([
            'success' => true,
            'message' => 'Image set as featured'
        ]);
    }

    /**
     * Upload lookbook image
     */
    private function uploadImage(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        if ($file['size'] > $maxSize) {
            return null;
        }

        $uploadDir = ROOT_PATH . '/public/uploads/lookbook/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'lookbook_' . time() . '_' . uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        return null;
    }
}
