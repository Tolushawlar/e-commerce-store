<?php

/**
 * Rate Limiting Middleware - Usage Examples
 * 
 * This file demonstrates how to use the rate limiting middleware
 * in your API endpoints.
 */

require_once __DIR__ . '/middleware/RateLimitMiddleware.php';

// Load configuration
$config = require __DIR__ . '/config/config.php';

// Example 1: Basic Usage - Apply rate limiting to all API requests
// Add this at the beginning of your API entry point (e.g., api/index.php)

use Middleware\RateLimitMiddleware;

// Initialize middleware with configuration
$rateLimitMiddleware = new RateLimitMiddleware($config['rate_limiting']);

// Apply rate limiting
$rateLimitMiddleware->handle();

// Your API logic continues here...
// If rate limit is exceeded, the middleware will automatically return 429 response


// Example 2: Manual Rate Limit Check
// Useful when you need custom logic based on rate limit status

use Middleware\RateLimiter;

$rateLimiter = new RateLimiter($config['rate_limiting']);

// Check rate limit for current user/IP
$identifier = 'ip:' . $_SERVER['REMOTE_ADDR'];
$result = $rateLimiter->attempt($identifier, 10, 60); // 10 requests per minute

if ($result['allowed']) {
    // Process request
    echo json_encode([
        'success' => true,
        'data' => 'Your data here',
        'rate_limit' => [
            'limit' => $result['limit'],
            'remaining' => $result['remaining'],
            'reset' => $result['reset']
        ]
    ]);
} else {
    // Rate limit exceeded
    http_response_code(429);
    echo json_encode([
        'error' => 'Rate limit exceeded',
        'retry_after' => $result['reset']
    ]);
}


// Example 3: Endpoint-Specific Rate Limiting
// Different endpoints can have different limits

// For login endpoint (strict limit)
if ($_SERVER['REQUEST_URI'] === '/api/auth/login') {
    $rateLimitMiddleware = new RateLimitMiddleware([
        'enabled' => true,
        'storage' => 'file',
        'endpoints' => [
            '/api/auth/login' => ['limit' => 5, 'window' => 300] // 5 per 5 minutes
        ]
    ]);
    $rateLimitMiddleware->handle();
}

// For API endpoints (moderate limit)
if (strpos($_SERVER['REQUEST_URI'], '/api/products') === 0) {
    $rateLimitMiddleware = new RateLimitMiddleware([
        'enabled' => true,
        'storage' => 'file',
        'endpoints' => [
            '/api/products/*' => ['limit' => 100, 'window' => 60] // 100 per minute
        ]
    ]);
    $rateLimitMiddleware->handle();
}


// Example 4: User-Based Rate Limiting
// Rate limit based on authenticated user instead of IP

session_start();

if (isset($_SESSION['user_id'])) {
    $identifier = 'user:' . $_SESSION['user_id'];
} else {
    $identifier = 'ip:' . $_SERVER['REMOTE_ADDR'];
}

$result = $rateLimiter->attempt($identifier, 100, 3600); // 100 requests per hour


// Example 5: Whitelist/Blacklist Management

// Add IP to whitelist (bypass rate limiting)
$rateLimiter->addToWhitelist('192.168.1.100');

// Add IP to blacklist (permanently block)
$rateLimiter->addToBlacklist('10.0.0.50');

// Check if IP is whitelisted
if ($rateLimiter->isWhitelisted($_SERVER['REMOTE_ADDR'])) {
    // Skip rate limiting
}


// Example 6: Reset Rate Limit
// Useful for testing or admin actions

$identifier = 'ip:192.168.1.1';
$rateLimiter->reset($identifier);


// Example 7: Production Setup with Redis

$productionConfig = [
    'enabled' => true,
    'storage' => 'redis',
    'redis_host' => '127.0.0.1',
    'redis_port' => 6379,
    'redis_password' => 'your-redis-password',
    'redis_database' => 0,
    'default_limit' => 60,
    'default_window' => 60,
    'endpoints' => [
        '/api/auth/login' => ['limit' => 5, 'window' => 300],
        '/api/*' => ['limit' => 100, 'window' => 60]
    ],
    'whitelist' => ['127.0.0.1', '192.168.1.0/24'],
    'blacklist' => []
];

$rateLimitMiddleware = new RateLimitMiddleware($productionConfig);
$rateLimitMiddleware->handle();


// Example 8: Custom Response Headers
// The middleware automatically sets these headers:
// - X-RateLimit-Limit: Maximum requests allowed
// - X-RateLimit-Remaining: Requests remaining in current window
// - X-RateLimit-Reset: Seconds until limit resets
// - Retry-After: Seconds to wait when rate limited (only when 429)

// You can access these in your client:
// const limit = response.headers.get('X-RateLimit-Limit');
// const remaining = response.headers.get('X-RateLimit-Remaining');
// const reset = response.headers.get('X-RateLimit-Reset');


// Example 9: Environment Variables Setup
// Add these to your .env file:

/*
# Rate Limiting Configuration
RATE_LIMITING_ENABLED=true
RATE_LIMIT_STORAGE=redis
RATE_LIMIT_DEFAULT=60
RATE_LIMIT_WINDOW=60

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=your-password
REDIS_DATABASE=0

# Whitelist/Blacklist
RATE_LIMIT_WHITELIST=127.0.0.1,192.168.1.0/24
RATE_LIMIT_BLACKLIST=
*/


// Example 10: Integration with Existing API Router

class ApiRouter {
    private $rateLimitMiddleware;
    
    public function __construct() {
        $config = require __DIR__ . '/config/config.php';
        $this->rateLimitMiddleware = new RateLimitMiddleware($config['rate_limiting']);
    }
    
    public function handleRequest() {
        // Apply rate limiting before routing
        $this->rateLimitMiddleware->handle();
        
        // Continue with routing logic
        $this->route();
    }
    
    private function route() {
        // Your routing logic here
    }
}

$router = new ApiRouter();
$router->handleRequest();
