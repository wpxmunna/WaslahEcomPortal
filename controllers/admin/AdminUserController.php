<?php
/**
 * Admin User Controller - Manage Admin & Manager Users
 */

class AdminUserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        requireFullAdmin(); // Only full admins can manage admin users
        $this->userModel = new User();
    }

    /**
     * List all admin/manager users
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $users = $this->userModel->getAdminUsers($page, 20);

        $this->view('admin/users/index', [
            'pageTitle' => 'Admin Users - Admin',
            'users' => $users['data'],
            'pagination' => $users
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->view('admin/users/create', [
            'pageTitle' => 'Add Admin User - Admin',
            'roles' => ['admin' => 'Administrator', 'manager' => 'Manager']
        ], 'admin');
    }

    /**
     * Store new admin user
     */
    public function store(): void
    {
        $name = trim($this->post('name'));
        $email = trim($this->post('email'));
        $phone = trim($this->post('phone'));
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm_password');
        $role = $this->post('role', 'manager');
        $status = $this->post('status') ? 1 : 0;

        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            $this->redirect('admin/users/create', 'Name, email and password are required', 'error');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('admin/users/create', 'Please enter a valid email address', 'error');
            return;
        }

        if (strlen($password) < 6) {
            $this->redirect('admin/users/create', 'Password must be at least 6 characters', 'error');
            return;
        }

        if ($password !== $confirmPassword) {
            $this->redirect('admin/users/create', 'Passwords do not match', 'error');
            return;
        }

        if ($this->userModel->emailExists($email)) {
            $this->redirect('admin/users/create', 'Email address already exists', 'error');
            return;
        }

        if (!in_array($role, ['admin', 'manager'])) {
            $role = 'manager';
        }

        $userId = $this->userModel->createAdmin([
            'store_id' => Session::get('admin_store_id', 1),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => $role,
            'status' => $status
        ]);

        if ($userId) {
            $this->redirect('admin/users', 'Admin user created successfully');
        } else {
            $this->redirect('admin/users/create', 'Failed to create admin user', 'error');
        }
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $user = $this->userModel->find($id);

        if (!$user || !in_array($user['role'], ['admin', 'manager'])) {
            $this->redirect('admin/users', 'User not found', 'error');
            return;
        }

        $currentUserId = Session::getUserId();

        $this->view('admin/users/edit', [
            'pageTitle' => 'Edit Admin User - Admin',
            'user' => $user,
            'roles' => ['admin' => 'Administrator', 'manager' => 'Manager'],
            'isCurrentUser' => ($user['id'] == $currentUserId)
        ], 'admin');
    }

    /**
     * Update admin user
     */
    public function update(int $id): void
    {
        $user = $this->userModel->find($id);

        if (!$user || !in_array($user['role'], ['admin', 'manager'])) {
            $this->redirect('admin/users', 'User not found', 'error');
            return;
        }

        $currentUserId = Session::getUserId();
        $name = trim($this->post('name'));
        $email = trim($this->post('email'));
        $phone = trim($this->post('phone'));
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm_password');
        $role = $this->post('role', 'manager');
        $status = $this->post('status') ? 1 : 0;

        // Validation
        if (empty($name) || empty($email)) {
            $this->redirect('admin/users/edit/' . $id, 'Name and email are required', 'error');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('admin/users/edit/' . $id, 'Please enter a valid email address', 'error');
            return;
        }

        if (!empty($password)) {
            if (strlen($password) < 6) {
                $this->redirect('admin/users/edit/' . $id, 'Password must be at least 6 characters', 'error');
                return;
            }
            if ($password !== $confirmPassword) {
                $this->redirect('admin/users/edit/' . $id, 'Passwords do not match', 'error');
                return;
            }
        }

        if ($this->userModel->emailExists($email, $id)) {
            $this->redirect('admin/users/edit/' . $id, 'Email address already exists', 'error');
            return;
        }

        // Prevent user from demoting themselves
        if ($user['id'] == $currentUserId && $role !== 'admin') {
            $this->redirect('admin/users/edit/' . $id, 'You cannot change your own role', 'error');
            return;
        }

        // Prevent user from deactivating themselves
        if ($user['id'] == $currentUserId && !$status) {
            $this->redirect('admin/users/edit/' . $id, 'You cannot deactivate your own account', 'error');
            return;
        }

        if (!in_array($role, ['admin', 'manager'])) {
            $role = 'manager';
        }

        $updateData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'status' => $status
        ];

        if (!empty($password)) {
            $updateData['password'] = $password;
        }

        $this->userModel->updateAdmin($id, $updateData);
        $this->redirect('admin/users', 'Admin user updated successfully');
    }

    /**
     * Delete admin user
     */
    public function delete(int $id): void
    {
        $user = $this->userModel->find($id);

        if (!$user || !in_array($user['role'], ['admin', 'manager'])) {
            $this->redirect('admin/users', 'User not found', 'error');
            return;
        }

        // Prevent self-deletion
        if ($user['id'] == Session::getUserId()) {
            $this->redirect('admin/users', 'You cannot delete your own account', 'error');
            return;
        }

        $this->userModel->delete($id);
        $this->redirect('admin/users', 'Admin user deleted successfully');
    }

    /**
     * Toggle user status (AJAX)
     */
    public function toggle(int $id): void
    {
        $user = $this->userModel->find($id);

        if (!$user || !in_array($user['role'], ['admin', 'manager'])) {
            $this->json(['success' => false, 'message' => 'User not found']);
            return;
        }

        // Prevent self-toggle
        if ($user['id'] == Session::getUserId()) {
            $this->json(['success' => false, 'message' => 'You cannot deactivate your own account']);
            return;
        }

        $newStatus = $user['status'] ? 0 : 1;
        $this->userModel->update($id, ['status' => $newStatus]);

        $this->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $newStatus ? 'User activated' : 'User deactivated'
        ]);
    }
}
