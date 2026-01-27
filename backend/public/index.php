<?php

/**
 * API Entry Point
 * All API requests are routed through this file
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\StoreController;
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Controllers\TemplateController;
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
