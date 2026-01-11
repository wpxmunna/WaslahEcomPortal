<?php
/**
 * Meta Integration Model
 * Handle Facebook Pages, Instagram & WhatsApp Business API
 *
 * Setup Requirements:
 * 1. Create app at https://developers.facebook.com
 * 2. Add Facebook Login, Pages API, WhatsApp Business API products
 * 3. Configure OAuth redirect URL
 * 4. Get App ID and App Secret
 */

class MetaIntegration
{
    private Database $db;
    private string $graphApiUrl = 'https://graph.facebook.com/v18.0';
    private ?string $appId;
    private ?string $appSecret;

    public function __construct()
    {
        $this->db = new Database();
        $this->appId = defined('META_APP_ID') ? META_APP_ID : null;
        $this->appSecret = defined('META_APP_SECRET') ? META_APP_SECRET : null;
    }

    // ==========================================
    // OAuth & Authentication
    // ==========================================

    /**
     * Get OAuth login URL
     */
    public function getLoginUrl(string $redirectUri, array $scopes = []): string
    {
        $defaultScopes = [
            'pages_show_list',
            'pages_read_engagement',
            'pages_messaging',
            'pages_manage_metadata',
            'pages_read_user_content',
            'instagram_basic',
            'instagram_manage_messages',
            'instagram_manage_insights',
            'whatsapp_business_management',
            'whatsapp_business_messaging'
        ];

        $scopes = array_merge($defaultScopes, $scopes);

        return 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $redirectUri,
            'scope' => implode(',', $scopes),
            'response_type' => 'code',
            'state' => Session::getCsrfToken()
        ]);
    }

    /**
     * Exchange code for access token
     */
    public function getAccessToken(string $code, string $redirectUri): ?array
    {
        $response = $this->apiRequest('/oauth/access_token', [
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri' => $redirectUri,
            'code' => $code
        ], 'GET');

        if (isset($response['access_token'])) {
            return [
                'access_token' => $response['access_token'],
                'token_type' => $response['token_type'] ?? 'bearer',
                'expires_in' => $response['expires_in'] ?? null
            ];
        }

        return null;
    }

    /**
     * Get long-lived token (60 days)
     */
    public function getLongLivedToken(string $shortLivedToken): ?string
    {
        $response = $this->apiRequest('/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'fb_exchange_token' => $shortLivedToken
        ], 'GET');

        return $response['access_token'] ?? null;
    }

    // ==========================================
    // Facebook Pages
    // ==========================================

    /**
     * Get user's Facebook pages
     */
    public function getUserPages(string $accessToken): array
    {
        $response = $this->apiRequest('/me/accounts', [
            'access_token' => $accessToken,
            'fields' => 'id,name,access_token,category,picture,fan_count,followers_count'
        ], 'GET');

        return $response['data'] ?? [];
    }

    /**
     * Save page integration
     */
    public function savePageIntegration(int $storeId, string $platform, array $pageData, string $userToken): int|false
    {
        // Check if exists
        $existing = $this->getIntegration($storeId, $platform);

        $data = [
            'page_id' => $pageData['id'],
            'page_name' => $pageData['name'],
            'page_access_token' => $pageData['access_token'],
            'user_access_token' => $userToken,
            'token_expires_at' => date('Y-m-d H:i:s', strtotime('+60 days')),
            'is_active' => 1
        ];

        if ($existing) {
            $this->db->execute(
                "UPDATE meta_integrations SET
                    page_id = ?, page_name = ?, page_access_token = ?,
                    user_access_token = ?, token_expires_at = ?, is_active = 1, updated_at = NOW()
                 WHERE store_id = ? AND platform = ?",
                [$data['page_id'], $data['page_name'], $data['page_access_token'],
                 $data['user_access_token'], $data['token_expires_at'], $storeId, $platform]
            );
            return $existing['id'];
        }

        return $this->db->insert(
            "INSERT INTO meta_integrations (store_id, platform, page_id, page_name, page_access_token, user_access_token, token_expires_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$storeId, $platform, $data['page_id'], $data['page_name'],
             $data['page_access_token'], $data['user_access_token'], $data['token_expires_at']]
        );
    }

    /**
     * Get integration settings
     */
    public function getIntegration(int $storeId, string $platform): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM meta_integrations WHERE store_id = ? AND platform = ?",
            [$storeId, $platform]
        );
    }

    /**
     * Get all integrations for store
     */
    public function getAllIntegrations(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM meta_integrations WHERE store_id = ? ORDER BY platform",
            [$storeId]
        );
    }

    // ==========================================
    // Page Insights
    // ==========================================

    /**
     * Get Facebook page insights
     */
    public function getPageInsights(string $pageId, string $accessToken, array $metrics = [], string $period = 'day'): array
    {
        $defaultMetrics = [
            'page_impressions',
            'page_impressions_unique',
            'page_engaged_users',
            'page_post_engagements',
            'page_fans',
            'page_fan_adds',
            'page_views_total',
            'page_actions_post_reactions_total'
        ];

        $metrics = !empty($metrics) ? $metrics : $defaultMetrics;

        $response = $this->apiRequest("/{$pageId}/insights", [
            'access_token' => $accessToken,
            'metric' => implode(',', $metrics),
            'period' => $period,
            'date_preset' => 'last_30d'
        ], 'GET');

        return $response['data'] ?? [];
    }

    /**
     * Sync and store page insights
     */
    public function syncPageInsights(int $storeId, string $platform): bool
    {
        $integration = $this->getIntegration($storeId, $platform);
        if (!$integration || !$integration['page_access_token']) {
            return false;
        }

        $insights = $this->getPageInsights(
            $integration['page_id'],
            $integration['page_access_token']
        );

        foreach ($insights as $metric) {
            foreach ($metric['values'] ?? [] as $value) {
                $date = date('Y-m-d', strtotime($value['end_time']));
                $metricValue = is_array($value['value']) ? array_sum($value['value']) : $value['value'];

                $this->db->execute(
                    "INSERT INTO meta_page_insights (store_id, platform, page_id, metric_name, metric_value, period, stat_date, metadata)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE metric_value = ?, metadata = ?",
                    [
                        $storeId, $platform, $integration['page_id'],
                        $metric['name'], $metricValue, $metric['period'] ?? 'day', $date,
                        json_encode($value), $metricValue, json_encode($value)
                    ]
                );
            }
        }

        // Update last sync time
        $this->db->execute(
            "UPDATE meta_integrations SET last_sync_at = NOW() WHERE id = ?",
            [$integration['id']]
        );

        return true;
    }

    /**
     * Get stored insights
     */
    public function getStoredInsights(int $storeId, string $platform, string $startDate, string $endDate): array
    {
        return $this->db->fetchAll(
            "SELECT metric_name, stat_date, metric_value
             FROM meta_page_insights
             WHERE store_id = ? AND platform = ? AND stat_date BETWEEN ? AND ?
             ORDER BY stat_date, metric_name",
            [$storeId, $platform, $startDate, $endDate]
        );
    }

    // ==========================================
    // Messaging (Facebook & Instagram)
    // ==========================================

    /**
     * Get page conversations
     */
    public function getConversations(string $pageId, string $accessToken, int $limit = 25): array
    {
        $response = $this->apiRequest("/{$pageId}/conversations", [
            'access_token' => $accessToken,
            'fields' => 'id,link,message_count,unread_count,updated_time,participants,messages{id,message,from,to,created_time}',
            'limit' => $limit
        ], 'GET');

        return $response['data'] ?? [];
    }

    /**
     * Get conversation messages
     */
    public function getMessages(string $conversationId, string $accessToken): array
    {
        $response = $this->apiRequest("/{$conversationId}/messages", [
            'access_token' => $accessToken,
            'fields' => 'id,message,from,to,created_time,attachments,sticker'
        ], 'GET');

        return $response['data'] ?? [];
    }

    /**
     * Send message to user
     */
    public function sendMessage(string $pageId, string $recipientId, string $message, string $accessToken): ?array
    {
        return $this->apiRequest("/{$pageId}/messages", [
            'access_token' => $accessToken,
            'recipient' => json_encode(['id' => $recipientId]),
            'message' => json_encode(['text' => $message]),
            'messaging_type' => 'RESPONSE'
        ], 'POST');
    }

    /**
     * Store message in database
     */
    public function storeMessage(int $storeId, string $platform, array $messageData): int|false
    {
        return $this->db->insert(
            "INSERT INTO meta_messages (store_id, platform, message_id, conversation_id, sender_id, sender_name, recipient_id, message_type, content, is_incoming, metadata)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE content = VALUES(content)",
            [
                $storeId,
                $platform,
                $messageData['id'],
                $messageData['conversation_id'] ?? null,
                $messageData['from']['id'] ?? null,
                $messageData['from']['name'] ?? null,
                $messageData['to']['data'][0]['id'] ?? null,
                $messageData['type'] ?? 'text',
                $messageData['message'] ?? null,
                $messageData['is_incoming'] ?? 1,
                json_encode($messageData)
            ]
        );
    }

    /**
     * Get stored messages
     */
    public function getStoredMessages(int $storeId, ?string $platform = null, ?string $senderId = null, int $limit = 50): array
    {
        $sql = "SELECT * FROM meta_messages WHERE store_id = ?";
        $params = [$storeId];

        if ($platform) {
            $sql .= " AND platform = ?";
            $params[] = $platform;
        }

        if ($senderId) {
            $sql .= " AND sender_id = ?";
            $params[] = $senderId;
        }

        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;

        return $this->db->fetchAll($sql, $params);
    }

    // ==========================================
    // WhatsApp Business API
    // ==========================================

    /**
     * Get WhatsApp Business accounts
     */
    public function getWhatsAppBusinessAccounts(string $accessToken): array
    {
        $response = $this->apiRequest('/me/businesses', [
            'access_token' => $accessToken,
            'fields' => 'id,name,owned_whatsapp_business_accounts{id,name,phone_numbers}'
        ], 'GET');

        $accounts = [];
        foreach ($response['data'] ?? [] as $business) {
            foreach ($business['owned_whatsapp_business_accounts']['data'] ?? [] as $wa) {
                $accounts[] = [
                    'business_id' => $business['id'],
                    'business_name' => $business['name'],
                    'whatsapp_id' => $wa['id'],
                    'whatsapp_name' => $wa['name'],
                    'phone_numbers' => $wa['phone_numbers']['data'] ?? []
                ];
            }
        }

        return $accounts;
    }

    /**
     * Save WhatsApp integration
     */
    public function saveWhatsAppIntegration(int $storeId, array $waData, string $userToken): int|false
    {
        $existing = $this->getIntegration($storeId, 'whatsapp');

        $data = [
            'page_id' => $waData['phone_number_id'],
            'page_name' => $waData['display_phone_number'],
            'whatsapp_business_id' => $waData['whatsapp_business_id'],
            'phone_number_id' => $waData['phone_number_id'],
            'user_access_token' => $userToken
        ];

        if ($existing) {
            $this->db->execute(
                "UPDATE meta_integrations SET
                    page_id = ?, page_name = ?, whatsapp_business_id = ?,
                    phone_number_id = ?, user_access_token = ?, is_active = 1, updated_at = NOW()
                 WHERE store_id = ? AND platform = 'whatsapp'",
                [$data['page_id'], $data['page_name'], $data['whatsapp_business_id'],
                 $data['phone_number_id'], $data['user_access_token'], $storeId]
            );
            return $existing['id'];
        }

        return $this->db->insert(
            "INSERT INTO meta_integrations (store_id, platform, page_id, page_name, whatsapp_business_id, phone_number_id, user_access_token)
             VALUES (?, 'whatsapp', ?, ?, ?, ?, ?)",
            [$storeId, $data['page_id'], $data['page_name'],
             $data['whatsapp_business_id'], $data['phone_number_id'], $data['user_access_token']]
        );
    }

    /**
     * Send WhatsApp message
     */
    public function sendWhatsAppMessage(string $phoneNumberId, string $to, string $message, string $accessToken): ?array
    {
        return $this->apiRequest("/{$phoneNumberId}/messages", [
            'access_token' => $accessToken,
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => json_encode(['body' => $message])
        ], 'POST');
    }

    /**
     * Send WhatsApp template message
     */
    public function sendWhatsAppTemplate(string $phoneNumberId, string $to, string $templateName, string $language, array $components, string $accessToken): ?array
    {
        return $this->apiRequest("/{$phoneNumberId}/messages", [
            'access_token' => $accessToken,
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => json_encode([
                'name' => $templateName,
                'language' => ['code' => $language],
                'components' => $components
            ])
        ], 'POST');
    }

    /**
     * Get WhatsApp message templates
     */
    public function getWhatsAppTemplates(string $waBusinessId, string $accessToken): array
    {
        $response = $this->apiRequest("/{$waBusinessId}/message_templates", [
            'access_token' => $accessToken,
            'fields' => 'id,name,status,category,language,components'
        ], 'GET');

        return $response['data'] ?? [];
    }

    // ==========================================
    // Instagram
    // ==========================================

    /**
     * Get Instagram business account connected to FB page
     */
    public function getInstagramAccount(string $pageId, string $accessToken): ?array
    {
        $response = $this->apiRequest("/{$pageId}", [
            'access_token' => $accessToken,
            'fields' => 'instagram_business_account{id,name,username,profile_picture_url,followers_count,follows_count,media_count}'
        ], 'GET');

        return $response['instagram_business_account'] ?? null;
    }

    /**
     * Get Instagram insights
     */
    public function getInstagramInsights(string $igAccountId, string $accessToken, array $metrics = []): array
    {
        $defaultMetrics = ['impressions', 'reach', 'profile_views', 'follower_count'];
        $metrics = !empty($metrics) ? $metrics : $defaultMetrics;

        $response = $this->apiRequest("/{$igAccountId}/insights", [
            'access_token' => $accessToken,
            'metric' => implode(',', $metrics),
            'period' => 'day',
            'since' => strtotime('-30 days'),
            'until' => time()
        ], 'GET');

        return $response['data'] ?? [];
    }

    // ==========================================
    // Webhook Handling
    // ==========================================

    /**
     * Verify webhook subscription
     */
    public function verifyWebhook(string $mode, string $token, string $challenge): ?string
    {
        $verifyToken = defined('META_WEBHOOK_VERIFY_TOKEN') ? META_WEBHOOK_VERIFY_TOKEN : 'waslah_verify_token';

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return $challenge;
        }

        return null;
    }

    /**
     * Process webhook payload
     */
    public function processWebhook(array $payload): void
    {
        $object = $payload['object'] ?? '';

        foreach ($payload['entry'] ?? [] as $entry) {
            $pageId = $entry['id'];

            // Find store by page_id
            $integration = $this->db->fetch(
                "SELECT * FROM meta_integrations WHERE page_id = ? OR phone_number_id = ?",
                [$pageId, $pageId]
            );

            if (!$integration) continue;

            // Handle different event types
            if ($object === 'page') {
                $this->handlePageWebhook($integration, $entry);
            } elseif ($object === 'instagram') {
                $this->handleInstagramWebhook($integration, $entry);
            } elseif ($object === 'whatsapp_business_account') {
                $this->handleWhatsAppWebhook($integration, $entry);
            }
        }
    }

    /**
     * Handle Facebook page webhook events
     */
    private function handlePageWebhook(array $integration, array $entry): void
    {
        foreach ($entry['messaging'] ?? [] as $event) {
            if (isset($event['message'])) {
                $this->storeMessage($integration['store_id'], 'facebook', [
                    'id' => $event['message']['mid'],
                    'from' => ['id' => $event['sender']['id']],
                    'to' => ['data' => [['id' => $event['recipient']['id']]]],
                    'message' => $event['message']['text'] ?? null,
                    'type' => isset($event['message']['attachments']) ? 'attachment' : 'text',
                    'is_incoming' => 1
                ]);
            }
        }
    }

    /**
     * Handle Instagram webhook events
     */
    private function handleInstagramWebhook(array $integration, array $entry): void
    {
        foreach ($entry['messaging'] ?? [] as $event) {
            if (isset($event['message'])) {
                $this->storeMessage($integration['store_id'], 'instagram', [
                    'id' => $event['message']['mid'],
                    'from' => ['id' => $event['sender']['id']],
                    'to' => ['data' => [['id' => $event['recipient']['id']]]],
                    'message' => $event['message']['text'] ?? null,
                    'is_incoming' => 1
                ]);
            }
        }
    }

    /**
     * Handle WhatsApp webhook events
     */
    private function handleWhatsAppWebhook(array $integration, array $entry): void
    {
        foreach ($entry['changes'] ?? [] as $change) {
            if ($change['field'] === 'messages') {
                foreach ($change['value']['messages'] ?? [] as $message) {
                    $this->storeMessage($integration['store_id'], 'whatsapp', [
                        'id' => $message['id'],
                        'from' => ['id' => $message['from']],
                        'message' => $message['text']['body'] ?? null,
                        'type' => $message['type'] ?? 'text',
                        'is_incoming' => 1
                    ]);
                }
            }
        }
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    /**
     * Make API request to Meta Graph API
     */
    private function apiRequest(string $endpoint, array $params = [], string $method = 'GET'): ?array
    {
        $url = $this->graphApiUrl . $endpoint;

        $ch = curl_init();

        if ($method === 'GET') {
            $url .= '?' . http_build_query($params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("Meta API Error: " . $error);
            return null;
        }

        return json_decode($response, true);
    }

    /**
     * Disconnect integration
     */
    public function disconnect(int $storeId, string $platform): bool
    {
        return $this->db->execute(
            "UPDATE meta_integrations SET is_active = 0 WHERE store_id = ? AND platform = ?",
            [$storeId, $platform]
        );
    }

    /**
     * Get insights summary for dashboard
     */
    public function getInsightsSummary(int $storeId): array
    {
        $integrations = $this->getAllIntegrations($storeId);

        $summary = [
            'facebook' => null,
            'instagram' => null,
            'whatsapp' => null,
            'total_messages' => 0,
            'unread_messages' => 0
        ];

        foreach ($integrations as $int) {
            $summary[$int['platform']] = [
                'connected' => (bool) $int['is_active'],
                'page_name' => $int['page_name'],
                'last_sync' => $int['last_sync_at']
            ];
        }

        // Get message counts
        $msgStats = $this->db->fetch(
            "SELECT COUNT(*) as total, SUM(is_read = 0) as unread FROM meta_messages WHERE store_id = ?",
            [$storeId]
        );

        $summary['total_messages'] = (int) ($msgStats['total'] ?? 0);
        $summary['unread_messages'] = (int) ($msgStats['unread'] ?? 0);

        return $summary;
    }
}
