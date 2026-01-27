<?php
// Quick debug to check routes
require_once __DIR__ . '/../bootstrap.php';

use App\Core\Router;

$router = new Router();

// Test route
$router->get('/api/test', function () {
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Test route works!']);
});

// OpenAPI route
$router->get('/api/openapi.json', function () {
    header('Content-Type: application/json');
    readfile(__DIR__ . '/openapi.json');
});

// Debug output
header('Content-Type: text/plain');
echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Parsed Path: " . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . "\n";
echo "\nAttempting to resolve route...\n\n";

// Try to resolve
ob_start();
$router->resolve();
$output = ob_get_clean();

if ($output) {
    echo $output;
} else {
    echo "Route resolved but no output\n";
}
