<?php
/**
 * User Controller
 */

class UserController extends Controller
{
    private User $userModel;
    private Auth $auth;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->auth = new Auth();
    }

    /**
     * Login page
     */
    public function login(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('account');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->post('email');
            $password = $this->post('password');

            if ($this->auth->attempt($email, $password)) {
                // Merge guest cart
                $cartModel = new Cart();
                $cartModel->mergeGuestCart(session_id(), Session::getUserId());

                // Redirect
                $redirect = Session::get('redirect_after_login', 'account');
                Session::remove('redirect_after_login');
                $this->redirect($redirect, 'Welcome back!');
            } else {
                $this->data['error'] = 'Invalid email or password';
            }
        }

        $data = [
            'pageTitle' => 'Login - ' . SITE_NAME,
            'error' => $this->data['error'] ?? null
        ];

        $this->view('user/login', $data);
    }

    /**
     * Register page
     */
    public function register(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect('account');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $this->post('name'),
                'email' => $this->post('email'),
                'password' => $this->post('password'),
                'phone' => $this->post('phone')
            ];

            $errors = $this->validateRegistration($data);

            if (empty($errors)) {
                $userId = $this->auth->register($data);

                if ($userId) {
                    // Auto login
                    $this->auth->attempt($data['email'], $this->post('password'));

                    // Merge guest cart
                    $cartModel = new Cart();
                    $cartModel->mergeGuestCart(session_id(), $userId);

                    $this->redirect('account', 'Account created successfully!');
                } else {
                    $errors[] = 'Email already exists';
                }
            }

            $this->data['errors'] = $errors;
            $this->data['old'] = $data;
        }

        $data = [
            'pageTitle' => 'Register - ' . SITE_NAME,
            'errors' => $this->data['errors'] ?? [],
            'old' => $this->data['old'] ?? []
        ];

        $this->view('user/register', $data);
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        $this->auth->logout();
        $this->redirect('', 'You have been logged out');
    }

    /**
     * Account dashboard
     */
    public function account(): void
    {
        $this->requireLogin();

        $userId = Session::getUserId();
        $user = $this->userModel->getWithAddresses($userId);
        $recentOrders = $this->userModel->getOrders($userId, 5);
        $stats = $this->userModel->getCustomerStats($userId);

        $data = [
            'pageTitle' => 'My Account - ' . SITE_NAME,
            'user' => $user,
            'recentOrders' => $recentOrders,
            'stats' => $stats
        ];

        $this->view('user/account', $data);
    }

    /**
     * Order history
     */
    public function orders(): void
    {
        $this->requireLogin();

        $orderModel = new Order();
        $page = (int) $this->get('page', 1);
        $orders = $orderModel->getUserOrders(Session::getUserId(), $page);

        $data = [
            'pageTitle' => 'My Orders - ' . SITE_NAME,
            'orders' => $orders
        ];

        $this->view('user/orders', $data);
    }

    /**
     * Order detail
     */
    public function orderDetail(int $id): void
    {
        $this->requireLogin();

        $orderModel = new Order();
        $order = $orderModel->getWithDetails($id);

        if (!$order || $order['user_id'] !== Session::getUserId()) {
            $this->redirect('account/orders', 'Order not found', 'error');
            return;
        }

        $data = [
            'pageTitle' => 'Order ' . $order['order_number'] . ' - ' . SITE_NAME,
            'order' => $order
        ];

        $this->view('user/order-detail', $data);
    }

    /**
     * Profile settings
     */
    public function profile(): void
    {
        $this->requireLogin();

        $userId = Session::getUserId();
        $user = $this->userModel->find($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $this->post('name'),
                'phone' => $this->post('phone')
            ];

            $this->userModel->update($userId, $data);

            // Update session
            $updatedUser = $this->userModel->find($userId);
            Session::setUser($updatedUser);

            $this->redirect('account/profile', 'Profile updated successfully');
        }

        $data = [
            'pageTitle' => 'Profile Settings - ' . SITE_NAME,
            'user' => $user
        ];

        $this->view('user/profile', $data);
    }

    /**
     * Change Password
     */
    public function changePassword(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('account/profile');
            return;
        }

        $userId = Session::getUserId();
        $user = $this->userModel->find($userId);

        $currentPassword = $this->post('current_password');
        $newPassword = $this->post('new_password');
        $confirmPassword = $this->post('confirm_password');

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            $this->redirect('account/profile', 'Current password is incorrect', 'error');
            return;
        }

        // Validate new password
        if (strlen($newPassword) < 6) {
            $this->redirect('account/profile', 'New password must be at least 6 characters', 'error');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->redirect('account/profile', 'New passwords do not match', 'error');
            return;
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->update($userId, ['password' => $hashedPassword]);

        $this->redirect('account/profile', 'Password updated successfully');
    }

    /**
     * Addresses
     */
    public function addresses(): void
    {
        $this->requireLogin();

        $userId = Session::getUserId();
        $user = $this->userModel->getWithAddresses($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $addressData = [
                'label' => $this->post('label', 'Home'),
                'name' => $this->post('name'),
                'phone' => $this->post('phone'),
                'address_line1' => $this->post('address_line1'),
                'address_line2' => $this->post('address_line2'),
                'city' => $this->post('city'),
                'state' => $this->post('state'),
                'postal_code' => $this->post('postal_code'),
                'country' => $this->post('country', 'United States'),
                'is_default' => $this->post('is_default') ? 1 : 0
            ];

            $this->userModel->addAddress($userId, $addressData);
            $this->redirect('account/addresses', 'Address added successfully');
        }

        $data = [
            'pageTitle' => 'My Addresses - ' . SITE_NAME,
            'addresses' => $user['addresses'] ?? []
        ];

        $this->view('user/addresses', $data);
    }

    /**
     * Delete address
     */
    public function deleteAddress(int $id): void
    {
        $this->requireLogin();

        $userId = Session::getUserId();

        // Verify address belongs to user
        $address = $this->db->fetch(
            "SELECT * FROM user_addresses WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );

        if ($address) {
            $this->db->delete('user_addresses', ['id' => $id, 'user_id' => $userId]);
            $this->redirect('account/addresses', 'Address deleted successfully');
        } else {
            $this->redirect('account/addresses', 'Address not found', 'error');
        }
    }

    /**
     * Set default address
     */
    public function setDefaultAddress(int $id): void
    {
        $this->requireLogin();

        $userId = Session::getUserId();

        // Verify address belongs to user
        $address = $this->db->fetch(
            "SELECT * FROM user_addresses WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );

        if ($address) {
            // Remove default from all addresses
            $this->db->query(
                "UPDATE user_addresses SET is_default = 0 WHERE user_id = ?",
                [$userId]
            );

            // Set new default
            $this->db->update('user_addresses', ['is_default' => 1], ['id' => $id]);

            $this->redirect('account/addresses', 'Default address updated');
        } else {
            $this->redirect('account/addresses', 'Address not found', 'error');
        }
    }

    /**
     * Wishlist
     */
    public function wishlist(): void
    {
        $this->requireLogin();

        $wishlist = $this->db->fetchAll(
            "SELECT w.*, p.name, p.slug, p.price, p.sale_price,
                    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) as image
             FROM wishlist w
             JOIN products p ON w.product_id = p.id
             WHERE w.user_id = ?
             ORDER BY w.created_at DESC",
            [Session::getUserId()]
        );

        $data = [
            'pageTitle' => 'Wishlist - ' . SITE_NAME,
            'wishlist' => $wishlist
        ];

        $this->view('user/wishlist', $data);
    }

    /**
     * Add to wishlist (AJAX)
     */
    public function addWishlist(): void
    {
        if (!Session::isLoggedIn()) {
            $this->json(['success' => false, 'redirect' => true]);
            return;
        }

        $productId = (int) $this->post('product_id');
        $userId = Session::getUserId();

        // Check if already in wishlist
        $existing = $this->db->fetch(
            "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        );

        if ($existing) {
            // Remove from wishlist
            $this->db->delete('wishlist', 'id = ?', [$existing['id']]);
            $this->json(['success' => true, 'action' => 'removed']);
        } else {
            // Add to wishlist
            $this->db->insert('wishlist', [
                'user_id' => $userId,
                'product_id' => $productId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $this->json(['success' => true, 'action' => 'added']);
        }
    }

    /**
     * Remove from wishlist
     */
    public function removeWishlist(): void
    {
        $this->requireLogin();

        $productId = (int) $this->post('product_id');
        $this->db->delete(
            'wishlist',
            'user_id = ? AND product_id = ?',
            [Session::getUserId(), $productId]
        );

        $this->redirect('wishlist', 'Item removed from wishlist');
    }

    /**
     * Validate registration data
     */
    private function validateRegistration(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }

        if (strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }

        if ($this->post('password') !== $this->post('password_confirm')) {
            $errors[] = 'Passwords do not match';
        }

        return $errors;
    }
}
