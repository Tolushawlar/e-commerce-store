<?php
// Simple test for OpenAPI generation
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing OpenAPI generation...\n\n";

// Test 1: Check if vendor autoload exists
$vendorPath = __DIR__ . '/../vendor/autoload.php';
echo "1. Checking vendor autoload at: $vendorPath\n";
if (file_exists($vendorPath)) {
    echo "   ✓ Vendor autoload exists\n";
    require_once $vendorPath;
} else {
    die("   ✗ Vendor autoload NOT found!\n");
}

// Test 2: Check if OpenApi\Generator is available
echo "\n2. Checking if OpenApi\\Generator class exists\n";
if (class_exists('OpenApi\Generator')) {
    echo "   ✓ OpenApi\\Generator class found\n";
} else {
    die("   ✗ OpenApi\\Generator class NOT found!\n");
}

// Test 3: Check scan paths
$scanPaths = [
    __DIR__ . '/swagger.php',
    __DIR__ . '/controllers',
    __DIR__ . '/models'
];

echo "\n3. Checking scan paths:\n";
foreach ($scanPaths as $path) {
    if (file_exists($path)) {
        echo "   ✓ $path exists\n";
    } else {
        echo "   ✗ $path NOT found!\n";
    }
}

// Test 4: Generate OpenAPI
echo "\n4. Generating OpenAPI specification...\n";
try {
    $openapi = \OpenApi\Generator::scan($scanPaths);
    $json = $openapi->toJson();
    echo "   ✓ OpenAPI generated successfully!\n";
    echo "   Length: " . strlen($json) . " bytes\n";

    // Check for errors in the spec
    $decoded = json_decode($json, true);
    if (isset($decoded['openapi'])) {
        echo "   ✓ OpenAPI version: " . $decoded['openapi'] . "\n";
    }
    if (isset($decoded['info']['title'])) {
        echo "   ✓ API Title: " . $decoded['info']['title'] . "\n";
    }

    echo "\n5. Output sample:\n";
    echo substr($json, 0, 500) . "...\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n\nTest complete!\n";
