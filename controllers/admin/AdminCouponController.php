<?php
/**
 * Admin Coupon Controller
 */

class AdminCouponController extends Controller
{
    private Coupon $couponModel;
    private Product $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->couponModel = new Coupon();
        $this->productModel = new Product();
    }

    /**
     * List all coupons
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $coupons = $this->couponModel->getByStore($storeId);

        $this->view('admin/coupons/index', [
            'pageTitle' => 'Coupons - Admin',
            'coupons' => $coupons
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $products = $this->productModel->getByStore($storeId);

        $this->view('admin/coupons/create', [
            'pageTitle' => 'Add Coupon - Admin',
            'products' => $products
        ], 'admin');
    }

    /**
     * Store new coupon
     */
    public function store(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $code = strtoupper(trim($this->post('code')));
        $type = $this->post('type', 'fixed');
        $value = (float) $this->post('value', 0);
        $minimumAmount = (float) $this->post('minimum_amount', 0);
        $maximumDiscount = $this->post('maximum_discount') ? (float) $this->post('maximum_discount') : null;
        $giftProductId = $this->post('gift_product_id') ? (int) $this->post('gift_product_id') : null;
        $buyQuantity = $this->post('buy_quantity') ? (int) $this->post('buy_quantity') : null;
        $getQuantity = $this->post('get_quantity') ? (int) $this->post('get_quantity') : null;
        $usageLimit = $this->post('usage_limit') ? (int) $this->post('usage_limit') : null;
        $startsAt = $this->post('starts_at') ?: null;
        $expiresAt = $this->post('expires_at') ?: null;
        $status = $this->post('status') ? 1 : 0;

        // Validation
        if (empty($code)) {
            $this->redirect('admin/coupons/create', 'Coupon code is required', 'error');
            return;
        }

        // Type-specific validation
        if (in_array($type, ['fixed', 'percentage', 'buy_x_get_y']) && $value <= 0) {
            $this->redirect('admin/coupons/create', 'Discount value must be greater than 0', 'error');
            return;
        }

        if ($type === 'percentage' && $value > 100) {
            $this->redirect('admin/coupons/create', 'Percentage discount cannot exceed 100%', 'error');
            return;
        }

        if ($type === 'gift_item' && !$giftProductId) {
            $this->redirect('admin/coupons/create', 'Please select a gift product', 'error');
            return;
        }

        if ($type === 'buy_x_get_y' && (!$buyQuantity || !$getQuantity)) {
            $this->redirect('admin/coupons/create', 'Please specify buy and get quantities', 'error');
            return;
        }

        // Check if code exists
        if ($this->couponModel->codeExists($code, $storeId)) {
            $this->redirect('admin/coupons/create', 'Coupon code already exists', 'error');
            return;
        }

        $couponId = $this->couponModel->create([
            'store_id' => $storeId,
            'code' => $code,
            'type' => $type,
            'value' => $value,
            'minimum_amount' => $minimumAmount,
            'maximum_discount' => $maximumDiscount,
            'gift_product_id' => $giftProductId,
            'buy_quantity' => $buyQuantity,
            'get_quantity' => $getQuantity,
            'usage_limit' => $usageLimit,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'status' => $status
        ]);

        if ($couponId) {
            $this->redirect('admin/coupons', 'Coupon created successfully');
        } else {
            $this->redirect('admin/coupons/create', 'Failed to create coupon', 'error');
        }
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $coupon = $this->couponModel->find($id);

        if (!$coupon || $coupon['store_id'] != $storeId) {
            $this->redirect('admin/coupons', 'Coupon not found', 'error');
            return;
        }

        $products = $this->productModel->getByStore($storeId);

        $this->view('admin/coupons/edit', [
            'pageTitle' => 'Edit Coupon - Admin',
            'coupon' => $coupon,
            'products' => $products
        ], 'admin');
    }

    /**
     * Update coupon
     */
    public function update(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $coupon = $this->couponModel->find($id);

        if (!$coupon || $coupon['store_id'] != $storeId) {
            $this->redirect('admin/coupons', 'Coupon not found', 'error');
            return;
        }

        $code = strtoupper(trim($this->post('code')));
        $type = $this->post('type', 'fixed');
        $value = (float) $this->post('value', 0);
        $minimumAmount = (float) $this->post('minimum_amount', 0);
        $maximumDiscount = $this->post('maximum_discount') ? (float) $this->post('maximum_discount') : null;
        $giftProductId = $this->post('gift_product_id') ? (int) $this->post('gift_product_id') : null;
        $buyQuantity = $this->post('buy_quantity') ? (int) $this->post('buy_quantity') : null;
        $getQuantity = $this->post('get_quantity') ? (int) $this->post('get_quantity') : null;
        $usageLimit = $this->post('usage_limit') ? (int) $this->post('usage_limit') : null;
        $startsAt = $this->post('starts_at') ?: null;
        $expiresAt = $this->post('expires_at') ?: null;
        $status = $this->post('status') ? 1 : 0;

        // Validation
        if (empty($code)) {
            $this->redirect('admin/coupons/edit/' . $id, 'Coupon code is required', 'error');
            return;
        }

        // Type-specific validation
        if (in_array($type, ['fixed', 'percentage', 'buy_x_get_y']) && $value <= 0) {
            $this->redirect('admin/coupons/edit/' . $id, 'Discount value must be greater than 0', 'error');
            return;
        }

        if ($type === 'percentage' && $value > 100) {
            $this->redirect('admin/coupons/edit/' . $id, 'Percentage discount cannot exceed 100%', 'error');
            return;
        }

        if ($type === 'gift_item' && !$giftProductId) {
            $this->redirect('admin/coupons/edit/' . $id, 'Please select a gift product', 'error');
            return;
        }

        if ($type === 'buy_x_get_y' && (!$buyQuantity || !$getQuantity)) {
            $this->redirect('admin/coupons/edit/' . $id, 'Please specify buy and get quantities', 'error');
            return;
        }

        // Check if code exists (excluding current)
        if ($this->couponModel->codeExists($code, $storeId, $id)) {
            $this->redirect('admin/coupons/edit/' . $id, 'Coupon code already exists', 'error');
            return;
        }

        $this->couponModel->update($id, [
            'code' => $code,
            'type' => $type,
            'value' => $value,
            'minimum_amount' => $minimumAmount,
            'maximum_discount' => $maximumDiscount,
            'gift_product_id' => $giftProductId,
            'buy_quantity' => $buyQuantity,
            'get_quantity' => $getQuantity,
            'usage_limit' => $usageLimit,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'status' => $status
        ]);

        $this->redirect('admin/coupons', 'Coupon updated successfully');
    }

    /**
     * Delete coupon
     */
    public function delete(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $coupon = $this->couponModel->find($id);

        if (!$coupon || $coupon['store_id'] != $storeId) {
            $this->redirect('admin/coupons', 'Coupon not found', 'error');
            return;
        }

        $this->couponModel->delete($id);
        $this->redirect('admin/coupons', 'Coupon deleted successfully');
    }

    /**
     * Toggle coupon status (AJAX)
     */
    public function toggle(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $coupon = $this->couponModel->find($id);

        if (!$coupon || $coupon['store_id'] != $storeId) {
            $this->json(['success' => false, 'message' => 'Coupon not found']);
            return;
        }

        $newStatus = $coupon['status'] ? 0 : 1;
        $this->couponModel->update($id, ['status' => $newStatus]);

        $this->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $newStatus ? 'Coupon activated' : 'Coupon deactivated'
        ]);
    }
}
