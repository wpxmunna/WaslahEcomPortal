<?php

class AdminCourierController extends Controller
{
    private $courierModel;

    public function __construct()
    {
        parent::__construct();
        $this->courierModel = new Courier();

        // Check admin authentication
        if (!Auth::isAdmin()) {
            $this->redirect('admin/login');
        }

        requireFullAdmin(); // Only full admins can manage couriers
    }

    public function index()
    {
        $storeId = Session::get('admin_store_id', 1);
        $couriers = $this->courierModel->getByStore($storeId);

        $this->view('admin/couriers/index', [
            'couriers' => $couriers,
            'pageTitle' => 'Couriers'
        ], 'admin');
    }

    public function create()
    {
        $this->view('admin/couriers/create', [
            'pageTitle' => 'Add Courier'
        ], 'admin');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/couriers');
        }

        // Verify CSRF
        if (!Session::validateCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid request');
            $this->redirect('admin/couriers/create');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'name' => trim($_POST['name'] ?? ''),
            'code' => strtolower(trim($_POST['code'] ?? '')),
            'description' => trim($_POST['description'] ?? ''),
            'base_rate' => (float)($_POST['base_rate'] ?? 0),
            'per_kg_rate' => (float)($_POST['per_kg_rate'] ?? 0),
            'estimated_days' => trim($_POST['estimated_days'] ?? ''),
            'tracking_url' => trim($_POST['tracking_url'] ?? ''),
            'status' => isset($_POST['status']) ? 1 : 0
        ];

        // Validate
        if (empty($data['name']) || empty($data['code'])) {
            Session::flash('error', 'Name and code are required');
            $this->redirect('admin/couriers/create');
        }

        // Check if code already exists
        $existing = $this->db->fetch(
            "SELECT id FROM couriers WHERE store_id = ? AND code = ?",
            [$storeId, $data['code']]
        );

        if ($existing) {
            Session::flash('error', 'Courier code already exists');
            $this->redirect('admin/couriers/create');
        }

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $uploadResult = $this->uploadImage($_FILES['logo'], 'couriers');
            if ($uploadResult['success']) {
                $data['logo'] = $uploadResult['path'];
            }
        }

        $courierId = $this->courierModel->create($data);

        if ($courierId) {
            Session::flash('success', 'Courier created successfully');
            $this->redirect('admin/couriers');
        } else {
            Session::flash('error', 'Failed to create courier');
            $this->redirect('admin/couriers/create');
        }
    }

    public function edit($id)
    {
        $storeId = Session::get('admin_store_id', 1);
        $courier = $this->courierModel->find($id);

        if (!$courier || $courier['store_id'] != $storeId) {
            Session::flash('error', 'Courier not found');
            $this->redirect('admin/couriers');
        }

        $this->view('admin/couriers/edit', [
            'courier' => $courier,
            'pageTitle' => 'Edit Courier'
        ], 'admin');
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/couriers');
        }

        // Verify CSRF
        if (!Session::validateCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid request');
            $this->redirect('admin/couriers/edit/' . $id);
        }

        $storeId = Session::get('admin_store_id', 1);
        $courier = $this->courierModel->find($id);

        if (!$courier || $courier['store_id'] != $storeId) {
            Session::flash('error', 'Courier not found');
            $this->redirect('admin/couriers');
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'code' => strtolower(trim($_POST['code'] ?? '')),
            'description' => trim($_POST['description'] ?? ''),
            'base_rate' => (float)($_POST['base_rate'] ?? 0),
            'per_kg_rate' => (float)($_POST['per_kg_rate'] ?? 0),
            'estimated_days' => trim($_POST['estimated_days'] ?? ''),
            'tracking_url' => trim($_POST['tracking_url'] ?? ''),
            'status' => isset($_POST['status']) ? 1 : 0
        ];

        // Validate
        if (empty($data['name']) || empty($data['code'])) {
            Session::flash('error', 'Name and code are required');
            $this->redirect('admin/couriers/edit/' . $id);
        }

        // Check if code already exists for another courier
        $existing = $this->db->fetch(
            "SELECT id FROM couriers WHERE store_id = ? AND code = ? AND id != ?",
            [$storeId, $data['code'], $id]
        );

        if ($existing) {
            Session::flash('error', 'Courier code already exists');
            $this->redirect('admin/couriers/edit/' . $id);
        }

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $uploadResult = $this->uploadImage($_FILES['logo'], 'couriers');
            if ($uploadResult['success']) {
                $data['logo'] = $uploadResult['path'];
            }
        }

        $updated = $this->courierModel->update($id, $data);

        if ($updated) {
            Session::flash('success', 'Courier updated successfully');
        } else {
            Session::flash('error', 'Failed to update courier');
        }

        $this->redirect('admin/couriers');
    }

    public function delete($id)
    {
        $storeId = Session::get('admin_store_id', 1);
        $courier = $this->courierModel->find($id);

        if (!$courier || $courier['store_id'] != $storeId) {
            Session::flash('error', 'Courier not found');
            $this->redirect('admin/couriers');
        }

        // Check if courier has shipments
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM shipments WHERE courier_id = ?",
            [$id]
        );
        $shipmentCount = $result['count'] ?? 0;

        if ($shipmentCount > 0) {
            Session::flash('error', 'Cannot delete courier with existing shipments');
            $this->redirect('admin/couriers');
        }

        $deleted = $this->courierModel->delete($id);

        if ($deleted) {
            Session::flash('success', 'Courier deleted successfully');
        } else {
            Session::flash('error', 'Failed to delete courier');
        }

        $this->redirect('admin/couriers');
    }

    public function toggleStatus($id)
    {
        $storeId = Session::get('admin_store_id', 1);
        $courier = $this->courierModel->find($id);

        if (!$courier || $courier['store_id'] != $storeId) {
            $this->json(['success' => false, 'message' => 'Courier not found']);
        }

        $newStatus = $courier['status'] ? 0 : 1;
        $this->courierModel->update($id, ['status' => $newStatus]);

        $this->json(['success' => true, 'status' => $newStatus]);
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
