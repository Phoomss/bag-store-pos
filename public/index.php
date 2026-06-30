<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Helpers\Session;

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Start secure session
Session::start();

// Set default Timezone
date_default_timezone_set($_ENV['TIMEZONE'] ?? 'Asia/Bangkok');

// Instantiate request, response and router
$request = new Request();
$response = new Response();
$router = new Router($request, $response);

// Middlewares
$auth = App\Middleware\AuthMiddleware::class;
$csrf = App\Middleware\CsrfMiddleware::class;

// --- ROUTES ---

// Authentication routes
$router->get('/login', [App\Controllers\AuthController::class, 'loginView']);
$router->post('/login', [App\Controllers\AuthController::class, 'login']);
$router->get('/logout', [App\Controllers\AuthController::class, 'logout']);

// Dashboard
$router->get('/', [App\Controllers\DashboardController::class, 'index'], [$auth]);
$router->get('/api/dashboard/stats', [App\Controllers\DashboardController::class, 'getStats'], [$auth]);

// Categories
$router->get('/categories', [App\Controllers\CategoryController::class, 'index'], [$auth]);
$router->post('/categories/create', [App\Controllers\CategoryController::class, 'create'], [$auth, $csrf]);
$router->post('/categories/update/{id}', [App\Controllers\CategoryController::class, 'update'], [$auth, $csrf]);
$router->post('/categories/delete/{id}', [App\Controllers\CategoryController::class, 'delete'], [$auth, $csrf]);

// Brands
$router->get('/brands', [App\Controllers\BrandController::class, 'index'], [$auth]);
$router->post('/brands/create', [App\Controllers\BrandController::class, 'create'], [$auth, $csrf]);
$router->post('/brands/update/{id}', [App\Controllers\BrandController::class, 'update'], [$auth, $csrf]);
$router->post('/brands/delete/{id}', [App\Controllers\BrandController::class, 'delete'], [$auth, $csrf]);

// Products
$router->get('/products', [App\Controllers\ProductController::class, 'index'], [$auth]);
$router->get('/products/create', [App\Controllers\ProductController::class, 'createView'], [$auth]);
$router->post('/products/create', [App\Controllers\ProductController::class, 'create'], [$auth, $csrf]);
$router->get('/products/edit/{id}', [App\Controllers\ProductController::class, 'editView'], [$auth]);
$router->post('/products/update/{id}', [App\Controllers\ProductController::class, 'update'], [$auth, $csrf]);
$router->post('/products/delete/{id}', [App\Controllers\ProductController::class, 'delete'], [$auth, $csrf]);
$router->get('/api/products/search', [App\Controllers\ProductController::class, 'search'], [$auth]);
$router->get('/api/products/barcode/{barcode}', [App\Controllers\ProductController::class, 'getByBarcode'], [$auth]);

// Suppliers
$router->get('/suppliers', [App\Controllers\SupplierController::class, 'index'], [$auth]);
$router->post('/suppliers/create', [App\Controllers\SupplierController::class, 'create'], [$auth, $csrf]);
$router->post('/suppliers/update/{id}', [App\Controllers\SupplierController::class, 'update'], [$auth, $csrf]);
$router->post('/suppliers/delete/{id}', [App\Controllers\SupplierController::class, 'delete'], [$auth, $csrf]);
$router->get('/suppliers/{id}/history', [App\Controllers\SupplierController::class, 'history'], [$auth]);

// Customers
$router->get('/customers', [App\Controllers\CustomerController::class, 'index'], [$auth]);
$router->post('/customers/create', [App\Controllers\CustomerController::class, 'create'], [$auth, $csrf]);
$router->post('/customers/update/{id}', [App\Controllers\CustomerController::class, 'update'], [$auth, $csrf]);
$router->post('/customers/delete/{id}', [App\Controllers\CustomerController::class, 'delete'], [$auth, $csrf]);
$router->get('/api/customers/search', [App\Controllers\CustomerController::class, 'search'], [$auth]);

// Purchases
$router->get('/purchases', [App\Controllers\PurchaseController::class, 'index'], [$auth]);
$router->get('/purchases/create', [App\Controllers\PurchaseController::class, 'createView'], [$auth]);
$router->post('/purchases/store', [App\Controllers\PurchaseController::class, 'store'], [$auth, $csrf]);
$router->get('/purchases/view/{id}', [App\Controllers\PurchaseController::class, 'viewPurchase'], [$auth]);
$router->post('/purchases/pay/{id}', [App\Controllers\PurchaseController::class, 'addPayment'], [$auth, $csrf]);
$router->post('/purchases/status/{id}', [App\Controllers\PurchaseController::class, 'updateStatus'], [$auth, $csrf]);

// Inventory
$router->get('/inventory', [App\Controllers\InventoryController::class, 'index'], [$auth]);
$router->post('/inventory/adjust', [App\Controllers\InventoryController::class, 'adjust'], [$auth, $csrf]);
$router->get('/inventory/movements', [App\Controllers\InventoryController::class, 'movement'], [$auth]);
$router->get('/inventory/audit', [App\Controllers\InventoryController::class, 'audit'], [$auth]);

// POS Screen
$router->get('/pos', [App\Controllers\PosController::class, 'index'], [$auth]);
$router->post('/pos/checkout', [App\Controllers\PosController::class, 'checkout'], [$auth, $csrf]);
$router->get('/pos/receipt/{id}', [App\Controllers\PosController::class, 'receipt'], [$auth]);
$router->post('/pos/hold', [App\Controllers\PosController::class, 'holdSale'], [$auth, $csrf]);
$router->get('/pos/held', [App\Controllers\PosController::class, 'listHeldSales'], [$auth]);
$router->post('/pos/resume', [App\Controllers\PosController::class, 'resumeSale'], [$auth, $csrf]);

// Sales
$router->get('/sales', [App\Controllers\SalesController::class, 'index'], [$auth]);
$router->get('/sales/view/{id}', [App\Controllers\SalesController::class, 'viewInvoice'], [$auth]);
$router->post('/sales/refund/{id}', [App\Controllers\SalesController::class, 'refund'], [$auth, $csrf]);

// Reports
$router->get('/reports', [App\Controllers\ReportController::class, 'index'], [$auth]);

// Expenses
$router->get('/expenses', [App\Controllers\ExpenseController::class, 'index'], [$auth]);
$router->post('/expenses/create', [App\Controllers\ExpenseController::class, 'create'], [$auth, $csrf]);
$router->post('/expenses/delete/{id}', [App\Controllers\ExpenseController::class, 'delete'], [$auth, $csrf]);

// Users
$router->get('/users', [App\Controllers\UserController::class, 'index'], [$auth]);
$router->post('/users/create', [App\Controllers\UserController::class, 'create'], [$auth, $csrf]);
$router->post('/users/update/{id}', [App\Controllers\UserController::class, 'update'], [$auth, $csrf]);
$router->post('/users/delete/{id}', [App\Controllers\UserController::class, 'delete'], [$auth, $csrf]);
$router->get('/users/logs', [App\Controllers\UserController::class, 'logs'], [$auth]);

// Settings
$router->get('/settings', [App\Controllers\SettingsController::class, 'index'], [$auth]);
$router->post('/settings/update', [App\Controllers\SettingsController::class, 'update'], [$auth, $csrf]);

// Resolve the route
$router->resolve();
