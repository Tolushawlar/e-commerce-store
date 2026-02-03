<?php

/**
 * Generate OpenAPI specification from annotations
 * Run this file to generate openapi.json
 */

// Suppress warnings during generation
error_reporting(0);
ini_set('display_errors', '0');

require_once __DIR__ . '/../vendor/autoload.php';

use OpenApi\Generator;

// Use absolute paths to avoid path resolution issues
$baseDir = dirname(__DIR__);
$scanPaths = [
    $baseDir . '/backend/controllers',
    $baseDir . '/backend/models'
];

// Generate OpenAPI documentation
$openapi = Generator::scan($scanPaths);
$json = json_decode($openapi->toJson(), true);

// Add missing info section
$json['info'] = [
    'title' => 'LivePetal E-Commerce Platform API',
    'description' => 'Multi-tenant e-commerce platform with JWT authentication. This API allows super admins to manage clients and their stores, while clients can manage their products and orders.',
    'version' => '2.0.0',
    'contact' => [
        'name' => 'API Support',
        'email' => 'support@livepetal.com'
    ]
];

// Add servers
$json['servers'] = [
    [
        'url' => 'http://localhost:8000/',
        'description' => 'Local Development Server'
    ],
    [
        'url' => 'https://api.livepetal.com',
        'description' => 'Production Server'
    ]
];

// Ensure components section exists
if (!isset($json['components'])) {
    $json['components'] = [];
}

// Add security schemes
$json['components']['securitySchemes'] = [
    'bearerAuth' => [
        'type' => 'http',
        'scheme' => 'bearer',
        'bearerFormat' => 'JWT',
        'description' => 'Enter JWT token obtained from login endpoint'
    ]
];

// Add common schemas
if (!isset($json['components']['schemas'])) {
    $json['components']['schemas'] = [];
}

$json['components']['schemas']['Error'] = [
    'type' => 'object',
    'required' => ['success', 'message'],
    'properties' => [
        'success' => ['type' => 'boolean', 'example' => false],
        'message' => ['type' => 'string', 'example' => 'Error description'],
        'errors' => ['type' => 'object', 'nullable' => true, 'description' => 'Validation errors if applicable']
    ]
];

$json['components']['schemas']['Success'] = [
    'type' => 'object',
    'required' => ['success', 'message'],
    'properties' => [
        'success' => ['type' => 'boolean', 'example' => true],
        'message' => ['type' => 'string', 'example' => 'Operation successful'],
        'data' => ['type' => 'object', 'nullable' => true]
    ]
];

$json['components']['schemas']['User'] = [
    'type' => 'object',
    'required' => ['id', 'email', 'role'],
    'properties' => [
        'id' => ['type' => 'integer', 'example' => 1],
        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'user@example.com'],
        'role' => ['type' => 'string', 'enum' => ['admin', 'client'], 'example' => 'client'],
        'name' => ['type' => 'string', 'example' => 'John Doe']
    ]
];

$json['components']['schemas']['Client'] = [
    'type' => 'object',
    'required' => ['id', 'name', 'email', 'status'],
    'properties' => [
        'id' => ['type' => 'integer', 'example' => 1],
        'name' => ['type' => 'string', 'example' => 'John Doe'],
        'email' => ['type' => 'string', 'format' => 'email', 'example' => 'client@example.com'],
        'company_name' => ['type' => 'string', 'nullable' => true, 'example' => 'Acme Inc'],
        'phone' => ['type' => 'string', 'nullable' => true, 'example' => '+1234567890'],
        'subscription_plan' => ['type' => 'string', 'enum' => ['basic', 'standard', 'premium'], 'example' => 'standard'],
        'status' => ['type' => 'string', 'enum' => ['active', 'inactive', 'suspended'], 'example' => 'active'],
        'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-01-26T10:30:00Z']
    ]
];

$json['components']['schemas']['Store'] = [
    'type' => 'object',
    'required' => ['id', 'client_id', 'name', 'status'],
    'properties' => [
        'id' => ['type' => 'integer', 'example' => 1],
        'client_id' => ['type' => 'integer', 'example' => 1],
        'name' => ['type' => 'string', 'example' => 'My Online Store'],
        'domain' => ['type' => 'string', 'nullable' => true, 'example' => 'mystore.com'],
        'description' => ['type' => 'string', 'nullable' => true, 'example' => 'Best products online'],
        'logo_url' => ['type' => 'string', 'nullable' => true, 'example' => '/uploads/logos/store1.png'],
        'status' => ['type' => 'string', 'enum' => ['active', 'inactive', 'maintenance'], 'example' => 'active'],
        'template' => ['type' => 'string', 'example' => 'default'],
        'customization' => ['type' => 'object', 'nullable' => true],
        'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-01-26T10:30:00Z']
    ]
];

$json['components']['schemas']['Product'] = [
    'type' => 'object',
    'required' => ['id', 'store_id', 'name', 'price', 'stock_quantity'],
    'properties' => [
        'id' => ['type' => 'integer', 'example' => 1],
        'store_id' => ['type' => 'integer', 'example' => 1],
        'name' => ['type' => 'string', 'example' => 'Premium Widget'],
        'description' => ['type' => 'string', 'nullable' => true, 'example' => 'High quality product'],
        'price' => ['type' => 'number', 'format' => 'float', 'example' => 29.99],
        'stock_quantity' => ['type' => 'integer', 'example' => 100],
        'sku' => ['type' => 'string', 'nullable' => true, 'example' => 'WIDGET-001'],
        'category' => ['type' => 'string', 'nullable' => true, 'example' => 'Electronics'],
        'image_url' => ['type' => 'string', 'nullable' => true, 'example' => '/uploads/products/widget1.jpg'],
        'status' => ['type' => 'string', 'enum' => ['active', 'inactive', 'out_of_stock'], 'example' => 'active'],
        'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-01-26T10:30:00Z']
    ]
];

$json['components']['schemas']['Order'] = [
    'type' => 'object',
    'required' => ['id', 'store_id', 'customer_name', 'customer_email', 'total_amount', 'status'],
    'properties' => [
        'id' => ['type' => 'integer', 'example' => 1],
        'store_id' => ['type' => 'integer', 'example' => 1],
        'customer_name' => ['type' => 'string', 'example' => 'Jane Smith'],
        'customer_email' => ['type' => 'string', 'format' => 'email', 'example' => 'jane@example.com'],
        'customer_phone' => ['type' => 'string', 'nullable' => true, 'example' => '+1234567890'],
        'total_amount' => ['type' => 'number', 'format' => 'float', 'example' => 149.99],
        'status' => ['type' => 'string', 'enum' => ['pending', 'processing', 'completed', 'cancelled'], 'example' => 'pending'],
        'payment_method' => ['type' => 'string', 'nullable' => true, 'example' => 'credit_card'],
        'shipping_address' => ['type' => 'string', 'nullable' => true, 'example' => '123 Main St, City'],
        'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-01-26T10:30:00Z'],
        'updated_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-01-26T11:00:00Z']
    ]
];

// Output formatted JSON
$output = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
// Remove any potential null bytes
$output = str_replace("\0", '', $output);
echo $output;
