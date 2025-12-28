<?php

class AdminSettingsController extends Controller
{
    private $storeModel;

    public function __construct()
    {
        parent::__construct();
        $this->storeModel = new Store();

        // Check admin authentication
        if (!Auth::check() || !Auth::isAdmin()) {
            $this->redirect('admin/login');
        }
    }

    public function index()
    {
        $storeId = Session::get('admin_store_id', 1);
        $store = $this->storeModel->find($storeId);
        $settings = $this->getSettings($storeId);

        $this->view('admin/settings/index', [
            'store' => $store,
            'settings' => $settings,
            'pageTitle' => 'Settings'
        ], 'admin');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/settings');
        }

        // Verify CSRF
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid request');
            $this->redirect('admin/settings');
        }

        $storeId = Session::get('admin_store_id', 1);

        // Update store info
        $storeData = [
            'name' => trim($_POST['store_name'] ?? ''),
            'email' => trim($_POST['store_email'] ?? ''),
            'phone' => trim($_POST['store_phone'] ?? ''),
            'address' => trim($_POST['store_address'] ?? ''),
            'description' => trim($_POST['store_description'] ?? '')
        ];

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $uploadResult = $this->uploadImage($_FILES['logo'], 'stores');
            if ($uploadResult['success']) {
                $storeData['logo'] = $uploadResult['path'];
            }
        }

        $this->storeModel->update($storeId, $storeData);

        // Update settings
        $settingsToSave = [
            'currency_symbol' => $_POST['currency_symbol'] ?? '$',
            'currency_code' => $_POST['currency_code'] ?? 'USD',
            'tax_rate' => (float)($_POST['tax_rate'] ?? 0),
            'free_shipping_threshold' => (float)($_POST['free_shipping_threshold'] ?? 0),
            'default_shipping_cost' => (float)($_POST['default_shipping_cost'] ?? 0),
            'products_per_page' => (int)($_POST['products_per_page'] ?? 12),
            'allow_guest_checkout' => isset($_POST['allow_guest_checkout']) ? '1' : '0',
            'order_prefix' => $_POST['order_prefix'] ?? 'ORD',
            'facebook_url' => $_POST['facebook_url'] ?? '',
            'instagram_url' => $_POST['instagram_url'] ?? '',
            'twitter_url' => $_POST['twitter_url'] ?? '',
            'footer_text' => $_POST['footer_text'] ?? ''
        ];

        foreach ($settingsToSave as $key => $value) {
            $this->saveSetting($storeId, $key, $value);
        }

        Session::flash('success', 'Settings updated successfully');
        $this->redirect('admin/settings');
    }

    public function payment()
    {
        $storeId = Session::get('admin_store_id', 1);
        $settings = $this->getSettings($storeId);

        $this->view('admin/settings/payment', [
            'settings' => $settings,
            'pageTitle' => 'Payment Settings'
        ], 'admin');
    }

    public function updatePayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/settings/payment');
        }

        // Verify CSRF
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid request');
            $this->redirect('admin/settings/payment');
        }

        $storeId = Session::get('admin_store_id', 1);

        $paymentSettings = [
            'payment_cod_enabled' => isset($_POST['payment_cod_enabled']) ? '1' : '0',
            'payment_stripe_enabled' => isset($_POST['payment_stripe_enabled']) ? '1' : '0',
            'payment_paypal_enabled' => isset($_POST['payment_paypal_enabled']) ? '1' : '0',
            'stripe_public_key' => $_POST['stripe_public_key'] ?? '',
            'stripe_secret_key' => $_POST['stripe_secret_key'] ?? '',
            'paypal_client_id' => $_POST['paypal_client_id'] ?? '',
            'paypal_secret' => $_POST['paypal_secret'] ?? '',
            'paypal_mode' => $_POST['paypal_mode'] ?? 'sandbox'
        ];

        foreach ($paymentSettings as $key => $value) {
            $this->saveSetting($storeId, $key, $value);
        }

        Session::flash('success', 'Payment settings updated successfully');
        $this->redirect('admin/settings/payment');
    }

    public function email()
    {
        $storeId = Session::get('admin_store_id', 1);
        $settings = $this->getSettings($storeId);

        $this->view('admin/settings/email', [
            'settings' => $settings,
            'pageTitle' => 'Email Settings'
        ], 'admin');
    }

    public function updateEmail()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/settings/email');
        }

        // Verify CSRF
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid request');
            $this->redirect('admin/settings/email');
        }

        $storeId = Session::get('admin_store_id', 1);

        $emailSettings = [
            'smtp_host' => $_POST['smtp_host'] ?? '',
            'smtp_port' => $_POST['smtp_port'] ?? '587',
            'smtp_username' => $_POST['smtp_username'] ?? '',
            'smtp_password' => $_POST['smtp_password'] ?? '',
            'smtp_encryption' => $_POST['smtp_encryption'] ?? 'tls',
            'mail_from_name' => $_POST['mail_from_name'] ?? '',
            'mail_from_email' => $_POST['mail_from_email'] ?? ''
        ];

        foreach ($emailSettings as $key => $value) {
            $this->saveSetting($storeId, $key, $value);
        }

        Session::flash('success', 'Email settings updated successfully');
        $this->redirect('admin/settings/email');
    }

    private function getSettings($storeId)
    {
        $settings = [];
        $rows = $this->db->fetchAll(
            "SELECT setting_key, setting_value FROM settings WHERE store_id = ?",
            [$storeId]
        );

        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    }

    private function saveSetting($storeId, $key, $value)
    {
        $existing = $this->db->fetch(
            "SELECT id FROM settings WHERE store_id = ? AND setting_key = ?",
            [$storeId, $key]
        );

        if ($existing) {
            $this->db->query(
                "UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE id = ?",
                [$value, $existing['id']]
            );
        } else {
            $this->db->query(
                "INSERT INTO settings (store_id, setting_key, setting_value, created_at, updated_at)
                 VALUES (?, ?, ?, NOW(), NOW())",
                [$storeId, $key, $value]
            );
        }
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
