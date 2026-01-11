<?php
/**
 * Admin Meta Integration Controller
 * Handle Facebook, Instagram & WhatsApp Business connections
 */

class AdminMetaController extends Controller
{
    private MetaIntegration $metaModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->metaModel = new MetaIntegration();
    }

    /**
     * Integration dashboard
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $integrations = $this->metaModel->getAllIntegrations($storeId);
        $summary = $this->metaModel->getInsightsSummary($storeId);

        // Get recent messages
        $recentMessages = $this->metaModel->getStoredMessages($storeId, null, null, 20);

        $this->view('admin/meta/index', [
            'pageTitle' => 'Meta Business Suite Integration',
            'integrations' => $integrations,
            'summary' => $summary,
            'recentMessages' => $recentMessages,
            'appConfigured' => defined('META_APP_ID') && defined('META_APP_SECRET')
        ], 'admin');
    }

    /**
     * Connect to Meta (Start OAuth flow)
     */
    public function connect(): void
    {
        if (!defined('META_APP_ID') || !defined('META_APP_SECRET')) {
            $this->redirect('admin/meta', 'Please configure Meta App ID and Secret first', 'error');
        }

        $redirectUri = SITE_URL . '/admin/meta/callback';
        $loginUrl = $this->metaModel->getLoginUrl($redirectUri);

        header('Location: ' . $loginUrl);
        exit;
    }

    /**
     * OAuth callback handler
     */
    public function callback(): void
    {
        $code = $this->get('code');
        $state = $this->get('state');
        $error = $this->get('error');

        if ($error) {
            $this->redirect('admin/meta', 'Authorization denied: ' . $this->get('error_description'), 'error');
        }

        // Verify state token
        if ($state !== Session::getCsrfToken()) {
            $this->redirect('admin/meta', 'Invalid state token', 'error');
        }

        $redirectUri = SITE_URL . '/admin/meta/callback';
        $tokenData = $this->metaModel->getAccessToken($code, $redirectUri);

        if (!$tokenData) {
            $this->redirect('admin/meta', 'Failed to get access token', 'error');
        }

        // Get long-lived token
        $longToken = $this->metaModel->getLongLivedToken($tokenData['access_token']);

        // Store in session temporarily
        Session::set('meta_access_token', $longToken ?? $tokenData['access_token']);

        $this->redirect('admin/meta/select-pages', 'Connected successfully! Now select your pages.', 'success');
    }

    /**
     * Select pages to connect
     */
    public function selectPages(): void
    {
        $accessToken = Session::get('meta_access_token');

        if (!$accessToken) {
            $this->redirect('admin/meta', 'Please connect to Meta first', 'error');
        }

        // Get user's pages
        $pages = $this->metaModel->getUserPages($accessToken);

        // Get WhatsApp Business accounts
        $waAccounts = $this->metaModel->getWhatsAppBusinessAccounts($accessToken);

        $this->view('admin/meta/select-pages', [
            'pageTitle' => 'Select Pages to Connect',
            'pages' => $pages,
            'waAccounts' => $waAccounts
        ], 'admin');
    }

    /**
     * Save selected pages
     */
    public function savePages(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/meta', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $accessToken = Session::get('meta_access_token');

        if (!$accessToken) {
            $this->redirect('admin/meta', 'Please connect to Meta first', 'error');
        }

        $pages = $this->metaModel->getUserPages($accessToken);
        $selectedPages = $this->post('pages', []);

        foreach ($pages as $page) {
            if (in_array($page['id'], $selectedPages)) {
                // Save Facebook page
                $this->metaModel->savePageIntegration($storeId, 'facebook', $page, $accessToken);

                // Check for Instagram account
                $igAccount = $this->metaModel->getInstagramAccount($page['id'], $page['access_token']);
                if ($igAccount) {
                    $igData = [
                        'id' => $igAccount['id'],
                        'name' => $igAccount['username'] ?? $igAccount['name'],
                        'access_token' => $page['access_token']
                    ];
                    $this->metaModel->savePageIntegration($storeId, 'instagram', $igData, $accessToken);
                }
            }
        }

        // Save WhatsApp if selected
        $selectedWA = $this->post('whatsapp');
        if ($selectedWA) {
            $waAccounts = $this->metaModel->getWhatsAppBusinessAccounts($accessToken);
            foreach ($waAccounts as $wa) {
                foreach ($wa['phone_numbers'] ?? [] as $phone) {
                    if ($phone['id'] === $selectedWA) {
                        $this->metaModel->saveWhatsAppIntegration($storeId, [
                            'phone_number_id' => $phone['id'],
                            'display_phone_number' => $phone['display_phone_number'] ?? $phone['id'],
                            'whatsapp_business_id' => $wa['whatsapp_id']
                        ], $accessToken);
                        break 2;
                    }
                }
            }
        }

        // Clear session token
        Session::remove('meta_access_token');

        $this->redirect('admin/meta', 'Pages connected successfully!', 'success');
    }

    /**
     * Disconnect a platform
     */
    public function disconnect(string $platform): void
    {
        if (!Session::validateCsrf($this->get('csrf_token'))) {
            $this->redirect('admin/meta', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        if ($this->metaModel->disconnect($storeId, $platform)) {
            $this->redirect('admin/meta', ucfirst($platform) . ' disconnected', 'success');
        } else {
            $this->redirect('admin/meta', 'Failed to disconnect', 'error');
        }
    }

    /**
     * View Facebook insights
     */
    public function facebookInsights(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $integration = $this->metaModel->getIntegration($storeId, 'facebook');

        if (!$integration || !$integration['is_active']) {
            $this->redirect('admin/meta', 'Facebook not connected', 'error');
        }

        // Sync insights
        $this->metaModel->syncPageInsights($storeId, 'facebook');

        // Get stored insights
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $insights = $this->metaModel->getStoredInsights($storeId, 'facebook', $startDate, $endDate);

        // Format for charts
        $chartData = $this->formatInsightsForChart($insights);

        $this->view('admin/meta/facebook-insights', [
            'pageTitle' => 'Facebook Page Insights',
            'integration' => $integration,
            'chartData' => $chartData,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'admin');
    }

    /**
     * View Instagram insights
     */
    public function instagramInsights(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $integration = $this->metaModel->getIntegration($storeId, 'instagram');

        if (!$integration || !$integration['is_active']) {
            $this->redirect('admin/meta', 'Instagram not connected', 'error');
        }

        // Sync insights
        $this->metaModel->syncPageInsights($storeId, 'instagram');

        // Get stored insights
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $insights = $this->metaModel->getStoredInsights($storeId, 'instagram', $startDate, $endDate);

        $chartData = $this->formatInsightsForChart($insights);

        $this->view('admin/meta/instagram-insights', [
            'pageTitle' => 'Instagram Insights',
            'integration' => $integration,
            'chartData' => $chartData,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'admin');
    }

    /**
     * Messages inbox
     */
    public function messages(?string $platform = null): void
    {
        $storeId = Session::get('admin_store_id', 1);

        // Get messages
        $messages = $this->metaModel->getStoredMessages($storeId, $platform, null, 100);

        // Group by sender
        $conversations = [];
        foreach ($messages as $msg) {
            $key = $msg['sender_id'] . '_' . $msg['platform'];
            if (!isset($conversations[$key])) {
                $conversations[$key] = [
                    'sender_id' => $msg['sender_id'],
                    'sender_name' => $msg['sender_name'],
                    'platform' => $msg['platform'],
                    'messages' => [],
                    'unread' => 0,
                    'last_message' => null
                ];
            }
            $conversations[$key]['messages'][] = $msg;
            if (!$msg['is_read']) {
                $conversations[$key]['unread']++;
            }
            if (!$conversations[$key]['last_message']) {
                $conversations[$key]['last_message'] = $msg;
            }
        }

        $this->view('admin/meta/messages', [
            'pageTitle' => 'Messages Inbox',
            'conversations' => array_values($conversations),
            'currentPlatform' => $platform,
            'integrations' => $this->metaModel->getAllIntegrations($storeId)
        ], 'admin');
    }

    /**
     * Send reply message
     */
    public function sendMessage(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $storeId = Session::get('admin_store_id', 1);
        $platform = $this->post('platform');
        $recipientId = $this->post('recipient_id');
        $message = trim($this->post('message'));

        if (empty($message)) {
            $this->json(['success' => false, 'message' => 'Message cannot be empty']);
        }

        $integration = $this->metaModel->getIntegration($storeId, $platform);

        if (!$integration || !$integration['is_active']) {
            $this->json(['success' => false, 'message' => 'Platform not connected']);
        }

        $token = $integration['page_access_token'] ?: $integration['user_access_token'];
        $result = null;

        if ($platform === 'whatsapp') {
            $result = $this->metaModel->sendWhatsAppMessage(
                $integration['phone_number_id'],
                $recipientId,
                $message,
                $token
            );
        } else {
            $result = $this->metaModel->sendMessage(
                $integration['page_id'],
                $recipientId,
                $message,
                $token
            );
        }

        if ($result && !isset($result['error'])) {
            // Store sent message
            $this->metaModel->storeMessage($storeId, $platform, [
                'id' => $result['message_id'] ?? uniqid('sent_'),
                'from' => ['id' => $integration['page_id']],
                'to' => ['data' => [['id' => $recipientId]]],
                'message' => $message,
                'is_incoming' => 0
            ]);

            $this->json(['success' => true, 'message' => 'Message sent']);
        } else {
            $this->json(['success' => false, 'message' => $result['error']['message'] ?? 'Failed to send message']);
        }
    }

    /**
     * Webhook endpoint for Meta
     */
    public function webhook(): void
    {
        // GET request = verification
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $mode = $this->get('hub_mode');
            $token = $this->get('hub_verify_token');
            $challenge = $this->get('hub_challenge');

            $result = $this->metaModel->verifyWebhook($mode, $token, $challenge);

            if ($result) {
                echo $result;
            } else {
                http_response_code(403);
                echo 'Forbidden';
            }
            exit;
        }

        // POST request = event
        $payload = json_decode(file_get_contents('php://input'), true);

        if ($payload) {
            $this->metaModel->processWebhook($payload);
        }

        http_response_code(200);
        echo 'EVENT_RECEIVED';
        exit;
    }

    /**
     * Sync insights manually
     */
    public function syncInsights(string $platform): void
    {
        $storeId = Session::get('admin_store_id', 1);

        if ($this->metaModel->syncPageInsights($storeId, $platform)) {
            $this->redirect("admin/meta/{$platform}-insights", 'Insights synced successfully', 'success');
        } else {
            $this->redirect("admin/meta/{$platform}-insights", 'Failed to sync insights', 'error');
        }
    }

    /**
     * Settings page
     */
    public function settings(): void
    {
        $this->view('admin/meta/settings', [
            'pageTitle' => 'Meta Integration Settings',
            'appId' => defined('META_APP_ID') ? META_APP_ID : '',
            'appSecret' => defined('META_APP_SECRET') ? '********' : '',
            'webhookUrl' => SITE_URL . '/admin/meta/webhook',
            'verifyToken' => defined('META_WEBHOOK_VERIFY_TOKEN') ? META_WEBHOOK_VERIFY_TOKEN : 'waslah_verify_token'
        ], 'admin');
    }

    /**
     * Format insights data for charts
     */
    private function formatInsightsForChart(array $insights): array
    {
        $formatted = [];

        foreach ($insights as $row) {
            $date = $row['stat_date'];
            $metric = $row['metric_name'];
            $value = (float) $row['metric_value'];

            if (!isset($formatted[$metric])) {
                $formatted[$metric] = [];
            }

            $formatted[$metric][$date] = $value;
        }

        return $formatted;
    }
}
