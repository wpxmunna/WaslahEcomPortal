<?php
/**
 * Admin Color Controller
 * Manage product colors
 */

class AdminColorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    /**
     * List all colors
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $colors = $this->db->fetchAll(
            "SELECT * FROM product_colors WHERE store_id = ? ORDER BY sort_order, name",
            [$storeId]
        );

        $data = [
            'pageTitle' => 'Product Colors - Admin',
            'colors' => $colors
        ];

        $this->view('admin/colors/index', $data, 'admin');
    }

    /**
     * Store new color
     */
    public function store(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $name = trim($this->post('name'));
        $colorCode = trim($this->post('color_code'));

        if (empty($name) || empty($colorCode)) {
            $this->redirect('admin/colors', 'Name and color code are required', 'error');
            return;
        }

        // Check if color exists
        $existing = $this->db->fetch(
            "SELECT id FROM product_colors WHERE name = ? AND store_id = ?",
            [$name, $storeId]
        );

        if ($existing) {
            $this->redirect('admin/colors', 'Color already exists', 'error');
            return;
        }

        // Get max sort order
        $maxSort = $this->db->fetch(
            "SELECT MAX(sort_order) as max_sort FROM product_colors WHERE store_id = ?",
            [$storeId]
        );

        $this->db->insert('product_colors', [
            'store_id' => $storeId,
            'name' => $name,
            'color_code' => $colorCode,
            'sort_order' => ($maxSort['max_sort'] ?? 0) + 1,
            'status' => 1
        ]);

        $this->redirect('admin/colors', 'Color added successfully');
    }

    /**
     * Update color
     */
    public function update(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $name = trim($this->post('name'));
        $colorCode = trim($this->post('color_code'));
        $status = $this->post('status') ? 1 : 0;

        if (empty($name) || empty($colorCode)) {
            $this->redirect('admin/colors', 'Name and color code are required', 'error');
            return;
        }

        $this->db->update('product_colors', [
            'name' => $name,
            'color_code' => $colorCode,
            'status' => $status
        ], 'id = ? AND store_id = ?', [$id, $storeId]);

        $this->redirect('admin/colors', 'Color updated successfully');
    }

    /**
     * Delete color
     */
    public function delete(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);

        // Check if color is used in any product variants
        $usage = $this->db->fetch(
            "SELECT COUNT(*) as count FROM product_variants WHERE color_id = ?",
            [$id]
        );

        if ($usage && $usage['count'] > 0) {
            $this->redirect('admin/colors', 'Cannot delete color. It is used in ' . $usage['count'] . ' product variant(s)', 'error');
            return;
        }

        $this->db->delete('product_colors', 'id = ? AND store_id = ?', [$id, $storeId]);

        $this->redirect('admin/colors', 'Color deleted successfully');
    }

    /**
     * Update sort order (AJAX)
     */
    public function reorder(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $order = $this->post('order', []);

        foreach ($order as $position => $id) {
            $this->db->update('product_colors', [
                'sort_order' => $position + 1
            ], 'id = ? AND store_id = ?', [$id, $storeId]);
        }

        $this->json(['success' => true]);
    }
}
