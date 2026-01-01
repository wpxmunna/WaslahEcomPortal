<?php
/**
 * Application Routes
 */

// Home
$router->add('', 'HomeController', 'index');
$router->add('home', 'HomeController', 'index');

// Products
$router->add('shop', 'ProductController', 'index');
$router->add('shop/category/{slug}', 'ProductController', 'category');
$router->add('product/{slug}', 'ProductController', 'show');
$router->add('search', 'ProductController', 'search');

// Cart
$router->add('cart', 'CartController', 'index');
$router->add('cart/add', 'CartController', 'add');
$router->add('cart/update', 'CartController', 'update');
$router->add('cart/remove', 'CartController', 'remove');
$router->add('cart/clear', 'CartController', 'clear');

// Checkout
$router->add('checkout', 'CheckoutController', 'index');
$router->add('checkout/process', 'CheckoutController', 'process');
$router->add('checkout/payment', 'CheckoutController', 'payment');
$router->add('checkout/confirm', 'CheckoutController', 'confirm');
$router->add('order/success/{id}', 'CheckoutController', 'success');
$router->add('order/track/{id}', 'CheckoutController', 'track');

// User
$router->add('login', 'UserController', 'login');
$router->add('register', 'UserController', 'register');
$router->add('logout', 'UserController', 'logout');
$router->add('account', 'UserController', 'account');
$router->add('account/orders', 'UserController', 'orders');
$router->add('account/order/{id}', 'UserController', 'orderDetail');
$router->add('account/profile', 'UserController', 'profile');
$router->add('account/password', 'UserController', 'changePassword');
$router->add('account/addresses', 'UserController', 'addresses');
$router->add('account/addresses/delete/{id}', 'UserController', 'deleteAddress');
$router->add('account/addresses/default/{id}', 'UserController', 'setDefaultAddress');
$router->add('wishlist', 'UserController', 'wishlist');
$router->add('wishlist/add', 'UserController', 'addWishlist');
$router->add('wishlist/remove', 'UserController', 'removeWishlist');

// Admin Routes
$router->add('admin', 'AdminDashboardController', 'index');
$router->add('admin/login', 'AdminDashboardController', 'login');
$router->add('admin/logout', 'AdminDashboardController', 'logout');

// Admin Products
$router->add('admin/products', 'AdminProductController', 'index');
$router->add('admin/products/create', 'AdminProductController', 'create');
$router->add('admin/products/store', 'AdminProductController', 'store');
$router->add('admin/products/edit/{id}', 'AdminProductController', 'edit');
$router->add('admin/products/update/{id}', 'AdminProductController', 'update');
$router->add('admin/products/delete/{id}', 'AdminProductController', 'delete');

// Admin Categories
$router->add('admin/categories', 'AdminCategoryController', 'index');
$router->add('admin/categories/create', 'AdminCategoryController', 'create');
$router->add('admin/categories/store', 'AdminCategoryController', 'store');
$router->add('admin/categories/edit/{id}', 'AdminCategoryController', 'edit');
$router->add('admin/categories/update/{id}', 'AdminCategoryController', 'update');
$router->add('admin/categories/delete/{id}', 'AdminCategoryController', 'delete');

// Admin Orders
$router->add('admin/orders', 'AdminOrderController', 'index');
$router->add('admin/orders/view/{id}', 'AdminOrderController', 'show');
$router->add('admin/orders/status/{id}', 'AdminOrderController', 'updateStatus');
$router->add('admin/orders/invoice/{id}', 'AdminOrderController', 'invoice');
$router->add('admin/orders/pathao/{id}', 'AdminOrderController', 'createPathaoShipment');
$router->add('admin/orders/pathao-status/{id}', 'AdminOrderController', 'pathaoStatus');
$router->add('admin/orders/update-shipping/{id}', 'AdminOrderController', 'updateShipping');
$router->add('admin/orders/save-notes/{id}', 'AdminOrderController', 'saveNotes');

// Admin Customers
$router->add('admin/customers', 'AdminCustomerController', 'index');
$router->add('admin/customers/view/{id}', 'AdminCustomerController', 'show');
$router->add('admin/customers/toggle/{id}', 'AdminCustomerController', 'toggleStatus');

// Admin Stores (Multi-store)
$router->add('admin/stores', 'AdminStoreController', 'index');
$router->add('admin/stores/create', 'AdminStoreController', 'create');
$router->add('admin/stores/store', 'AdminStoreController', 'store');
$router->add('admin/stores/edit/{id}', 'AdminStoreController', 'edit');
$router->add('admin/stores/update/{id}', 'AdminStoreController', 'update');
$router->add('admin/stores/switch/{id}', 'AdminStoreController', 'switchStore');
$router->add('admin/stores/delete/{id}', 'AdminStoreController', 'delete');

// Admin Colors
$router->add('admin/colors', 'AdminColorController', 'index');
$router->add('admin/colors/store', 'AdminColorController', 'store');
$router->add('admin/colors/update/{id}', 'AdminColorController', 'update');
$router->add('admin/colors/delete/{id}', 'AdminColorController', 'delete');
$router->add('admin/colors/reorder', 'AdminColorController', 'reorder');

// Admin Coupons
$router->add('admin/coupons', 'AdminCouponController', 'index');
$router->add('admin/coupons/create', 'AdminCouponController', 'create');
$router->add('admin/coupons/store', 'AdminCouponController', 'store');
$router->add('admin/coupons/edit/{id}', 'AdminCouponController', 'edit');
$router->add('admin/coupons/update/{id}', 'AdminCouponController', 'update');
$router->add('admin/coupons/delete/{id}', 'AdminCouponController', 'delete');
$router->add('admin/coupons/toggle/{id}', 'AdminCouponController', 'toggle');

// Admin Couriers
$router->add('admin/couriers', 'AdminCourierController', 'index');
$router->add('admin/couriers/create', 'AdminCourierController', 'create');
$router->add('admin/couriers/store', 'AdminCourierController', 'store');
$router->add('admin/couriers/edit/{id}', 'AdminCourierController', 'edit');
$router->add('admin/couriers/update/{id}', 'AdminCourierController', 'update');
$router->add('admin/couriers/delete/{id}', 'AdminCourierController', 'delete');

// Admin Payments
$router->add('admin/payments', 'AdminPaymentController', 'index');
$router->add('admin/payments/settings', 'AdminPaymentController', 'settings');

// Admin Reports
$router->add('admin/reports', 'AdminReportController', 'index');
$router->add('admin/reports/sales', 'AdminReportController', 'sales');
$router->add('admin/reports/products', 'AdminReportController', 'products');
$router->add('admin/reports/customers', 'AdminReportController', 'customers');
$router->add('admin/reports/export', 'AdminReportController', 'export');

// Admin Pathao Courier
$router->add('admin/pathao', 'AdminPathaoController', 'index');
$router->add('admin/pathao/update', 'AdminPathaoController', 'update');
$router->add('admin/pathao/test', 'AdminPathaoController', 'test');
$router->add('admin/pathao/force-enable', 'AdminPathaoController', 'forceEnable');
$router->add('admin/pathao/stores', 'AdminPathaoController', 'stores');
$router->add('admin/pathao/cities', 'AdminPathaoController', 'cities');
$router->add('admin/pathao/zones/{cityId}', 'AdminPathaoController', 'zones');
$router->add('admin/pathao/areas/{zoneId}', 'AdminPathaoController', 'areas');

// Admin Returns
$router->add('admin/returns', 'AdminReturnController', 'index');
$router->add('admin/returns/create', 'AdminReturnController', 'create');
$router->add('admin/returns/store', 'AdminReturnController', 'store');
$router->add('admin/returns/show/{id}', 'AdminReturnController', 'show');
$router->add('admin/returns/updateRefund/{id}', 'AdminReturnController', 'updateRefund');
$router->add('admin/returns/saveNotes/{id}', 'AdminReturnController', 'saveNotes');
$router->add('admin/returns/delete/{id}', 'AdminReturnController', 'delete');

// Admin Users (Admin/Manager Management)
$router->add('admin/users', 'AdminUserController', 'index');
$router->add('admin/users/create', 'AdminUserController', 'create');
$router->add('admin/users/store', 'AdminUserController', 'store');
$router->add('admin/users/edit/{id}', 'AdminUserController', 'edit');
$router->add('admin/users/update/{id}', 'AdminUserController', 'update');
$router->add('admin/users/delete/{id}', 'AdminUserController', 'delete');
$router->add('admin/users/toggle/{id}', 'AdminUserController', 'toggle');

// Admin Settings
$router->add('admin/settings', 'AdminSettingsController', 'index');
$router->add('admin/settings/update', 'AdminSettingsController', 'update');
$router->add('admin/settings/payment', 'AdminSettingsController', 'payment');
$router->add('admin/settings/payment/update', 'AdminSettingsController', 'updatePayment');
$router->add('admin/settings/email', 'AdminSettingsController', 'email');
$router->add('admin/settings/email/update', 'AdminSettingsController', 'updateEmail');

// API endpoints for AJAX
$router->add('api/cart/count', 'CartController', 'count');
$router->add('api/products/filter', 'ProductController', 'filter');
$router->add('api/coupon/apply', 'CheckoutController', 'applyCoupon');
