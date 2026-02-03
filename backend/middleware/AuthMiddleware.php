<?php

namespace App\Middleware;

use App\Helpers\JWT;

/**
 * Authentication Middleware
 * Handles JWT token validation for admin, client, and customer tokens
 */
class AuthMiddleware
{
    /**
     * Verify JWT token
     */
    public static function handle(): void
    {
        $token = JWT::getTokenFromRequest();

        if (!$token) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No token provided'
            ]);
            exit;
        }

        try {
            // Verify and decode token
            $payload = JWT::decode($token);

            // Store user info in request for use in controllers
            $_REQUEST['auth_user'] = $payload;
        } catch (\Exception $e) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Check if user has specific role
     */
    public static function checkRole(string $role): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser || $authUser['role'] !== $role) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Insufficient permissions'
            ]);
            exit;
        }
    }

    /**
     * Check if user is admin
     */
    public static function adminOnly(): void
    {
        self::handle();
        self::checkRole('admin');
    }



    /**
     * Check if user is client
     */
    public static function clientOnly(): void
    {
        self::handle();
        self::checkRole('client');
    }
}
