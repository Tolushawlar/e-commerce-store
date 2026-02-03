<?php

namespace App\Services;

use App\Helpers\JWT;

/**
 * Customer JWT Service
 * Handles JWT tokens for store customers (separate from admin/client tokens)
 */
class CustomerJWTService
{
    /**
     * Generate customer JWT token
     */
    public static function generate(array $customer, int $storeId): string
    {
        $payload = [
            'id' => $customer['id'],
            'role' => 'customer',
            'store_id' => $storeId,
            'email' => $customer['email'],
            'is_guest' => $customer['is_guest'] ?? false,
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60) // 7 days for customers
        ];

        return JWT::encode($payload);
    }

    /**
     * Verify and decode customer token
     */
    public static function verify(string $token): ?array
    {
        try {
            $payload = JWT::decode($token);

            // Verify it's a customer token
            if (!isset($payload['role']) || $payload['role'] !== 'customer') {
                return null;
            }

            // Check expiration
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return null;
            }

            return $payload;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Refresh customer token
     */
    public static function refresh(string $token): ?string
    {
        $payload = self::verify($token);

        if (!$payload) {
            return null;
        }

        // Generate new token with same data but new expiration
        return self::generate([
            'id' => $payload['id'],
            'email' => $payload['email'],
            'is_guest' => $payload['is_guest']
        ], $payload['store_id']);
    }

    /**
     * Extract customer from request headers/query
     */
    public static function getCustomerFromRequest(): ?array
    {
        // Try to get token from Authorization header
        $token = null;
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
        } elseif (isset($_GET['token'])) {
            $token = $_GET['token'];
        } elseif (isset($_COOKIE['customer_token'])) {
            $token = $_COOKIE['customer_token'];
        }

        if (!$token) {
            return null;
        }

        return self::verify($token);
    }
}
