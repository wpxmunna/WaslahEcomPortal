<?php
/**
 * Mock Courier Service
 * Simulates shipping and tracking for testing
 */

class CourierService
{
    private Database $db;
    private Courier $courierModel;

    public function __construct()
    {
        $this->db = new Database();
        $this->courierModel = new Courier();
    }

    /**
     * Create shipment for order
     */
    public function createShipment(int $orderId, int $courierId): array
    {
        $order = $this->db->fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);

        if (!$order) {
            return $this->errorResponse('Order not found');
        }

        $courier = $this->courierModel->find($courierId);

        if (!$courier) {
            return $this->errorResponse('Courier not found');
        }

        // Create shipment
        $shipmentId = $this->courierModel->createShipment($orderId, $courierId);

        // Get shipment details
        $shipment = $this->db->fetch("SELECT * FROM shipments WHERE id = ?", [$shipmentId]);

        // Update order status
        $orderModel = new Order();
        $orderModel->updateStatus($orderId, 'processing');

        return $this->successResponse([
            'shipment_id' => $shipmentId,
            'tracking_number' => $shipment['tracking_number'],
            'courier' => $courier['name'],
            'estimated_delivery' => $courier['estimated_days'],
            'message' => 'Shipment created successfully'
        ]);
    }

    /**
     * Track shipment
     */
    public function track(string $trackingNumber): array
    {
        $shipment = $this->courierModel->getShipmentByTracking($trackingNumber);

        if (!$shipment) {
            return $this->errorResponse('Shipment not found');
        }

        return $this->successResponse([
            'tracking_number' => $shipment['tracking_number'],
            'courier' => $shipment['courier_name'],
            'status' => $shipment['status'],
            'shipped_at' => $shipment['shipped_at'],
            'delivered_at' => $shipment['delivered_at'],
            'tracking_history' => $shipment['tracking']
        ]);
    }

    /**
     * Update shipment status
     */
    public function updateStatus(int $shipmentId, string $status, string $description = '', string $location = ''): array
    {
        $shipment = $this->db->fetch("SELECT * FROM shipments WHERE id = ?", [$shipmentId]);

        if (!$shipment) {
            return $this->errorResponse('Shipment not found');
        }

        $validStatuses = ['pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed'];

        if (!in_array($status, $validStatuses)) {
            return $this->errorResponse('Invalid status');
        }

        $this->courierModel->updateShipmentStatus($shipmentId, $status, $description, $location);

        // Update order status based on shipment
        $order = $this->db->fetch("SELECT * FROM orders WHERE id = ?", [$shipment['order_id']]);

        if ($status === 'picked_up' || $status === 'in_transit') {
            $orderModel = new Order();
            $orderModel->updateStatus($order['id'], 'shipped');
        } elseif ($status === 'delivered') {
            $orderModel = new Order();
            $orderModel->updateStatus($order['id'], 'delivered');
        }

        return $this->successResponse([
            'message' => 'Shipment status updated',
            'new_status' => $status
        ]);
    }

    /**
     * Simulate delivery progress
     * This can be called via cron or manually to simulate package movement
     */
    public function simulateProgress(int $shipmentId): array
    {
        $shipment = $this->db->fetch("SELECT * FROM shipments WHERE id = ?", [$shipmentId]);

        if (!$shipment) {
            return $this->errorResponse('Shipment not found');
        }

        if ($shipment['status'] === 'delivered') {
            return $this->successResponse(['message' => 'Already delivered']);
        }

        $this->courierModel->simulateProgress($shipmentId);

        $updated = $this->db->fetch("SELECT * FROM shipments WHERE id = ?", [$shipmentId]);

        return $this->successResponse([
            'previous_status' => $shipment['status'],
            'new_status' => $updated['status'],
            'message' => 'Shipment progressed'
        ]);
    }

    /**
     * Calculate shipping rates
     */
    public function calculateRates(float $weight, string $destination, int $storeId = 1): array
    {
        $couriers = $this->courierModel->getActive($storeId);

        $rates = [];
        foreach ($couriers as $courier) {
            $cost = $courier['base_rate'];

            if ($weight > 0 && $courier['per_kg_rate'] > 0) {
                $cost += $weight * $courier['per_kg_rate'];
            }

            $rates[] = [
                'courier_id' => $courier['id'],
                'courier_name' => $courier['name'],
                'courier_code' => $courier['code'],
                'cost' => $cost,
                'estimated_days' => $courier['estimated_days'],
                'description' => $courier['description']
            ];
        }

        return $this->successResponse([
            'rates' => $rates,
            'weight' => $weight,
            'destination' => $destination
        ]);
    }

    /**
     * Get estimated delivery date
     */
    public function getEstimatedDelivery(int $courierId): ?string
    {
        $courier = $this->courierModel->find($courierId);

        if (!$courier) {
            return null;
        }

        // Parse estimated days (e.g., "5-7 days" -> take middle value)
        preg_match('/(\d+)(?:-(\d+))?/', $courier['estimated_days'], $matches);

        if (isset($matches[2])) {
            $days = ceil(($matches[1] + $matches[2]) / 2);
        } elseif (isset($matches[1])) {
            $days = (int) $matches[1];
        } else {
            $days = 7; // Default
        }

        return date('Y-m-d', strtotime("+{$days} days"));
    }

    /**
     * Get shipment by order
     */
    public function getByOrder(int $orderId): ?array
    {
        $shipment = $this->db->fetch(
            "SELECT s.*, c.name as courier_name, c.tracking_url
             FROM shipments s
             LEFT JOIN couriers c ON s.courier_id = c.id
             WHERE s.order_id = ?",
            [$orderId]
        );

        if ($shipment) {
            $shipment['tracking'] = $this->db->fetchAll(
                "SELECT * FROM shipment_tracking WHERE shipment_id = ? ORDER BY tracked_at DESC",
                [$shipment['id']]
            );
        }

        return $shipment;
    }

    /**
     * Success response
     */
    private function successResponse(array $data): array
    {
        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * Error response
     */
    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => $message
        ];
    }

    /**
     * Get status label
     */
    public static function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'Order Placed',
            'picked_up' => 'Picked Up',
            'in_transit' => 'In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'failed' => 'Delivery Failed',
            default => ucfirst($status)
        };
    }

    /**
     * Get status icon
     */
    public static function getStatusIcon(string $status): string
    {
        return match($status) {
            'pending' => 'fa-clock',
            'picked_up' => 'fa-box',
            'in_transit' => 'fa-truck',
            'out_for_delivery' => 'fa-shipping-fast',
            'delivered' => 'fa-check-circle',
            'failed' => 'fa-times-circle',
            default => 'fa-info-circle'
        };
    }
}
