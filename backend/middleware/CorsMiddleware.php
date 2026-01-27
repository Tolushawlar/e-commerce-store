<?php

namespace App\Middleware;

/**
 * CORS Middleware
 * Handles Cross-Origin Resource Sharing
 */
class CorsMiddleware
{
    /**
     * Handle CORS headers
     */
    public static function handle(): void
    {
        $allowedOrigins = config('cors.allowed_origins');
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

        // Check if origin is allowed
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        }

        header('Access-Control-Allow-Methods: ' . implode(', ', config('cors.allowed_methods')));
        header('Access-Control-Allow-Headers: ' . implode(', ', config('cors.allowed_headers')));
        header('Access-Control-Max-Age: ' . config('cors.max_age'));
        header('Access-Control-Allow-Credentials: true');

        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
