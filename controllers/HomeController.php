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
        $sliderModel = new Slider();
        $lookbookModel = new Lookbook();

        // Get main categories with product counts
        $mainCategories = $categoryModel->getParents();
        foreach ($mainCategories as &$cat) {
            $cat['product_count'] = $categoryModel->getProductCount($cat['id']);
        }

        // Get active sliders (gracefully handle if table doesn't exist)
        $storeId = Session::get('store_id', 1);
        try {
            $sliders = $sliderModel->getActive($storeId);
        } catch (\PDOException $e) {
            $sliders = [];
        } catch (\Exception $e) {
            $sliders = [];
        }

        // Get lookbook items (gracefully handle if table doesn't exist)
        try {
            $lookbookItems = $lookbookModel->getActive($storeId, 5);
        } catch (\PDOException $e) {
            $lookbookItems = [];
        } catch (\Exception $e) {
            $lookbookItems = [];
        }

        $data = [
            'pageTitle' => SITE_NAME . ' - Fashion for Everyone',
            'mainCategories' => $mainCategories,
            'featuredProducts' => $productModel->getFeatured(8),
            'newArrivals' => $productModel->getNewArrivals(8),
            'sliders' => $sliders,
            'lookbookItems' => $lookbookItems
        ];

        $this->view('home/index', $data);
    }
}
