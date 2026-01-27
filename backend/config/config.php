<?php

/**
 * Application Configuration
 */

return [
    'app' => [
        'name' => 'E-commerce Platform',
        'version' => '2.0.0',
        'env' => 'development', // development, production
        'debug' => true,
        'timezone' => 'Africa/Lagos',
    ],

    'database' => [
        'host' => 'localhost',
        'name' => 'ecommerce_platform',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],

    'cors' => [
        'allowed_origins' => ['*'],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        'max_age' => 86400,
    ],

    'paths' => [
        'stores' => dirname(__DIR__, 2) . '/api/stores',
        'uploads' => dirname(__DIR__, 2) . '/uploads',
        'templates' => dirname(__DIR__, 2) . '/store-templates',
    ],

    'security' => [
        'jwt_secret' => 'your-secret-key-change-in-production',
        'password_min_length' => 8,
        'session_lifetime' => 7200, // 2 hours
    ],

    'pagination' => [
        'default_limit' => 20,
        'max_limit' => 100,
    ],
];
