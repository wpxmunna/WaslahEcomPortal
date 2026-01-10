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
$router->add('admin/products/upload-images/{id}', 'AdminProductController', 'uploadImages');
$router->add('admin/products/delete-image/{id}', 'AdminProductController', 'deleteImage');
$router->add('admin/products/set-primary/{productId}/{imageId}', 'AdminProductController', 'setPrimary');

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

// Admin Sliders
$router->add('admin/sliders', 'AdminSliderController', 'index');
$router->add('admin/sliders/create', 'AdminSliderController', 'create');
$router->add('admin/sliders/store', 'AdminSliderController', 'store');
$router->add('admin/sliders/edit/{id}', 'AdminSliderController', 'edit');
$router->add('admin/sliders/update/{id}', 'AdminSliderController', 'update');
$router->add('admin/sliders/delete/{id}', 'AdminSliderController', 'delete');
$router->add('admin/sliders/toggle/{id}', 'AdminSliderController', 'toggle');
$router->add('admin/sliders/order', 'AdminSliderController', 'updateOrder');

// Admin Lookbook
$router->add('admin/lookbook', 'AdminLookbookController', 'index');
$router->add('admin/lookbook/create', 'AdminLookbookController', 'create');
$router->add('admin/lookbook/store', 'AdminLookbookController', 'store');
$router->add('admin/lookbook/edit/{id}', 'AdminLookbookController', 'edit');
$router->add('admin/lookbook/update/{id}', 'AdminLookbookController', 'update');
$router->add('admin/lookbook/delete/{id}', 'AdminLookbookController', 'delete');
$router->add('admin/lookbook/toggle/{id}', 'AdminLookbookController', 'toggle');
$router->add('admin/lookbook/featured/{id}', 'AdminLookbookController', 'featured');

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

// Admin Expenses (ERP)
$router->add('admin/expenses', 'AdminExpenseController', 'index');
$router->add('admin/expenses/create', 'AdminExpenseController', 'create');
$router->add('admin/expenses/store', 'AdminExpenseController', 'store');
$router->add('admin/expenses/edit/{id}', 'AdminExpenseController', 'edit');
$router->add('admin/expenses/update/{id}', 'AdminExpenseController', 'update');
$router->add('admin/expenses/delete/{id}', 'AdminExpenseController', 'delete');
$router->add('admin/expenses/categories', 'AdminExpenseController', 'categories');
$router->add('admin/expenses/categories/store', 'AdminExpenseController', 'storeCategory');
$router->add('admin/expenses/categories/update/{id}', 'AdminExpenseController', 'updateCategory');
$router->add('admin/expenses/categories/delete/{id}', 'AdminExpenseController', 'deleteCategory');

// Admin Suppliers (ERP)
$router->add('admin/suppliers', 'AdminSupplierController', 'index');
$router->add('admin/suppliers/create', 'AdminSupplierController', 'create');
$router->add('admin/suppliers/store', 'AdminSupplierController', 'store');
$router->add('admin/suppliers/view/{id}', 'AdminSupplierController', 'show');
$router->add('admin/suppliers/edit/{id}', 'AdminSupplierController', 'edit');
$router->add('admin/suppliers/update/{id}', 'AdminSupplierController', 'update');
$router->add('admin/suppliers/delete/{id}', 'AdminSupplierController', 'delete');

// Admin Purchase Orders (ERP)
$router->add('admin/purchase-orders', 'AdminPurchaseOrderController', 'index');
$router->add('admin/purchase-orders/create', 'AdminPurchaseOrderController', 'create');
$router->add('admin/purchase-orders/store', 'AdminPurchaseOrderController', 'store');
$router->add('admin/purchase-orders/view/{id}', 'AdminPurchaseOrderController', 'show');
$router->add('admin/purchase-orders/edit/{id}', 'AdminPurchaseOrderController', 'edit');
$router->add('admin/purchase-orders/update/{id}', 'AdminPurchaseOrderController', 'update');
$router->add('admin/purchase-orders/receive/{id}', 'AdminPurchaseOrderController', 'receive');
$router->add('admin/purchase-orders/process-receipt/{id}', 'AdminPurchaseOrderController', 'processReceipt');
$router->add('admin/purchase-orders/cancel/{id}', 'AdminPurchaseOrderController', 'cancel');
$router->add('admin/purchase-orders/payment/{id}', 'AdminPurchaseOrderController', 'addPayment');

// Admin Accounting (ERP)
$router->add('admin/accounting', 'AdminAccountingController', 'index');
$router->add('admin/accounting/accounts', 'AdminAccountingController', 'accounts');
$router->add('admin/accounting/accounts/store', 'AdminAccountingController', 'storeAccount');
$router->add('admin/accounting/journal', 'AdminAccountingController', 'journal');
$router->add('admin/accounting/journal/create', 'AdminAccountingController', 'createEntry');
$router->add('admin/accounting/journal/store', 'AdminAccountingController', 'storeEntry');

// Admin Financial Reports (ERP)
$router->add('admin/finance-reports', 'AdminFinanceReportController', 'index');
$router->add('admin/finance-reports/profit-loss', 'AdminFinanceReportController', 'profitLoss');
$router->add('admin/finance-reports/cash-flow', 'AdminFinanceReportController', 'cashFlow');
$router->add('admin/finance-reports/expenses', 'AdminFinanceReportController', 'expenseReport');
$router->add('admin/finance-reports/export/{type}', 'AdminFinanceReportController', 'export');

// Admin POS (Point of Sale)
$router->add('admin/pos', 'AdminPOSController', 'terminal');
$router->add('admin/pos/terminal', 'AdminPOSController', 'terminal');
$router->add('admin/pos/open-shift', 'AdminPOSController', 'openShift');
$router->add('admin/pos/close-shift', 'AdminPOSController', 'closeShift');
$router->add('admin/pos/sale', 'AdminPOSController', 'sale');
$router->add('admin/pos/receipt/{id}', 'AdminPOSController', 'receipt');
$router->add('admin/pos/transactions', 'AdminPOSController', 'transactions');
$router->add('admin/pos/shifts', 'AdminPOSController', 'shifts');
$router->add('admin/pos/shift/{id}', 'AdminPOSController', 'shiftDetails');
$router->add('admin/pos/cash', 'AdminPOSController', 'cashManagement');
$router->add('admin/pos/hold-order', 'AdminPOSController', 'holdOrder');
$router->add('admin/pos/held-orders', 'AdminPOSController', 'getHeldOrders');
$router->add('admin/pos/recall-order/{id}', 'AdminPOSController', 'recallOrder');
$router->add('admin/pos/delete-held/{id}', 'AdminPOSController', 'deleteHeldOrder');
$router->add('admin/pos/search-customers', 'AdminPOSController', 'searchCustomers');
$router->add('admin/pos/barcode-lookup', 'AdminPOSController', 'barcodeLookup');
$router->add('admin/pos/refund', 'AdminPOSController', 'refund');
$router->add('admin/pos/search-transaction', 'AdminPOSController', 'searchTransaction');
$router->add('admin/pos/process-refund', 'AdminPOSController', 'processRefund');
$router->add('admin/pos/daily-summary', 'AdminPOSController', 'getDailySummary');

// Admin HR - Employees
$router->add('admin/employees', 'AdminEmployeeController', 'index');
$router->add('admin/employees/create', 'AdminEmployeeController', 'create');
$router->add('admin/employees/store', 'AdminEmployeeController', 'store');
$router->add('admin/employees/view/{id}', 'AdminEmployeeController', 'show');
$router->add('admin/employees/edit/{id}', 'AdminEmployeeController', 'edit');
$router->add('admin/employees/update/{id}', 'AdminEmployeeController', 'update');
$router->add('admin/employees/departments', 'AdminEmployeeController', 'departments');
$router->add('admin/employees/departments/store', 'AdminEmployeeController', 'storeDepartment');

// Admin HR - Attendance
$router->add('admin/attendance', 'AdminAttendanceController', 'index');
$router->add('admin/attendance/today', 'AdminAttendanceController', 'today');
$router->add('admin/attendance/check-in', 'AdminAttendanceController', 'checkIn');
$router->add('admin/attendance/check-out', 'AdminAttendanceController', 'checkOut');
$router->add('admin/attendance/bulk-check-in', 'AdminAttendanceController', 'bulkCheckIn');
$router->add('admin/attendance/mark-absent', 'AdminAttendanceController', 'markAbsent');
$router->add('admin/attendance/monthly-report', 'AdminAttendanceController', 'monthlyReport');

// Admin HR - Payroll
$router->add('admin/payroll', 'AdminPayrollController', 'index');
$router->add('admin/payroll/create', 'AdminPayrollController', 'create');
$router->add('admin/payroll/store', 'AdminPayrollController', 'store');
$router->add('admin/payroll/view/{id}', 'AdminPayrollController', 'show');
$router->add('admin/payroll/process/{id}', 'AdminPayrollController', 'process');
$router->add('admin/payroll/approve/{id}', 'AdminPayrollController', 'approve');
$router->add('admin/payroll/mark-paid/{id}', 'AdminPayrollController', 'markPaid');
$router->add('admin/payroll/payslip/{id}', 'AdminPayrollController', 'payslip');
$router->add('admin/payroll/components', 'AdminPayrollController', 'components');
$router->add('admin/payroll/components/store', 'AdminPayrollController', 'storeComponent');
$router->add('admin/payroll/employee-salary/{id}', 'AdminPayrollController', 'employeeSalary');
$router->add('admin/payroll/employee-salary/update/{id}', 'AdminPayrollController', 'updateEmployeeSalary');

// API endpoints for AJAX
$router->add('api/cart/count', 'CartController', 'count');
$router->add('api/products/filter', 'ProductController', 'filter');
$router->add('api/coupon/apply', 'CheckoutController', 'applyCoupon');
