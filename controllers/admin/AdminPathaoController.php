<?php

class AdminPathaoController extends Controller
{
    private $pathaoService;

    public function __construct()
    {
        parent::__construct();

        // Check admin authentication
        if (!Auth::isAdmin()) {
            $this->redirect('admin/login');
        }

        requireFullAdmin(); // Only full admins can manage Pathao settings
    }

    public function index()
    {
        $storeId = Session::get('admin_store_id', 1);

        // Ensure store_id 1 exists if not set
        if (!$storeId) {
            $storeId = 1;
            Session::set('admin_store_id', 1);
        }

        $settings = $this->getPathaoSettings($storeId);

        // Debug: Check directly from database
        $dbCheck = $this->db->fetch(
            "SELECT * FROM settings WHERE setting_key = 'pathao_enabled' AND store_id = ?",
            [$storeId]
        );

        $this->view('admin/pathao/index', [
            'pageTitle' => 'Pathao Courier Settings',
            'settings' => $settings,
            'debug_store_id' => $storeId,
            'debug_db_check' => $dbCheck
        ], 'admin');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/pathao');
        }

        if (!Session::validateCsrf($_POST['csrf_token'] ?? '')) {
            Session::setFlash('Invalid request', 'error');
            $this->redirect('admin/pathao');
        }

        $storeId = Session::get('admin_store_id', 1);

        // Debug: Log what we're receiving
        $pathaoEnabled = isset($_POST['pathao_enabled']) ? '1' : '0';
        logMessage("Pathao update - store_id: {$storeId}, pathao_enabled: {$pathaoEnabled}", 'info');

        $settings = [
            'pathao_enabled' => $pathaoEnabled,
            'pathao_environment' => $_POST['pathao_environment'] ?? 'sandbox',
            'pathao_store_id' => $_POST['pathao_store_id'] ?? '',
            // Sandbox credentials
            'pathao_sandbox_client_id' => $_POST['pathao_sandbox_client_id'] ?? '7N1aMJQbWm',
            'pathao_sandbox_client_secret' => $_POST['pathao_sandbox_client_secret'] ?? 'wRcaibZkUdSNz2EI9ZyuXLlNrnAv0TdPUPXMnD39',
            'pathao_sandbox_username' => $_POST['pathao_sandbox_username'] ?? 'test@pathao.com',
            'pathao_sandbox_password' => $_POST['pathao_sandbox_password'] ?? 'lovePathao',
            // Production credentials
            'pathao_client_id' => $_POST['pathao_client_id'] ?? '',
            'pathao_client_secret' => $_POST['pathao_client_secret'] ?? '',
            'pathao_username' => $_POST['pathao_username'] ?? '',
            'pathao_password' => $_POST['pathao_password'] ?? '',
            // Auto-create order settings
            'pathao_auto_create' => isset($_POST['pathao_auto_create']) ? '1' : '0',
            'pathao_default_weight' => $_POST['pathao_default_weight'] ?? '0.5'
        ];

        foreach ($settings as $key => $value) {
            $this->saveSetting($storeId, $key, $value);
        }

        // Clear saved tokens when credentials change
        $this->saveSetting($storeId, 'pathao_access_token', '');
        $this->saveSetting($storeId, 'pathao_refresh_token', '');
        $this->saveSetting($storeId, 'pathao_token_expiry', '');

        Session::setFlash('Pathao settings updated successfully', 'success');
        $this->redirect('admin/pathao');
    }

    public function test()
    {
        $storeId = Session::get('admin_store_id', 1);
        $pathao = new PathaoService($storeId);

        $result = $pathao->testConnection();

        $this->json($result);
    }

    /**
     * Force enable Pathao (debug endpoint)
     */
    public function forceEnable()
    {
        $storeId = Session::get('admin_store_id', 1);
        if (!$storeId) $storeId = 1;

        try {
            // Check if setting exists
            $existing = $this->db->fetch(
                "SELECT id FROM settings WHERE store_id = ? AND setting_key = 'pathao_enabled'",
                [$storeId]
            );

            if ($existing) {
                $this->db->query(
                    "UPDATE settings SET setting_value = '1', updated_at = NOW() WHERE id = ?",
                    [$existing['id']]
                );
                $this->json(['success' => true, 'message' => 'Updated existing setting to enabled', 'id' => $existing['id']]);
            } else {
                $this->db->query(
                    "INSERT INTO settings (store_id, setting_key, setting_value, created_at, updated_at) VALUES (?, 'pathao_enabled', '1', NOW(), NOW())",
                    [$storeId]
                );
                $this->json(['success' => true, 'message' => 'Inserted new setting', 'store_id' => $storeId]);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function stores()
    {
        $storeId = Session::get('admin_store_id', 1);
        $pathao = new PathaoService($storeId);

        $stores = $pathao->getStores();

        $this->json([
            'success' => true,
            'stores' => $stores
        ]);
    }

    public function cities()
    {
        $storeId = Session::get('admin_store_id', 1);
        $pathao = new PathaoService($storeId);

        $cities = $pathao->getCities();

        $this->json([
            'success' => true,
            'cities' => $cities
        ]);
    }

    public function zones($cityId)
    {
        $storeId = Session::get('admin_store_id', 1);
        $pathao = new PathaoService($storeId);

        $zones = $pathao->getZones((int)$cityId);

        $this->json([
            'success' => true,
            'zones' => $zones
        ]);
    }

    public function areas($zoneId)
    {
        $storeId = Session::get('admin_store_id', 1);
        $pathao = new PathaoService($storeId);

        $areas = $pathao->getAreas((int)$zoneId);

        $this->json([
            'success' => true,
            'areas' => $areas
        ]);
    }

    private function getPathaoSettings($storeId): array
    {
        $settings = [];
        $rows = $this->db->fetchAll(
            "SELECT setting_key, setting_value FROM settings WHERE store_id = ? AND setting_key LIKE 'pathao_%'",
            [$storeId]
        );

        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        // Set defaults
        $defaults = [
            'pathao_enabled' => '0',
            'pathao_environment' => 'sandbox',
            'pathao_sandbox_client_id' => '7N1aMJQbWm',
            'pathao_sandbox_client_secret' => 'wRcaibZkUdSNz2EI9ZyuXLlNrnAv0TdPUPXMnD39',
            'pathao_sandbox_username' => 'test@pathao.com',
            'pathao_sandbox_password' => 'lovePathao',
            'pathao_auto_create' => '1',
            'pathao_default_weight' => '0.5'
        ];

        return array_merge($defaults, $settings);
    }

    private function saveSetting($storeId, $key, $value): void
    {
        try {
            $existing = $this->db->fetch(
                "SELECT id FROM settings WHERE store_id = ? AND setting_key = ?",
                [$storeId, $key]
            );

            if ($existing) {
                $this->db->query(
                    "UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE id = ?",
                    [$value, $existing['id']]
                );
                logMessage("Pathao saveSetting UPDATE - key: {$key}, value: {$value}, id: {$existing['id']}", 'info');
            } else {
                $this->db->query(
                    "INSERT INTO settings (store_id, setting_key, setting_value, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())",
                    [$storeId, $key, $value]
                );
                logMessage("Pathao saveSetting INSERT - store_id: {$storeId}, key: {$key}, value: {$value}", 'info');
            }
        } catch (Exception $e) {
            logMessage("Pathao saveSetting ERROR - key: {$key}, error: " . $e->getMessage(), 'error');
        }
    }
}
