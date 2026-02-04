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
];
