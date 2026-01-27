<?php

/**
 * Direct test of OpenAPI generation (access via browser)
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../vendor/autoload.php';

use OpenApi\Generator;

// Define scan paths  
$scanPaths = [
    __DIR__ . '/../swagger.php',
    __DIR__ . '/../controllers',
];

try {
    $openapi = Generator::scan($scanPaths);
    header('Content-Type: application/json');
    echo $openapi->toJson(JSON_PRETTY_PRINT);
} catch (Exception $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
