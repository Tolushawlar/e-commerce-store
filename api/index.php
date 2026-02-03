<?php

/**
 * API Entry Point
 * All API requests are routed through this file
 */

require_once __DIR__ . '/../backend/bootstrap.php';

\Sentry\captureMessage('Test message from PHP', \Sentry\Severity::info());

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\StoreController;
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Controllers\TemplateController;
use App\Controllers\CategoryController;
use App\Controllers\ImageController;
use App\Controllers\CustomerController;
use App\Controllers\CartController;
use App\Controllers\AddressController;
use App\Controllers\CheckoutController;
use App\Controllers\AdminOrderController;
use App\Controllers\PaymentController;
use App\Middleware\AuthMiddleware;

$router = new Router();

// Authentication Routes (Public)
$router->post('/api/auth/admin/login', [AuthController::class, 'adminLogin']);
$router->post('/api/auth/client/login', [AuthController::class, 'clientLogin']);
$router->post('/api/auth/client/register', [AuthController::class, 'clientRegister']);
$router->get('/api/auth/verify', [AuthController::class, 'verify']);
$router->post('/api/auth/refresh', [AuthController::class, 'refresh']);
$router->post('/api/auth/logout', [AuthController::class, 'logout']);
$router->post('/api/auth/change-password', [AuthController::class, 'changePassword'])
    ->middleware([AuthMiddleware::class, 'handle']);

// Client Routes (Admin Only)
$router->get('/api/clients', [ClientController::class, 'index'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);
$router->get('/api/clients/{id}', [ClientController::class, 'show'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);
$router->post('/api/clients', [ClientController::class, 'store'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);
$router->put('/api/clients/{id}', [ClientController::class, 'update'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);
$router->delete('/api/clients/{id}', [ClientController::class, 'destroy'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);

// Store Routes (Protected - Admin and Client)
$router->get('/api/stores', [StoreController::class, 'index'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->get('/api/stores/{id}', [StoreController::class, 'show'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->post('/api/stores', [StoreController::class, 'store'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);
$router->put('/api/stores/{id}', [StoreController::class, 'update'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->delete('/api/stores/{id}', [StoreController::class, 'destroy'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);
$router->post('/api/stores/{id}/generate', [StoreController::class, 'generate'])
    ->middleware([AuthMiddleware::class, 'handle']);

// Template Routes (Protected)
$router->get('/api/templates', [TemplateController::class, 'index'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->get('/api/templates/{id}', [TemplateController::class, 'show'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->post('/api/templates', [TemplateController::class, 'create'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);
$router->put('/api/templates/{id}', [TemplateController::class, 'update'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);
$router->delete('/api/templates/{id}', [TemplateController::class, 'delete'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);

// Category Routes (Public listing for stores, Protected for management)
$router->get('/api/categories', [CategoryController::class, 'index']); // Public for store display
$router->get('/api/categories/{id}', [CategoryController::class, 'show']); // Public for store display
$router->get('/api/categories/slug/{slug}', [CategoryController::class, 'getBySlug']); // Public
$router->get('/api/categories/popular', [CategoryController::class, 'popular']); // Public
$router->post('/api/categories', [CategoryController::class, 'store'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->put('/api/categories/{id}', [CategoryController::class, 'update'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->delete('/api/categories/{id}', [CategoryController::class, 'destroy'])
    ->middleware([AuthMiddleware::class, 'handle']);

// Product Routes (Public listing for stores, Protected for management)
$router->get('/api/products', [ProductController::class, 'index']); // Public for store display
$router->get('/api/products/{id}', [ProductController::class, 'show']); // Public for store display
$router->post('/api/products', [ProductController::class, 'store'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->put('/api/products/{id}', [ProductController::class, 'update'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->delete('/api/products/{id}', [ProductController::class, 'destroy'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->get('/api/products/low-stock', [ProductController::class, 'lowStock'])
    ->middleware([AuthMiddleware::class, 'handle']);

// Order Routes (Protected)
$router->get('/api/orders', [OrderController::class, 'index'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->get('/api/orders/{id}', [OrderController::class, 'show'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->post('/api/orders', [OrderController::class, 'store'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->put('/api/orders/{id}/status', [OrderController::class, 'updateStatus'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->get('/api/orders/stats', [OrderController::class, 'stats'])
    ->middleware([AuthMiddleware::class, 'handle']);

// Image Upload Routes (Protected)
$router->post('/api/images/upload', [ImageController::class, 'upload'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->post('/api/images/upload-multiple', [ImageController::class, 'uploadMultiple'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->post('/api/images/upload-from-url', [ImageController::class, 'uploadFromUrl'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->delete('/api/images/{publicId}', [ImageController::class, 'delete'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->get('/api/images/{publicId}/details', [ImageController::class, 'getDetails'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->post('/api/images/transform', [ImageController::class, 'transform'])
    ->middleware([AuthMiddleware::class, 'handle']);

// ============================================================================
// CUSTOMER ROUTES (Public - Store-facing)
// Customer authentication, registration, and profile management
// ============================================================================

// Customer Authentication (Public)
$router->post('/api/stores/{store_id}/customers/register', [CustomerController::class, 'register']);
$router->post('/api/stores/{store_id}/customers/login', [CustomerController::class, 'login']);
$router->post('/api/stores/{store_id}/customers/logout', [CustomerController::class, 'logout']);

// Customer Profile (Requires Customer Token)
$router->get('/api/stores/{store_id}/customers/me', [CustomerController::class, 'me'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->put('/api/stores/{store_id}/customers/me', [CustomerController::class, 'updateProfile'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->post('/api/stores/{store_id}/customers/change-password', [CustomerController::class, 'changePassword'])
    ->middleware([AuthMiddleware::class, 'handle']);

// Shopping Cart Routes (Public/Customer Token)
$router->get('/api/stores/{store_id}/cart', [CartController::class, 'index']);
$router->post('/api/stores/{store_id}/cart', [CartController::class, 'addItem']);
$router->put('/api/stores/{store_id}/cart/{item_id}', [CartController::class, 'updateQuantity']);
$router->delete('/api/stores/{store_id}/cart/{item_id}', [CartController::class, 'removeItem']);
$router->delete('/api/stores/{store_id}/cart', [CartController::class, 'clearCart']);
$router->post('/api/stores/{store_id}/cart/sync', [CartController::class, 'syncCart']);

// Customer Address Routes (Requires Customer Token)
$router->get('/api/stores/{store_id}/addresses', [AddressController::class, 'index']);
$router->get('/api/stores/{store_id}/addresses/{id}', [AddressController::class, 'show']);
$router->post('/api/stores/{store_id}/addresses', [AddressController::class, 'store']);
$router->put('/api/stores/{store_id}/addresses/{id}', [AddressController::class, 'update']);
$router->delete('/api/stores/{store_id}/addresses/{id}', [AddressController::class, 'destroy']);
$router->post('/api/stores/{store_id}/addresses/{id}/set-default', [AddressController::class, 'setDefault']);

// Checkout & Order Routes
$router->post('/api/stores/{store_id}/checkout', [CheckoutController::class, 'checkout']); // Public (guest or registered)
$router->get('/api/stores/{store_id}/orders', [CheckoutController::class, 'index']); // Requires token
$router->get('/api/stores/{store_id}/orders/{id}', [CheckoutController::class, 'show']); // Token or email verification
$router->get('/api/stores/{store_id}/orders/track', [CheckoutController::class, 'track']); // Public with email

// ============================================================================
// PAYMENT ROUTES (Paystack Integration)
// Payment initialization, verification, and webhook handling
// ============================================================================

// Payment Configuration (Public - for frontend)
$router->get('/api/stores/{store_id}/payment/config', [PaymentController::class, 'getConfig']);

// Payment Processing (Requires Customer Token)
$router->post('/api/payment/initialize', [PaymentController::class, 'initialize'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->post('/api/payment/verify', [PaymentController::class, 'verify'])
    ->middleware([AuthMiddleware::class, 'handle']);

// Webhook (Public - Paystack callback, no auth)
$router->post('/api/payment/webhook/paystack', [PaymentController::class, 'webhook']);

// ============================================================================
// ADMIN ORDER MANAGEMENT ROUTES (Protected - Admin/Client only)
// Order management for store owners
// ============================================================================

// Admin Order Management (Requires Admin/Client Token)
$router->get('/api/stores/{store_id}/admin/orders', [AdminOrderController::class, 'index'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->get('/api/stores/{store_id}/admin/orders/stats', [AdminOrderController::class, 'stats'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->get('/api/stores/{store_id}/admin/orders/{order_id}', [AdminOrderController::class, 'show'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->put('/api/stores/{store_id}/admin/orders/{order_id}/status', [AdminOrderController::class, 'updateStatus'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->put('/api/stores/{store_id}/admin/orders/{order_id}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->put('/api/stores/{store_id}/admin/orders/{order_id}/tracking', [AdminOrderController::class, 'addTracking'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->post('/api/stores/{store_id}/admin/orders/bulk-update', [AdminOrderController::class, 'bulkUpdate'])
    ->middleware([AuthMiddleware::class, 'handle']);

// API Documentation Routes
$router->get('/api/openapi.json', function () {
    header('Content-Type: application/json');
    readfile(__DIR__ . '/openapi.json');
});

$router->get('/api/docs', function () {
    readfile(__DIR__ . '/docs.html');
});

// Health check
$router->get('/api/health', function () {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'API is running',
        'version' => config('app.version'),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// Resolve the route
$router->resolve();
