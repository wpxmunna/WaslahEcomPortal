<?php
/**
 * Admin Order Controller
 */

class AdminOrderController extends Controller
{
    private Order $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->orderModel = new Order();
    }

    /**
     * List orders
     */
    public function index(): void
    {
        $page = (int) $this->get('page', 1);
        $storeId = Session::get('current_store_id', 1);

        $filters = [
            'status' => $this->get('status'),
            'search' => $this->get('search')
        ];

        $orders = $this->orderModel->getAdminOrders($page, 20, $storeId, $filters);

        $data = [
            'pageTitle' => 'Orders - Admin',
            'orders' => $orders,
            'filters' => $filters,
            'stores' => (new Store())->getActive()
        ];

        $this->view('admin/orders/index', $data, 'admin');
    }

    /**
     * View order details
     */
    public function show(int $id): void
    {
        $order = $this->orderModel->getWithDetails($id);

        if (!$order) {
            $this->redirect('admin/orders', 'Order not found', 'error');
            return;
        }

        $data = [
            'pageTitle' => 'Order ' . $order['order_number'] . ' - Admin',
            'order' => $order,
            'stores' => (new Store())->getActive()
        ];

        parent::view('admin/orders/view', $data, 'admin');
    }

    /**
     * Update order status (AJAX)
     */
    public function updateStatus(int $id): void
    {
        $status = $this->post('status');

        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];

        if (!in_array($status, $validStatuses)) {
            $this->json(['success' => false, 'message' => 'Invalid status']);
            return;
        }

        $this->orderModel->updateStatus($id, $status);

        $pathaoResult = null;

        // If processing, create Pathao pickup request
        if ($status === 'processing') {
            $pathaoResult = $this->createPathaoOrder($id);
        }

        // If shipped, simulate courier
        if ($status === 'shipped') {
            $shipment = $this->db->fetch("SELECT * FROM shipments WHERE order_id = ?", [$id]);
            if ($shipment) {
                $courierService = new CourierService();
                $courierService->updateStatus($shipment['id'], 'picked_up', 'Order picked up', 'Warehouse');
            }
        }

        // If delivered
        if ($status === 'delivered') {
            $shipment = $this->db->fetch("SELECT * FROM shipments WHERE order_id = ?", [$id]);
            if ($shipment) {
                $courierService = new CourierService();
                $courierService->updateStatus($shipment['id'], 'delivered', 'Package delivered', 'Customer Address');
            }
        }

        $response = ['success' => true, 'message' => 'Status updated'];

        if ($pathaoResult) {
            $response['pathao'] = $pathaoResult;
        }

        $this->json($response);
    }

    /**
     * Create Pathao order for pickup
     */
    private function createPathaoOrder(int $orderId): ?array
    {
        $storeId = Session::get('admin_store_id', 1);
        $pathao = new PathaoService($storeId);

        // Check if Pathao is enabled
        if (!$pathao->isEnabled()) {
            return null;
        }

        // Get order details
        $order = $this->orderModel->getWithDetails($orderId);
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }

        // Check if already has Pathao consignment
        $existingShipment = $this->db->fetch(
            "SELECT * FROM shipments WHERE order_id = ? AND courier_name = 'Pathao'",
            [$orderId]
        );

        if ($existingShipment && !empty($existingShipment['tracking_number'])) {
            return ['success' => true, 'message' => 'Pathao order already exists', 'consignment_id' => $existingShipment['tracking_number']];
        }

        // Get default weight from settings
        $weightSetting = $this->db->fetch(
            "SELECT setting_value FROM settings WHERE store_id = ? AND setting_key = 'pathao_default_weight'",
            [$storeId]
        );
        $defaultWeight = (float)($weightSetting['setting_value'] ?? 0.5);

        // Calculate total items
        $totalItems = 0;
        $itemDescriptions = [];
        foreach ($order['items'] as $item) {
            $totalItems += $item['quantity'];
            $itemDescriptions[] = $item['product_name'] . ' x' . $item['quantity'];
        }

        // Prepare order data for Pathao
        $orderData = [
            'order_number' => $order['order_number'],
            'recipient_name' => $order['shipping_name'] ?? $order['customer_name'],
            'recipient_phone' => $order['shipping_phone'] ?? $order['customer_phone'] ?? '',
            'recipient_address' => $this->formatAddress($order),
            'item_quantity' => $totalItems,
            'item_weight' => $defaultWeight,
            'amount_to_collect' => ($order['payment_method'] === 'cod') ? (float)$order['total_amount'] : 0,
            'item_description' => implode(', ', array_slice($itemDescriptions, 0, 3)),
            'special_instruction' => $order['notes'] ?? ''
        ];

        // Create Pathao order
        $result = $pathao->createOrder($orderData);

        if ($result['success']) {
            // Save shipment record
            $this->db->query(
                "INSERT INTO shipments (order_id, courier_id, courier_name, tracking_number, status, delivery_fee, created_at, updated_at)
                 VALUES (?, ?, 'Pathao', ?, 'pending', ?, NOW(), NOW())",
                [
                    $orderId,
                    0, // No local courier ID for Pathao
                    $result['consignment_id'],
                    $result['delivery_fee'] ?? 0
                ]
            );

            // Log success
            logMessage("Pathao order created for Order #{$order['order_number']}: {$result['consignment_id']}", 'info');

            return [
                'success' => true,
                'message' => 'Pathao pickup request created',
                'consignment_id' => $result['consignment_id'],
                'delivery_fee' => $result['delivery_fee'] ?? 0
            ];
        }

        // Log error
        logMessage("Pathao order failed for Order #{$order['order_number']}: " . ($result['error'] ?? 'Unknown error'), 'error');

        return $result;
    }

    /**
     * Format address for Pathao
     */
    private function formatAddress(array $order): string
    {
        $parts = array_filter([
            $order['shipping_address'] ?? '',
            $order['shipping_city'] ?? '',
            $order['shipping_state'] ?? '',
            $order['shipping_zip'] ?? '',
            $order['shipping_country'] ?? 'Bangladesh'
        ]);

        return implode(', ', $parts);
    }

    /**
     * Manually trigger Pathao order creation
     */
    public function createPathaoShipment(int $id): void
    {
        $result = $this->createPathaoOrder($id);

        if ($result && $result['success']) {
            $this->json(['success' => true, 'message' => $result['message'], 'data' => $result]);
        } else {
            $this->json(['success' => false, 'message' => $result['error'] ?? 'Failed to create Pathao order']);
        }
    }

    /**
     * Get Pathao order status
     */
    public function pathaoStatus(int $id): void
    {
        $shipment = $this->db->fetch(
            "SELECT * FROM shipments WHERE order_id = ? AND courier_name = 'Pathao'",
            [$id]
        );

        if (!$shipment || empty($shipment['tracking_number'])) {
            $this->json(['success' => false, 'message' => 'No Pathao shipment found']);
            return;
        }

        $storeId = Session::get('admin_store_id', 1);
        $pathao = new PathaoService($storeId);

        $result = $pathao->getOrderInfo($shipment['tracking_number']);

        $this->json($result);
    }

    /**
     * Generate invoice
     */
    public function invoice(int $id): void
    {
        $order = $this->orderModel->getWithDetails($id);

        if (!$order) {
            $this->redirect('admin/orders', 'Order not found', 'error');
            return;
        }

        $data = [
            'pageTitle' => 'Invoice - ' . $order['order_number'],
            'order' => $order
        ];

        $this->view('admin/orders/invoice', $data, null);
    }
}
