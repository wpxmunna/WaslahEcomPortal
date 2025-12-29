<?php
/**
 * Pathao Courier API Service
 * Handles all interactions with Pathao Merchant API
 */

class PathaoService
{
    private $db;
    private $storeId;
    private $baseUrl;
    private $clientId;
    private $clientSecret;
    private $username;
    private $password;
    private $accessToken;
    private $refreshToken;
    private $tokenExpiry;
    private $pathaoStoreId;
    private $isSandbox;

    public function __construct(int $storeId = 1)
    {
        $this->db = new Database();
        $this->storeId = $storeId;
        $this->loadSettings();
    }

    /**
     * Load Pathao settings from database
     */
    private function loadSettings(): void
    {
        $settings = $this->db->fetchAll(
            "SELECT setting_key, setting_value FROM settings WHERE store_id = ? AND setting_key LIKE 'pathao_%'",
            [$this->storeId]
        );

        $config = [];
        foreach ($settings as $setting) {
            $config[$setting['setting_key']] = $setting['setting_value'];
        }

        $this->isSandbox = ($config['pathao_environment'] ?? 'sandbox') === 'sandbox';

        if ($this->isSandbox) {
            $this->baseUrl = 'https://courier-api-sandbox.pathao.com';
            $this->clientId = $config['pathao_sandbox_client_id'] ?? '7N1aMJQbWm';
            $this->clientSecret = $config['pathao_sandbox_client_secret'] ?? 'wRcaibZkUdSNz2EI9ZyuXLlNrnAv0TdPUPXMnD39';
            $this->username = $config['pathao_sandbox_username'] ?? 'test@pathao.com';
            $this->password = $config['pathao_sandbox_password'] ?? 'lovePathao';
        } else {
            $this->baseUrl = 'https://api-hermes.pathao.com';
            $this->clientId = $config['pathao_client_id'] ?? '';
            $this->clientSecret = $config['pathao_client_secret'] ?? '';
            $this->username = $config['pathao_username'] ?? '';
            $this->password = $config['pathao_password'] ?? '';
        }

        $this->pathaoStoreId = $config['pathao_store_id'] ?? null;
        $this->accessToken = $config['pathao_access_token'] ?? null;
        $this->refreshToken = $config['pathao_refresh_token'] ?? null;
        $this->tokenExpiry = $config['pathao_token_expiry'] ?? null;
    }

    /**
     * Check if Pathao is enabled and configured
     */
    public function isEnabled(): bool
    {
        $enabled = $this->db->fetch(
            "SELECT setting_value FROM settings WHERE store_id = ? AND setting_key = 'pathao_enabled'",
            [$this->storeId]
        );

        return ($enabled['setting_value'] ?? '0') === '1';
    }

    /**
     * Save setting to database
     */
    private function saveSetting(string $key, string $value): void
    {
        $existing = $this->db->fetch(
            "SELECT id FROM settings WHERE store_id = ? AND setting_key = ?",
            [$this->storeId, $key]
        );

        if ($existing) {
            $this->db->query(
                "UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE id = ?",
                [$value, $existing['id']]
            );
        } else {
            $this->db->query(
                "INSERT INTO settings (store_id, setting_key, setting_value, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())",
                [$this->storeId, $key, $value]
            );
        }
    }

    /**
     * Get valid access token (refresh if needed)
     */
    public function getAccessToken(): ?string
    {
        // Check if token exists and is still valid
        if ($this->accessToken && $this->tokenExpiry) {
            $expiryTime = strtotime($this->tokenExpiry);
            if ($expiryTime > time() + 300) { // 5 min buffer
                return $this->accessToken;
            }
        }

        // Try to refresh token
        if ($this->refreshToken) {
            $result = $this->refreshAccessToken();
            if ($result) {
                return $this->accessToken;
            }
        }

        // Issue new token
        $result = $this->issueToken();
        if ($result) {
            return $this->accessToken;
        }

        return null;
    }

    /**
     * Issue new access token
     */
    public function issueToken(): bool
    {
        $response = $this->makeRequest('POST', '/aladdin/api/v1/issue-token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'password',
            'username' => $this->username,
            'password' => $this->password
        ], false);

        if ($response && isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            $this->refreshToken = $response['refresh_token'];
            $this->tokenExpiry = date('Y-m-d H:i:s', time() + ($response['expires_in'] ?? 432000));

            // Save tokens to database
            $this->saveSetting('pathao_access_token', $this->accessToken);
            $this->saveSetting('pathao_refresh_token', $this->refreshToken);
            $this->saveSetting('pathao_token_expiry', $this->tokenExpiry);

            return true;
        }

        return false;
    }

    /**
     * Refresh access token
     */
    public function refreshAccessToken(): bool
    {
        $response = $this->makeRequest('POST', '/aladdin/api/v1/issue-token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refreshToken
        ], false);

        if ($response && isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            $this->refreshToken = $response['refresh_token'];
            $this->tokenExpiry = date('Y-m-d H:i:s', time() + ($response['expires_in'] ?? 432000));

            // Save tokens to database
            $this->saveSetting('pathao_access_token', $this->accessToken);
            $this->saveSetting('pathao_refresh_token', $this->refreshToken);
            $this->saveSetting('pathao_token_expiry', $this->tokenExpiry);

            return true;
        }

        return false;
    }

    /**
     * Create order in Pathao
     */
    public function createOrder(array $orderData): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Failed to get access token'];
        }

        if (!$this->pathaoStoreId) {
            return ['success' => false, 'error' => 'Pathao store ID not configured'];
        }

        // Prepare order payload
        $payload = [
            'store_id' => (int) $this->pathaoStoreId,
            'merchant_order_id' => $orderData['order_number'] ?? '',
            'recipient_name' => $orderData['recipient_name'] ?? '',
            'recipient_phone' => $this->formatPhone($orderData['recipient_phone'] ?? ''),
            'recipient_address' => $orderData['recipient_address'] ?? '',
            'delivery_type' => 48, // Normal Delivery
            'item_type' => 2, // Parcel
            'item_quantity' => $orderData['item_quantity'] ?? 1,
            'item_weight' => $orderData['item_weight'] ?? 0.5,
            'amount_to_collect' => $orderData['amount_to_collect'] ?? 0,
            'item_description' => $orderData['item_description'] ?? '',
            'special_instruction' => $orderData['special_instruction'] ?? ''
        ];

        // Add optional city/zone/area if provided
        if (!empty($orderData['recipient_city'])) {
            $payload['recipient_city'] = (int) $orderData['recipient_city'];
        }
        if (!empty($orderData['recipient_zone'])) {
            $payload['recipient_zone'] = (int) $orderData['recipient_zone'];
        }
        if (!empty($orderData['recipient_area'])) {
            $payload['recipient_area'] = (int) $orderData['recipient_area'];
        }

        $response = $this->makeRequest('POST', '/aladdin/api/v1/orders', $payload, true);

        if ($response && isset($response['data']['consignment_id'])) {
            return [
                'success' => true,
                'consignment_id' => $response['data']['consignment_id'],
                'merchant_order_id' => $response['data']['merchant_order_id'] ?? '',
                'order_status' => $response['data']['order_status'] ?? 'Pending',
                'delivery_fee' => $response['data']['delivery_fee'] ?? 0
            ];
        }

        // Extract detailed error message
        $errorMsg = 'Failed to create Pathao order';
        if ($response) {
            if (isset($response['message'])) {
                $errorMsg = $response['message'];
            }
            if (isset($response['errors']) && is_array($response['errors'])) {
                $errorDetails = [];
                foreach ($response['errors'] as $field => $errors) {
                    $errorDetails[] = $field . ': ' . (is_array($errors) ? implode(', ', $errors) : $errors);
                }
                if (!empty($errorDetails)) {
                    $errorMsg .= ' - ' . implode('; ', $errorDetails);
                }
            }
        }

        logMessage("Pathao createOrder failed: " . $errorMsg . " | Payload: " . json_encode($payload), 'error');

        return [
            'success' => false,
            'error' => $errorMsg
        ];
    }

    /**
     * Get order info from Pathao
     */
    public function getOrderInfo(string $consignmentId): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Failed to get access token'];
        }

        $response = $this->makeRequest('GET', "/aladdin/api/v1/orders/{$consignmentId}/info", [], true);

        if ($response && isset($response['data'])) {
            return [
                'success' => true,
                'data' => $response['data']
            ];
        }

        return [
            'success' => false,
            'error' => $response['message'] ?? 'Failed to get order info'
        ];
    }

    /**
     * Get list of cities
     */
    public function getCities(): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [];
        }

        $response = $this->makeRequest('GET', '/aladdin/api/v1/city-list', [], true);

        if ($response && isset($response['data']['data'])) {
            return $response['data']['data'];
        }

        return [];
    }

    /**
     * Get zones for a city
     */
    public function getZones(int $cityId): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [];
        }

        $response = $this->makeRequest('GET', "/aladdin/api/v1/cities/{$cityId}/zone-list", [], true);

        if ($response && isset($response['data']['data'])) {
            return $response['data']['data'];
        }

        return [];
    }

    /**
     * Get areas for a zone
     */
    public function getAreas(int $zoneId): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [];
        }

        $response = $this->makeRequest('GET', "/aladdin/api/v1/zones/{$zoneId}/area-list", [], true);

        if ($response && isset($response['data']['data'])) {
            return $response['data']['data'];
        }

        return [];
    }

    /**
     * Get merchant stores from Pathao
     */
    public function getStores(): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [];
        }

        $response = $this->makeRequest('GET', '/aladdin/api/v1/stores', [], true);

        if ($response && isset($response['data']['data'])) {
            return $response['data']['data'];
        }

        return [];
    }

    /**
     * Calculate delivery price
     */
    public function calculatePrice(int $cityId, int $zoneId, float $weight = 0.5): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Failed to get access token'];
        }

        if (!$this->pathaoStoreId) {
            return ['success' => false, 'error' => 'Pathao store ID not configured'];
        }

        $payload = [
            'store_id' => (int) $this->pathaoStoreId,
            'item_type' => 2,
            'delivery_type' => 48,
            'item_weight' => $weight,
            'recipient_city' => $cityId,
            'recipient_zone' => $zoneId
        ];

        $response = $this->makeRequest('POST', '/aladdin/api/v1/merchant/price-plan', $payload, true);

        if ($response && isset($response['data'])) {
            return [
                'success' => true,
                'price' => $response['data']['final_price'] ?? 0,
                'cod_percentage' => $response['data']['cod_percentage'] ?? 0
            ];
        }

        return [
            'success' => false,
            'error' => $response['message'] ?? 'Failed to calculate price'
        ];
    }

    /**
     * Make HTTP request to Pathao API
     */
    private function makeRequest(string $method, string $endpoint, array $data = [], bool $auth = true): ?array
    {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if ($auth && $this->accessToken) {
            $headers[] = 'Authorization: Bearer ' . $this->accessToken;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            logMessage("Pathao API Error: {$error}", 'error');
            return null;
        }

        $result = json_decode($response, true);

        // Log API calls for debugging
        logMessage("Pathao API [{$method}] {$endpoint} - HTTP {$httpCode}", 'info');

        if ($httpCode >= 200 && $httpCode < 300) {
            return $result;
        }

        logMessage("Pathao API Error Response: " . $response, 'error');
        return $result;
    }

    /**
     * Format phone number to 11 digits
     */
    private function formatPhone(string $phone): string
    {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 880, remove it
        if (strpos($phone, '880') === 0) {
            $phone = '0' . substr($phone, 3);
        }

        // If doesn't start with 0, add it
        if (strpos($phone, '0') !== 0) {
            $phone = '0' . $phone;
        }

        // Ensure 11 digits
        return substr($phone, 0, 11);
    }

    /**
     * Get current environment
     */
    public function getEnvironment(): string
    {
        return $this->isSandbox ? 'sandbox' : 'production';
    }

    /**
     * Test connection to Pathao API
     */
    public function testConnection(): array
    {
        $token = $this->getAccessToken();

        if ($token) {
            return [
                'success' => true,
                'message' => 'Successfully connected to Pathao API',
                'environment' => $this->getEnvironment()
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to connect to Pathao API. Please check your credentials.'
        ];
    }
}
