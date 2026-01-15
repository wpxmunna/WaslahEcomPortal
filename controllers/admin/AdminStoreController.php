<?php
/**
 * Admin Store Controller (Multi-store Management)
 */

class AdminStoreController extends Controller
{
    private Store $storeModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        requireFullAdmin(); // Only full admins can manage stores
        $this->storeModel = new Store();
    }

    /**
     * List stores
     */
    public function index(): void
    {
        $stores = $this->storeModel->all('name', 'ASC');

        // Add stats to each store
        foreach ($stores as &$store) {
            $store['stats'] = $this->storeModel->getStats($store['id']);
        }

        $data = [
            'pageTitle' => 'Stores - Admin',
            'stores' => $stores,
            'allStores' => $stores
        ];

        $this->view('admin/stores/index', $data, 'admin');
    }

    /**
     * Create store form
     */
    public function create(): void
    {
        $data = [
            'pageTitle' => 'Create Store - Admin',
            'stores' => $this->storeModel->getActive()
        ];

        $this->view('admin/stores/create', $data, 'admin');
    }

    /**
     * Store new store
     */
    public function store(): void
    {
        $data = [
            'name' => $this->post('name'),
            'slug' => $this->post('slug') ?: slugify($this->post('name')),
            'description' => $this->post('description'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'address' => $this->post('address'),
            'tax_rate' => (float) $this->post('tax_rate', 0),
            'status' => $this->post('status') ? 1 : 0
            // Note: currency_code and currency_symbol removed - now configured in config/config.php
        ];

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $logoPath = $this->uploadFile($_FILES['logo'], 'stores');
            if ($logoPath) {
                $data['logo'] = $logoPath;
            }
        }

        $storeId = $this->storeModel->create($data);

        // Clone categories from default store
        if ($this->post('clone_categories')) {
            $this->cloneCategories(1, $storeId);
        }

        $this->redirect('admin/stores', 'Store created successfully');
    }

    /**
     * Edit store form
     */
    public function edit(int $id): void
    {
        $store = $this->storeModel->find($id);

        if (!$store) {
            $this->redirect('admin/stores', 'Store not found', 'error');
            return;
        }

        $data = [
            'pageTitle' => 'Edit Store - Admin',
            'store' => $store,
            'settings' => $this->storeModel->getSettings($id),
            'stores' => $this->storeModel->getActive()
        ];

        $this->view('admin/stores/edit', $data, 'admin');
    }

    /**
     * Update store
     */
    public function update(int $id): void
    {
        $data = [
            'name' => $this->post('name'),
            'slug' => $this->post('slug') ?: slugify($this->post('name')),
            'description' => $this->post('description'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'address' => $this->post('address'),
            'tax_rate' => (float) $this->post('tax_rate', 0),
            'status' => $this->post('status') ? 1 : 0
            // Note: currency_code and currency_symbol removed - now configured in config/config.php
        ];

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $logoPath = $this->uploadFile($_FILES['logo'], 'stores');
            if ($logoPath) {
                $data['logo'] = $logoPath;
            }
        }

        $this->storeModel->update($id, $data);

        // Set as default if requested
        if ($this->post('is_default')) {
            $this->storeModel->setDefault($id);
        }

        $this->redirect('admin/stores/edit/' . $id, 'Store updated successfully');
    }

    /**
     * Switch active store
     */
    public function switchStore(int $id): void
    {
        $store = $this->storeModel->find($id);

        if ($store) {
            Session::set('current_store_id', $id);
            Session::setFlash('Switched to ' . $store['name'], 'success');
        }

        // Redirect back to referring page
        $referer = $_SERVER['HTTP_REFERER'] ?? url('admin');
        header("Location: {$referer}");
        exit;
    }

    /**
     * Delete store
     */
    public function delete(int $id): void
    {
        $store = $this->storeModel->find($id);

        if (!$store) {
            $this->redirect('admin/stores', 'Store not found', 'error');
            return;
        }

        if ($store['is_default']) {
            $this->redirect('admin/stores', 'Cannot delete default store', 'error');
            return;
        }

        $this->storeModel->delete($id);
        $this->redirect('admin/stores', 'Store deleted successfully');
    }

    /**
     * Clone categories from one store to another
     */
    private function cloneCategories(int $sourceStoreId, int $targetStoreId): void
    {
        $categories = $this->db->fetchAll(
            "SELECT * FROM categories WHERE store_id = ? AND parent_id IS NULL",
            [$sourceStoreId]
        );

        foreach ($categories as $cat) {
            $newCat = $cat;
            unset($newCat['id']);
            $newCat['store_id'] = $targetStoreId;
            $newCat['created_at'] = date('Y-m-d H:i:s');
            $newCat['updated_at'] = date('Y-m-d H:i:s');

            $newCatId = $this->db->insert('categories', $newCat);

            // Clone children
            $children = $this->db->fetchAll(
                "SELECT * FROM categories WHERE parent_id = ?",
                [$cat['id']]
            );

            foreach ($children as $child) {
                $newChild = $child;
                unset($newChild['id']);
                $newChild['store_id'] = $targetStoreId;
                $newChild['parent_id'] = $newCatId;
                $newChild['created_at'] = date('Y-m-d H:i:s');
                $newChild['updated_at'] = date('Y-m-d H:i:s');

                $this->db->insert('categories', $newChild);
            }
        }
    }
}
