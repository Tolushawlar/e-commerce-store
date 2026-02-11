<?php

/**
 * Application Configuration
 */

// Load environment variables from .env file
if (file_exists(__DIR__ . '/../../.env')) {
    $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
            }
        }
    }
}

return [
    'app' => [
        'name' => 'E-commerce Platform',
        'version' => '2.0.0',
        'env' => getenv('APP_ENV') ?: 'development', // development, production
        'debug' => getenv('APP_DEBUG') === 'true',
        'timezone' => 'Africa/Lagos',
    ],

    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'ecommerce_platform',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: 'root',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],

    'cors' => [
        'allowed_origins' => [
            'http://localhost:3000',  // Development
            'http://localhost:8000',  // Development
            'https://livepetal.com', // Production
            'https://www.livepetal.com'
        ],
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
        'jwt_secret' => getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production',
        'password_min_length' => 8,
        'session_lifetime' => 7200, // 2 hours
    ],

    'pagination' => [
        'default_limit' => 20,
        'max_limit' => 100,
    ],

    'cloudinary' => [
        'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME') ?: '',
        'api_key' => getenv('CLOUDINARY_API_KEY') ?: '',
        'api_secret' => getenv('CLOUDINARY_API_SECRET') ?: '',
        'upload_preset' => getenv('CLOUDINARY_UPLOAD_PRESET') ?: 'ecommerce_uploads',
        'folder' => getenv('CLOUDINARY_FOLDER') ?: 'ecommerce',
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'allowed_formats' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
    ],

    'email' => [
        'smtp_host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
        'smtp_port' => getenv('SMTP_PORT') ?: 587,
        'smtp_username' => getenv('SMTP_USERNAME') ?: '',
        'smtp_password' => getenv('SMTP_PASSWORD') ?: '',
        'smtp_encryption' => getenv('SMTP_ENCRYPTION') ?: 'tls', // tls or ssl
        'from_email' => getenv('SMTP_FROM_EMAIL') ?: 'noreply@ecommerce-platform.com',
        'from_name' => getenv('SMTP_FROM_NAME') ?: 'E-commerce Platform',
        'max_retry' => 3,
        'timeout' => 30,
    ],

    'notifications' => [
        'enabled' => getenv('NOTIFICATIONS_ENABLED') !== 'false',
        'email_enabled' => getenv('EMAIL_NOTIFICATIONS_ENABLED') !== 'false',
        'channels' => ['database', 'email'], // Available channels
        'queue_enabled' => getenv('NOTIFICATION_QUEUE_ENABLED') === 'true',
    ],

    'rate_limiting' => [
        'enabled' => getenv('RATE_LIMITING_ENABLED') !== 'false',
        
        // Storage backend: 'file' (development) or 'redis' (production)
        // Defaults to 'file' in development, use RATE_LIMIT_STORAGE=redis in production
        'storage' => getenv('RATE_LIMIT_STORAGE') ?: (getenv('APP_ENV') === 'production' ? 'redis' : 'file'),
        
        // Redis configuration (used when storage = 'redis')
        'redis_host' => getenv('REDIS_HOST') ?: '127.0.0.1',
        'redis_port' => (int)(getenv('REDIS_PORT') ?: 6379),
        'redis_password' => getenv('REDIS_PASSWORD') ?: null,
        'redis_database' => (int)(getenv('REDIS_DATABASE') ?: 0),
        
        // File storage directory (used when storage = 'file')
        'file_storage_dir' => dirname(__DIR__, 2) . '/storage/rate_limits',
        
        // Default rate limits (requests per window)
        'default_limit' => (int)(getenv('RATE_LIMIT_DEFAULT') ?: 60),
        'default_window' => (int)(getenv('RATE_LIMIT_WINDOW') ?: 60), // seconds
        
        // Endpoint-specific rate limits
        'endpoints' => [
            // Authentication endpoints (stricter limits)
            '/api/auth/admin/login' => ['limit' => 5, 'window' => 300], // 5 per 5 minutes
            '/api/auth/client/login' => ['limit' => 5, 'window' => 300], // 5 per 5 minutes
            '/api/auth/client/register' => ['limit' => 3, 'window' => 3600], // 3 per hour
            '/api/auth/forgot-password' => ['limit' => 3, 'window' => 3600],
            '/api/auth/reset-password' => ['limit' => 3, 'window' => 3600],
            
            // API endpoints (moderate limits)
            '/api/products/*' => ['limit' => 100, 'window' => 60], // 100 per minute
            '/api/orders/*' => ['limit' => 50, 'window' => 60],
            '/api/customers/*' => ['limit' => 50, 'window' => 60],
            
            // Admin endpoints (higher limits)
            '/api/admin/*' => ['limit' => 200, 'window' => 60],
            
            // Public endpoints (lower limits)
            '/api/public/*' => ['limit' => 30, 'window' => 60],
        ],
        
        // Whitelist (IPs that bypass rate limiting)
        'whitelist' => array_filter(explode(',', getenv('RATE_LIMIT_WHITELIST') ?: '127.0.0.1')),
        
        // Blacklist (IPs that are permanently blocked)
        'blacklist' => array_filter(explode(',', getenv('RATE_LIMIT_BLACKLIST') ?: '')),
    ],
];
