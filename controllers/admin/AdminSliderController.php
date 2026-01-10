<?php
/**
 * Admin Slider Controller
 */

class AdminSliderController extends Controller
{
    private Slider $sliderModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->sliderModel = new Slider();
    }

    /**
     * List all sliders
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $sliders = $this->sliderModel->getByStore($storeId);

        $this->view('admin/sliders/index', [
            'pageTitle' => 'Sliders - Admin',
            'sliders' => $sliders
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->view('admin/sliders/create', [
            'pageTitle' => 'Add Slider - Admin'
        ], 'admin');
    }

    /**
     * Store new slider
     */
    public function store(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $title = trim($this->post('title'));
        $subtitle = trim($this->post('subtitle', ''));
        $description = trim($this->post('description', ''));
        $buttonText = trim($this->post('button_text', ''));
        $buttonLink = trim($this->post('button_link', ''));
        $button2Text = trim($this->post('button2_text', ''));
        $button2Link = trim($this->post('button2_link', ''));
        $textPosition = $this->post('text_position', 'left');
        $textColor = $this->post('text_color', '#ffffff');
        $overlayOpacity = (float) $this->post('overlay_opacity', 0.40);
        $sortOrder = (int) $this->post('sort_order', 0);
        $status = $this->post('status', 'active');

        // Validation
        if (empty($title)) {
            $this->redirect('admin/sliders/create', 'Title is required', 'error');
            return;
        }

        // Handle image upload
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = $this->uploadImage($_FILES['image']);
            if (!$imageName) {
                $this->redirect('admin/sliders/create', 'Failed to upload image', 'error');
                return;
            }
        }

        $sliderId = $this->sliderModel->create([
            'store_id' => $storeId,
            'title' => $title,
            'subtitle' => $subtitle ?: null,
            'description' => $description ?: null,
            'button_text' => $buttonText ?: null,
            'button_link' => $buttonLink ?: null,
            'button2_text' => $button2Text ?: null,
            'button2_link' => $button2Link ?: null,
            'image' => $imageName,
            'text_position' => $textPosition,
            'text_color' => $textColor,
            'overlay_opacity' => $overlayOpacity,
            'sort_order' => $sortOrder,
            'status' => $status
        ]);

        if ($sliderId) {
            $this->redirect('admin/sliders', 'Slider created successfully');
        } else {
            $this->redirect('admin/sliders/create', 'Failed to create slider', 'error');
        }
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $slider = $this->sliderModel->find($id);

        if (!$slider || $slider['store_id'] != $storeId) {
            $this->redirect('admin/sliders', 'Slider not found', 'error');
            return;
        }

        $this->view('admin/sliders/edit', [
            'pageTitle' => 'Edit Slider - Admin',
            'slider' => $slider
        ], 'admin');
    }

    /**
     * Update slider
     */
    public function update(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $slider = $this->sliderModel->find($id);

        if (!$slider || $slider['store_id'] != $storeId) {
            $this->redirect('admin/sliders', 'Slider not found', 'error');
            return;
        }

        $title = trim($this->post('title'));
        $subtitle = trim($this->post('subtitle', ''));
        $description = trim($this->post('description', ''));
        $buttonText = trim($this->post('button_text', ''));
        $buttonLink = trim($this->post('button_link', ''));
        $button2Text = trim($this->post('button2_text', ''));
        $button2Link = trim($this->post('button2_link', ''));
        $textPosition = $this->post('text_position', 'left');
        $textColor = $this->post('text_color', '#ffffff');
        $overlayOpacity = (float) $this->post('overlay_opacity', 0.40);
        $sortOrder = (int) $this->post('sort_order', 0);
        $status = $this->post('status', 'active');

        // Validation
        if (empty($title)) {
            $this->redirect('admin/sliders/edit/' . $id, 'Title is required', 'error');
            return;
        }

        $updateData = [
            'title' => $title,
            'subtitle' => $subtitle ?: null,
            'description' => $description ?: null,
            'button_text' => $buttonText ?: null,
            'button_link' => $buttonLink ?: null,
            'button2_text' => $button2Text ?: null,
            'button2_link' => $button2Link ?: null,
            'text_position' => $textPosition,
            'text_color' => $textColor,
            'overlay_opacity' => $overlayOpacity,
            'sort_order' => $sortOrder,
            'status' => $status
        ];

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = $this->uploadImage($_FILES['image']);
            if ($imageName) {
                // Delete old image
                if ($slider['image']) {
                    $oldPath = ROOT_PATH . '/public/uploads/sliders/' . $slider['image'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $updateData['image'] = $imageName;
            }
        }

        $this->sliderModel->update($id, $updateData);
        $this->redirect('admin/sliders', 'Slider updated successfully');
    }

    /**
     * Delete slider
     */
    public function delete(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $slider = $this->sliderModel->find($id);

        if (!$slider || $slider['store_id'] != $storeId) {
            $this->redirect('admin/sliders', 'Slider not found', 'error');
            return;
        }

        $this->sliderModel->delete($id);
        $this->redirect('admin/sliders', 'Slider deleted successfully');
    }

    /**
     * Toggle slider status (AJAX)
     */
    public function toggle(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $slider = $this->sliderModel->find($id);

        if (!$slider || $slider['store_id'] != $storeId) {
            $this->json(['success' => false, 'message' => 'Slider not found']);
            return;
        }

        $this->sliderModel->toggleStatus($id);
        $newStatus = $slider['status'] === 'active' ? 'inactive' : 'active';

        $this->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $newStatus === 'active' ? 'Slider activated' : 'Slider deactivated'
        ]);
    }

    /**
     * Update sort order (AJAX)
     */
    public function updateOrder(): void
    {
        $order = $this->post('order', []);

        if (!empty($order)) {
            $this->sliderModel->updateSortOrder($order);
            $this->json(['success' => true, 'message' => 'Order updated']);
        } else {
            $this->json(['success' => false, 'message' => 'No order data provided']);
        }
    }

    /**
     * Upload slider image
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

        $uploadDir = ROOT_PATH . '/public/uploads/sliders/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'slider_' . time() . '_' . uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        return null;
    }
}
