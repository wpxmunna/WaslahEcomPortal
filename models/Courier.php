<?php
/**
 * Courier Model
 */

class Courier extends Model
{
    protected string $table = 'couriers';
    protected array $fillable = [
        'store_id', 'name', 'code', 'description', 'logo',
        'base_rate', 'per_kg_rate', 'estimated_days', 'tracking_url', 'status'
    ];

    /**
     * Get active couriers
     */
    public function getActive(int $storeId = 1): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM couriers WHERE store_id = ? AND status = 1 ORDER BY base_rate",
            [$storeId]
        );
    }

    /**
     * Get all couriers by store
     */
    public function getByStore(int $storeId = 1): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM couriers WHERE store_id = ? ORDER BY name",
            [$storeId]
        );
    }

    /**
     * Calculate shipping cost
     */
    public function calculateShipping(int $courierId, float $weight = 0, float $subtotal = 0): float
    {
        $courier = $this->find($courierId);

        if (!$courier) {
            return DEFAULT_SHIPPING_COST;
        }

        // Free shipping check
        if ($courier['code'] === 'free' && $subtotal >= FREE_SHIPPING_THRESHOLD) {
            return 0;
        }

        $cost = $courier['base_rate'];
        if ($weight > 0 && $courier['per_kg_rate'] > 0) {
            $cost += $weight * $courier['per_kg_rate'];
        }

        return $cost;
    }

    /**
     * Get by code
     */
    public function findByCode(string $code, int $storeId = 1): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM couriers WHERE code = ? AND store_id = ?",
            [$code, $storeId]
        );
    }

    /**
     * Create shipment
     */
    public function createShipment(int $orderId, int $courierId): int
    {
        $trackingNumber = $this->generateTrackingNumber();

        $shipmentId = $this->db->insert('shipments', [
            'order_id' => $orderId,
            'courier_id' => $courierId,
            'tracking_number' => $trackingNumber,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Add initial tracking entry
        $this->addTracking($shipmentId, 'pending', 'Order placed', 'Warehouse');

        return $shipmentId;
    }

    /**
     * Update shipment status
     */
    public function updateShipmentStatus(int $shipmentId, string $status, string $description = '', string $location = ''): bool
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'picked_up') {
            $data['shipped_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'delivered') {
            $data['delivered_at'] = date('Y-m-d H:i:s');
        }

        $result = $this->db->update('shipments', $data, 'id = ?', [$shipmentId]);

        if ($result) {
            $this->addTracking($shipmentId, $status, $description, $location);
        }

        return $result > 0;
    }

    /**
     * Add tracking entry
     */
    public function addTracking(int $shipmentId, string $status, string $description, string $location = ''): int
    {
        return $this->db->insert('shipment_tracking', [
            'shipment_id' => $shipmentId,
            'status' => $status,
            'description' => $description,
            'location' => $location,
            'tracked_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get shipment by tracking number
     */
    public function getShipmentByTracking(string $trackingNumber): ?array
    {
        $shipment = $this->db->fetch(
            "SELECT s.*, c.name as courier_name, c.tracking_url
             FROM shipments s
             LEFT JOIN couriers c ON s.courier_id = c.id
             WHERE s.tracking_number = ?",
            [$trackingNumber]
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
     * Generate tracking number
     */
    private function generateTrackingNumber(): string
    {
        return 'WAS' . date('Ymd') . strtoupper(randomString(8));
    }

    /**
     * Simulate shipment progress (for mock purposes)
     */
    public function simulateProgress(int $shipmentId): void
    {
        $shipment = $this->db->fetch("SELECT * FROM shipments WHERE id = ?", [$shipmentId]);

        if (!$shipment) {
            return;
        }

        $statuses = [
            'pending' => ['picked_up', 'Order picked up from warehouse', 'Local Facility'],
            'picked_up' => ['in_transit', 'Package in transit', 'Distribution Center'],
            'in_transit' => ['out_for_delivery', 'Out for delivery', 'Local Delivery Hub'],
            'out_for_delivery' => ['delivered', 'Package delivered', 'Customer Address']
        ];

        if (isset($statuses[$shipment['status']])) {
            [$newStatus, $description, $location] = $statuses[$shipment['status']];
            $this->updateShipmentStatus($shipmentId, $newStatus, $description, $location);
        }
    }
}
