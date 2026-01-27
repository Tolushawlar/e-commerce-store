<?php

/**
 * Generate OpenAPI specification from annotations
 * Run this file to generate openapi.json
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use OpenApi\Generator;

// Define scan paths
$scanPaths = [
    __DIR__ . '/../swagger.php',
    __DIR__ . '/../controllers',
    __DIR__ . '/../models'
];

// Generate OpenAPI documentation
$openapi = Generator::scan($scanPaths);

// Output as JSON
header('Content-Type: application/json');
echo $openapi->toJson();
