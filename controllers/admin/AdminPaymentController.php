<?php

class AdminPaymentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    /**
     * Payment methods list
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        // Get payment settings
        $paymentMethods = [
            [
                'id' => 'cod',
                'name' => 'Cash on Delivery',
                'description' => 'Pay when you receive your order',
                'icon' => 'fas fa-money-bill-wave',
                'enabled' => $this->getSetting('payment_cod_enabled', '1', $storeId) === '1'
            ],
            [
                'id' => 'bkash',
                'name' => 'bKash',
                'description' => 'Pay with bKash mobile banking',
                'icon' => 'fas fa-mobile-alt',
                'enabled' => $this->getSetting('payment_bkash_enabled', '0', $storeId) === '1'
            ],
            [
                'id' => 'nagad',
                'name' => 'Nagad',
                'description' => 'Pay with Nagad mobile banking',
                'icon' => 'fas fa-mobile-alt',
                'enabled' => $this->getSetting('payment_nagad_enabled', '0', $storeId) === '1'
            ],
            [
                'id' => 'card',
                'name' => 'Credit/Debit Card',
                'description' => 'Pay with Visa, Mastercard',
                'icon' => 'fas fa-credit-card',
                'enabled' => $this->getSetting('payment_card_enabled', '0', $storeId) === '1'
            ],
            [
                'id' => 'bank',
                'name' => 'Bank Transfer',
                'description' => 'Direct bank transfer',
                'icon' => 'fas fa-university',
                'enabled' => $this->getSetting('payment_bank_enabled', '0', $storeId) === '1'
            ]
        ];

        // Get recent transactions
        $transactions = $this->db->fetchAll(
            "SELECT p.*, o.order_number, u.name as customer_name
             FROM payments p
             LEFT JOIN orders o ON p.order_id = o.id
             LEFT JOIN users u ON o.user_id = u.id
             WHERE o.store_id = ?
             ORDER BY p.created_at DESC
             LIMIT 20",
            [$storeId]
        );

        $data = [
            'pageTitle' => 'Payment Methods',
            'paymentMethods' => $paymentMethods,
            'transactions' => $transactions
        ];

        $this->view('admin/payments/index', $data, 'admin');
    }

    /**
     * Payment settings
     */
    public function settings(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $methods = ['cod', 'bkash', 'nagad', 'card', 'bank'];

            foreach ($methods as $method) {
                $enabled = $this->post("payment_{$method}_enabled") ? '1' : '0';
                $this->saveSetting("payment_{$method}_enabled", $enabled, $storeId);

                // Save additional settings for each method
                if ($method === 'bkash') {
                    $this->saveSetting('payment_bkash_number', $this->post('payment_bkash_number', ''), $storeId);
                } elseif ($method === 'nagad') {
                    $this->saveSetting('payment_nagad_number', $this->post('payment_nagad_number', ''), $storeId);
                } elseif ($method === 'bank') {
                    $this->saveSetting('payment_bank_name', $this->post('payment_bank_name', ''), $storeId);
                    $this->saveSetting('payment_bank_account', $this->post('payment_bank_account', ''), $storeId);
                    $this->saveSetting('payment_bank_branch', $this->post('payment_bank_branch', ''), $storeId);
                }
            }

            $this->redirect('admin/payments', 'Payment settings updated successfully');
        }

        // Get current settings
        $settings = [
            'cod_enabled' => $this->getSetting('payment_cod_enabled', '1', $storeId),
            'bkash_enabled' => $this->getSetting('payment_bkash_enabled', '0', $storeId),
            'bkash_number' => $this->getSetting('payment_bkash_number', '', $storeId),
            'nagad_enabled' => $this->getSetting('payment_nagad_enabled', '0', $storeId),
            'nagad_number' => $this->getSetting('payment_nagad_number', '', $storeId),
            'card_enabled' => $this->getSetting('payment_card_enabled', '0', $storeId),
            'bank_enabled' => $this->getSetting('payment_bank_enabled', '0', $storeId),
            'bank_name' => $this->getSetting('payment_bank_name', '', $storeId),
            'bank_account' => $this->getSetting('payment_bank_account', '', $storeId),
            'bank_branch' => $this->getSetting('payment_bank_branch', '', $storeId),
        ];

        $data = [
            'pageTitle' => 'Payment Settings',
            'settings' => $settings
        ];

        $this->view('admin/payments/settings', $data, 'admin');
    }

    /**
     * Get setting value
     */
    private function getSetting(string $key, string $default = '', int $storeId = 1): string
    {
        $setting = $this->db->fetch(
            "SELECT setting_value FROM settings WHERE setting_key = ? AND store_id = ?",
            [$key, $storeId]
        );

        return $setting['setting_value'] ?? $default;
    }

    /**
     * Save setting
     */
    private function saveSetting(string $key, string $value, int $storeId = 1): void
    {
        $existing = $this->db->fetch(
            "SELECT id FROM settings WHERE setting_key = ? AND store_id = ?",
            [$key, $storeId]
        );

        if ($existing) {
            $this->db->update('settings',
                ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => $existing['id']]
            );
        } else {
            $this->db->insert('settings', [
                'store_id' => $storeId,
                'setting_key' => $key,
                'setting_value' => $value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
