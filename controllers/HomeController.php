<?php
/**
 * Home Controller
 */

class HomeController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();
        $categoryModel = new Category();

        // Get main categories with product counts
        $mainCategories = $categoryModel->getParents();
        foreach ($mainCategories as &$cat) {
            $cat['product_count'] = $categoryModel->getProductCount($cat['id']);
        }

        $data = [
            'pageTitle' => SITE_NAME . ' - Fashion for Everyone',
            'mainCategories' => $mainCategories,
            'featuredProducts' => $productModel->getFeatured(8),
            'newArrivals' => $productModel->getNewArrivals(8)
        ];

        $this->view('home/index', $data);
    }
}
