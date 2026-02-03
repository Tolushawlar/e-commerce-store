<?php

/**
 * Load Composer's autoloader
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Load .env file if it exists
 */
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }

            // Set environment variable
            if (!getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

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
 * Initialize Sentry Error Monitoring
 */
if (function_exists('\Sentry\init')) {
    $sentryConfig = require __DIR__ . '/config/sentry.php';

    if (!empty($sentryConfig['dsn'])) {
        \Sentry\init([
            'dsn' => $sentryConfig['dsn'],
            'environment' => $sentryConfig['environment'],
            'release' => $sentryConfig['release'],
            'traces_sample_rate' => $sentryConfig['traces_sample_rate'],
            'send_default_pii' => $sentryConfig['send_default_pii'],
            'attach_stacktrace' => $sentryConfig['attach_stacktrace'],
            'max_breadcrumbs' => $sentryConfig['max_breadcrumbs'],
            'before_send' => $sentryConfig['before_send'],
            'tags' => $sentryConfig['tags'],
            'enable_logs' => true,
        ]);

       // error_log("Sentry initialized successfully with DSN: " . substr($sentryConfig['dsn'], 0, 30) . "...");
    } else {
        error_log("Sentry DSN not configured. Set SENTRY_DSN environment variable to enable error tracking.");
    }
} else {
    error_log("Sentry SDK not loaded. Install with: composer require sentry/sentry");
}

/**
 * Set up global error handlers (always, whether Sentry is available or not)
 */
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }

    if (class_exists('\App\Helpers\Logger')) {
        \App\Helpers\Logger::error("PHP Error: $errstr", [
            'errno' => $errno,
            'file' => $errfile,
            'line' => $errline,
        ]);
    }

    return false;
});

set_exception_handler(function ($exception) {
    if (class_exists('\App\Helpers\Logger')) {
        \App\Helpers\Logger::exception($exception);
    }

    // Always send JSON response for API
    http_response_code(500);
    header('Content-Type: application/json');

    if (config('app.env') === 'production' && php_sapi_name() !== 'cli') {
        echo json_encode([
            'success' => false,
            'message' => 'An unexpected error occurred. Our team has been notified.',
        ]);
    } else {
        // In development, show detailed error
        echo json_encode([
            'success' => false,
            'message' => $exception->getMessage(),
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
    exit;
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        if (class_exists('\App\Helpers\Logger')) {
            \App\Helpers\Logger::critical("Fatal Error: {$error['message']}", [
                'file' => $error['file'],
                'line' => $error['line'],
            ]);
        }
    }

    // Flush Sentry events before shutdown
    if (function_exists('\Sentry\State\HubAdapter::getInstance')) {
        $client = \Sentry\State\HubAdapter::getInstance()->getClient();
        if ($client !== null) {
            $client->flush(2);
        }
    }
});

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
 * CORS Configuration (skip in CLI mode)
 */
if (php_sapi_name() !== 'cli') {
    header('Access-Control-Allow-Origin: ' . implode(', ', $config['cors']['allowed_origins']));
    header('Access-Control-Allow-Methods: ' . implode(', ', $config['cors']['allowed_methods']));
    header('Access-Control-Allow-Headers: ' . implode(', ', $config['cors']['allowed_headers']));
    header('Access-Control-Max-Age: ' . $config['cors']['max_age']);

    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
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
