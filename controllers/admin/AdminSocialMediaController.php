<?php
/**
 * Admin Social Media Controller
 * Manage social media links
 */

class AdminSocialMediaController extends Controller
{
    private SocialMedia $socialMediaModel;
    private CampaignMessage $campaignModel;
    private CampaignAnalytics $analyticsModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->socialMediaModel = new SocialMedia();
        $this->campaignModel = new CampaignMessage();
        $this->analyticsModel = new CampaignAnalytics();
    }

    /**
     * List all social media links
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $socialLinks = $this->socialMediaModel->getAllForStore($storeId);

        $this->view('admin/social-media/index', [
            'pageTitle' => 'Social Media Manager',
            'socialLinks' => $socialLinks,
            'presets' => SocialMedia::getPlatformPresets()
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->view('admin/social-media/create', [
            'pageTitle' => 'Add Social Media Link',
            'presets' => SocialMedia::getPlatformPresets()
        ], 'admin');
    }

    /**
     * Store new social media link
     */
    public function store(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/social-media', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'platform' => trim($this->post('platform')),
            'name' => trim($this->post('name')),
            'url' => trim($this->post('url')),
            'icon' => trim($this->post('icon')),
            'icon_style' => $this->post('icon_style', 'brands'),
            'color' => $this->post('color', '#000000'),
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0,
            'show_in_header' => $this->post('show_in_header') ? 1 : 0,
            'show_in_footer' => $this->post('show_in_footer') ? 1 : 0,
            'open_new_tab' => $this->post('open_new_tab') ? 1 : 0
        ];

        if (empty($data['name']) || empty($data['url']) || empty($data['icon'])) {
            $this->redirect('admin/social-media/create', 'Please fill in all required fields', 'error');
        }

        $id = $this->socialMediaModel->create($data);

        if ($id) {
            $this->redirect('admin/social-media', 'Social media link added successfully', 'success');
        } else {
            $this->redirect('admin/social-media/create', 'Failed to add social media link', 'error');
        }
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $socialLink = $this->socialMediaModel->getById($id);

        if (!$socialLink) {
            $this->redirect('admin/social-media', 'Social media link not found', 'error');
        }

        $this->view('admin/social-media/edit', [
            'pageTitle' => 'Edit Social Media Link',
            'socialLink' => $socialLink,
            'presets' => SocialMedia::getPlatformPresets()
        ], 'admin');
    }

    /**
     * Update social media link
     */
    public function update(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/social-media', 'Invalid request', 'error');
        }

        $socialLink = $this->socialMediaModel->getById($id);
        if (!$socialLink) {
            $this->redirect('admin/social-media', 'Social media link not found', 'error');
        }

        $data = [
            'platform' => trim($this->post('platform')),
            'name' => trim($this->post('name')),
            'url' => trim($this->post('url')),
            'icon' => trim($this->post('icon')),
            'icon_style' => $this->post('icon_style', 'brands'),
            'color' => $this->post('color', '#000000'),
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0,
            'show_in_header' => $this->post('show_in_header') ? 1 : 0,
            'show_in_footer' => $this->post('show_in_footer') ? 1 : 0,
            'open_new_tab' => $this->post('open_new_tab') ? 1 : 0
        ];

        if (empty($data['name']) || empty($data['url']) || empty($data['icon'])) {
            $this->redirect("admin/social-media/edit/{$id}", 'Please fill in all required fields', 'error');
        }

        if ($this->socialMediaModel->update($id, $data)) {
            $this->redirect('admin/social-media', 'Social media link updated successfully', 'success');
        } else {
            $this->redirect("admin/social-media/edit/{$id}", 'Failed to update social media link', 'error');
        }
    }

    /**
     * Delete social media link
     */
    public function delete(int $id): void
    {
        if (!Session::validateCsrf($this->get('csrf_token'))) {
            $this->redirect('admin/social-media', 'Invalid request', 'error');
        }

        if ($this->socialMediaModel->delete($id)) {
            $this->redirect('admin/social-media', 'Social media link deleted successfully', 'success');
        } else {
            $this->redirect('admin/social-media', 'Failed to delete social media link', 'error');
        }
    }

    /**
     * Toggle active status (AJAX)
     */
    public function toggle(int $id): void
    {
        if ($this->socialMediaModel->toggleActive($id)) {
            $this->json(['success' => true, 'message' => 'Status updated']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Update sort order (AJAX)
     */
    public function updateOrder(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $order = $input['order'] ?? [];

        if (empty($order)) {
            $this->json(['success' => false, 'message' => 'No order data']);
        }

        if ($this->socialMediaModel->updateSortOrder($order)) {
            $this->json(['success' => true, 'message' => 'Order updated']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update order']);
        }
    }

    // ==========================================
    // Campaign Messages
    // ==========================================

    /**
     * List all campaign messages
     */
    public function campaigns(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $platform = $this->get('platform');
        $type = $this->get('type');

        $campaigns = $this->campaignModel->getAllForStore($storeId, $platform, $type);
        $stats = $this->campaignModel->getStats($storeId);

        $this->view('admin/social-media/campaigns', [
            'pageTitle' => 'Campaign Messages',
            'campaigns' => $campaigns,
            'stats' => $stats,
            'platforms' => CampaignMessage::getPlatforms(),
            'messageTypes' => CampaignMessage::getMessageTypes(),
            'currentPlatform' => $platform,
            'currentType' => $type
        ], 'admin');
    }

    /**
     * Show create campaign form
     */
    public function createCampaign(): void
    {
        $this->view('admin/social-media/campaign-create', [
            'pageTitle' => 'Create Campaign Message',
            'platforms' => CampaignMessage::getPlatforms(),
            'messageTypes' => CampaignMessage::getMessageTypes()
        ], 'admin');
    }

    /**
     * Store new campaign message
     */
    public function storeCampaign(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/social-media/campaigns', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'title' => trim($this->post('title')),
            'platform' => $this->post('platform', 'all'),
            'message_type' => $this->post('message_type', 'promotion'),
            'content' => trim($this->post('content')),
            'short_content' => trim($this->post('short_content')) ?: null,
            'hashtags' => trim($this->post('hashtags')) ?: null,
            'call_to_action' => trim($this->post('call_to_action')) ?: null,
            'cta_url' => trim($this->post('cta_url')) ?: null,
            'scheduled_at' => $this->post('scheduled_at') ?: null,
            'expires_at' => $this->post('expires_at') ?: null,
            'is_active' => $this->post('is_active') ? 1 : 0,
            'is_pinned' => $this->post('is_pinned') ? 1 : 0,
            'created_by' => Session::getUserId()
        ];

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'campaigns');
            if ($imagePath) {
                $data['image_path'] = $imagePath;
            }
        }

        if (empty($data['title']) || empty($data['content'])) {
            $this->redirect('admin/social-media/campaigns/create', 'Title and content are required', 'error');
        }

        $id = $this->campaignModel->create($data);

        if ($id) {
            $this->redirect('admin/social-media/campaigns', 'Campaign message created successfully', 'success');
        } else {
            $this->redirect('admin/social-media/campaigns/create', 'Failed to create campaign message', 'error');
        }
    }

    /**
     * Show edit campaign form
     */
    public function editCampaign(int $id): void
    {
        $campaign = $this->campaignModel->getById($id);

        if (!$campaign) {
            $this->redirect('admin/social-media/campaigns', 'Campaign message not found', 'error');
        }

        $this->view('admin/social-media/campaign-edit', [
            'pageTitle' => 'Edit Campaign Message',
            'campaign' => $campaign,
            'platforms' => CampaignMessage::getPlatforms(),
            'messageTypes' => CampaignMessage::getMessageTypes()
        ], 'admin');
    }

    /**
     * Update campaign message
     */
    public function updateCampaign(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/social-media/campaigns', 'Invalid request', 'error');
        }

        $campaign = $this->campaignModel->getById($id);
        if (!$campaign) {
            $this->redirect('admin/social-media/campaigns', 'Campaign message not found', 'error');
        }

        $data = [
            'title' => trim($this->post('title')),
            'platform' => $this->post('platform', 'all'),
            'message_type' => $this->post('message_type', 'promotion'),
            'content' => trim($this->post('content')),
            'short_content' => trim($this->post('short_content')) ?: null,
            'hashtags' => trim($this->post('hashtags')) ?: null,
            'call_to_action' => trim($this->post('call_to_action')) ?: null,
            'cta_url' => trim($this->post('cta_url')) ?: null,
            'scheduled_at' => $this->post('scheduled_at') ?: null,
            'expires_at' => $this->post('expires_at') ?: null,
            'is_active' => $this->post('is_active') ? 1 : 0,
            'is_pinned' => $this->post('is_pinned') ? 1 : 0,
            'image_path' => $campaign['image_path']
        ];

        // Handle new image upload
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'campaigns');
            if ($imagePath) {
                $data['image_path'] = $imagePath;
            }
        }

        if (empty($data['title']) || empty($data['content'])) {
            $this->redirect("admin/social-media/campaigns/edit/{$id}", 'Title and content are required', 'error');
        }

        if ($this->campaignModel->update($id, $data)) {
            $this->redirect('admin/social-media/campaigns', 'Campaign message updated successfully', 'success');
        } else {
            $this->redirect("admin/social-media/campaigns/edit/{$id}", 'Failed to update campaign message', 'error');
        }
    }

    /**
     * Delete campaign message
     */
    public function deleteCampaign(int $id): void
    {
        if (!Session::validateCsrf($this->get('csrf_token'))) {
            $this->redirect('admin/social-media/campaigns', 'Invalid request', 'error');
        }

        if ($this->campaignModel->delete($id)) {
            $this->redirect('admin/social-media/campaigns', 'Campaign message deleted successfully', 'success');
        } else {
            $this->redirect('admin/social-media/campaigns', 'Failed to delete campaign message', 'error');
        }
    }

    /**
     * Toggle campaign active status (AJAX)
     */
    public function toggleCampaign(int $id): void
    {
        if ($this->campaignModel->toggleActive($id)) {
            $this->json(['success' => true, 'message' => 'Status updated']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Toggle campaign pinned status (AJAX)
     */
    public function pinCampaign(int $id): void
    {
        if ($this->campaignModel->togglePinned($id)) {
            $this->json(['success' => true, 'message' => 'Pin status updated']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update pin status']);
        }
    }

    /**
     * Duplicate campaign message
     */
    public function duplicateCampaign(int $id): void
    {
        $newId = $this->campaignModel->duplicate($id);

        if ($newId) {
            $this->redirect("admin/social-media/campaigns/edit/{$newId}", 'Campaign duplicated successfully', 'success');
        } else {
            $this->redirect('admin/social-media/campaigns', 'Failed to duplicate campaign', 'error');
        }
    }

    /**
     * Copy campaign content (AJAX) - increment copy count
     */
    public function copyCampaign(int $id): void
    {
        $campaign = $this->campaignModel->getById($id);

        if ($campaign) {
            $this->campaignModel->incrementCopyCount($id);
            // Track copy event
            $this->analyticsModel->trackEvent($id, 'copy', ['source' => 'admin']);
            $this->json([
                'success' => true,
                'content' => $campaign['content'],
                'hashtags' => $campaign['hashtags'],
                'short_content' => $campaign['short_content']
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Campaign not found']);
        }
    }

    // ==========================================
    // Campaign Analytics & Insights
    // ==========================================

    /**
     * Campaign insights dashboard
     */
    public function insights(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $insights = $this->analyticsModel->getDashboardInsights($storeId);

        // Get date range for trends
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $trends = $this->analyticsModel->getPerformanceTrends($storeId, $startDate, $endDate);

        $this->view('admin/social-media/insights', [
            'pageTitle' => 'Campaign Insights',
            'insights' => $insights,
            'trends' => $trends,
            'platforms' => CampaignMessage::getPlatforms(),
            'messageTypes' => CampaignMessage::getMessageTypes()
        ], 'admin');
    }

    /**
     * Single campaign performance view
     */
    public function campaignPerformance(int $id): void
    {
        $campaign = $this->campaignModel->getById($id);

        if (!$campaign) {
            $this->redirect('admin/social-media/campaigns', 'Campaign not found', 'error');
        }

        $performance = $this->analyticsModel->getCampaignPerformance($id);
        $goals = $this->analyticsModel->getGoals($id);
        $notes = $this->analyticsModel->getNotes($id);

        $this->view('admin/social-media/campaign-performance', [
            'pageTitle' => 'Campaign Performance: ' . $campaign['title'],
            'campaign' => $campaign,
            'performance' => $performance,
            'goals' => $goals,
            'notes' => $notes,
            'platforms' => CampaignMessage::getPlatforms(),
            'messageTypes' => CampaignMessage::getMessageTypes()
        ], 'admin');
    }

    /**
     * Track campaign event (AJAX - for external tracking)
     */
    public function trackEvent(int $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $eventType = $input['event_type'] ?? 'view';

        $result = $this->analyticsModel->trackEvent($id, $eventType, [
            'platform' => $input['platform'] ?? null,
            'source' => $input['source'] ?? 'external',
            'metadata' => $input['metadata'] ?? null
        ]);

        $this->json(['success' => $result]);
    }

    /**
     * Add campaign goal
     */
    public function addGoal(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect("admin/social-media/campaigns/performance/{$id}", 'Invalid request', 'error');
        }

        $goalType = $this->post('goal_type');
        $targetValue = (int) $this->post('target_value');
        $startDate = $this->post('start_date') ?: null;
        $endDate = $this->post('end_date') ?: null;

        if ($this->analyticsModel->addGoal($id, $goalType, $targetValue, $startDate, $endDate)) {
            $this->redirect("admin/social-media/campaigns/performance/{$id}", 'Goal added successfully', 'success');
        } else {
            $this->redirect("admin/social-media/campaigns/performance/{$id}", 'Failed to add goal', 'error');
        }
    }

    /**
     * Add campaign note
     */
    public function addNote(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect("admin/social-media/campaigns/performance/{$id}", 'Invalid request', 'error');
        }

        $note = trim($this->post('note'));
        $noteType = $this->post('note_type', 'general');

        if (empty($note)) {
            $this->redirect("admin/social-media/campaigns/performance/{$id}", 'Note cannot be empty', 'error');
        }

        if ($this->analyticsModel->addNote($id, Session::getUserId(), $note, $noteType)) {
            $this->redirect("admin/social-media/campaigns/performance/{$id}", 'Note added successfully', 'success');
        } else {
            $this->redirect("admin/social-media/campaigns/performance/{$id}", 'Failed to add note', 'error');
        }
    }

    /**
     * Delete campaign note
     */
    public function deleteNote(int $id, int $noteId): void
    {
        if (!Session::validateCsrf($this->get('csrf_token'))) {
            $this->redirect("admin/social-media/campaigns/performance/{$id}", 'Invalid request', 'error');
        }

        if ($this->analyticsModel->deleteNote($noteId)) {
            $this->redirect("admin/social-media/campaigns/performance/{$id}", 'Note deleted', 'success');
        } else {
            $this->redirect("admin/social-media/campaigns/performance/{$id}", 'Failed to delete note', 'error');
        }
    }

    /**
     * Export campaign analytics (CSV)
     */
    public function exportAnalytics(int $id): void
    {
        $campaign = $this->campaignModel->getById($id);
        if (!$campaign) {
            $this->redirect('admin/social-media/campaigns', 'Campaign not found', 'error');
        }

        $performance = $this->analyticsModel->getCampaignPerformance($id);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="campaign-' . $id . '-analytics.csv"');

        $output = fopen('php://output', 'w');

        // Campaign info
        fputcsv($output, ['Campaign Analytics Report']);
        fputcsv($output, ['Title', $campaign['title']]);
        fputcsv($output, ['Platform', $campaign['platform']]);
        fputcsv($output, ['Created', $campaign['created_at']]);
        fputcsv($output, []);

        // Summary
        fputcsv($output, ['Performance Summary']);
        fputcsv($output, ['Metric', 'Value']);
        fputcsv($output, ['Total Views', $performance['summary']['total_views']]);
        fputcsv($output, ['Total Clicks', $performance['summary']['total_clicks']]);
        fputcsv($output, ['Total Copies', $performance['summary']['total_copies']]);
        fputcsv($output, ['Conversion Rate', $performance['summary']['conversion_rate'] . '%']);
        fputcsv($output, []);

        // Daily stats
        fputcsv($output, ['Daily Statistics']);
        fputcsv($output, ['Date', 'Views', 'Clicks', 'Copies', 'Shares', 'Unique Views']);
        foreach ($performance['daily_stats'] as $stat) {
            fputcsv($output, [
                $stat['stat_date'],
                $stat['views'],
                $stat['clicks'],
                $stat['copies'],
                $stat['shares'],
                $stat['unique_views']
            ]);
        }

        fclose($output);
        exit;
    }
}
