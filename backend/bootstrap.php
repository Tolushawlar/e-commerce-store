<?php

/**
 * PSR-4 Autoloader
 */
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Load configuration
 */
$config = require __DIR__ . '/config/config.php';

/**
 * Set timezone
 */
date_default_timezone_set($config['app']['timezone']);

/**
 * Error reporting based on environment
 */
if ($config['app']['env'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/**
 * CORS Configuration
 */
header('Access-Control-Allow-Origin: ' . implode(', ', $config['cors']['allowed_origins']));
header('Access-Control-Allow-Methods: ' . implode(', ', $config['cors']['allowed_methods']));
header('Access-Control-Allow-Headers: ' . implode(', ', $config['cors']['allowed_headers']));
header('Access-Control-Max-Age: ' . $config['cors']['max_age']);

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Helper function to get config value
 */
function config(string $key, $default = null)
{
    global $config;

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }

    return $value;
}

/**
 * Helper function for base path
 */
function base_path(string $path = ''): string
{
    return __DIR__ . '/../' . ltrim($path, '/');
}

/**
 * Helper function for storage path
 */
function storage_path(string $path = ''): string
{
    return base_path('storage/' . ltrim($path, '/'));
}
