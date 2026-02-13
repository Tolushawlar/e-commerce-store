<?php

namespace App\Middleware;

use App\Helpers\JWT;
use App\Services\TokenSecurityService;

/**
 * Authentication Middleware
 * Handles JWT token validation for admin, client, and customer tokens
 */
class AuthMiddleware
{
    /**
     * Verify JWT token with enhanced security checks
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

            // Initialize security service
            $securityService = new TokenSecurityService();

            // Check if token is blacklisted (if jti exists)
            if (isset($payload['jti']) && $securityService->isBlacklisted($payload['jti'])) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Token has been revoked'
                ]);
                exit;
            }

            // Validate device fingerprint (skip for refresh tokens)
            if (!isset($payload['type']) || $payload['type'] !== 'refresh') {
                $fingerprint = $securityService->generateFingerprint();
                $userType = self::getUserType($payload);
                
                $deviceCheck = $securityService->validateDevice(
                    $payload['user_id'],
                    $userType,
                    $fingerprint
                );

                // Check if device is not trusted (strict mode enabled)
                if (!$deviceCheck['trusted']) {
                    $reason = $deviceCheck['reason'] ?? 'untrusted_device';
                    http_response_code(403);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Device verification failed. Possible token theft detected.',
                        'reason' => $reason,
                        'requires_reauth' => true
                    ]);
                    exit;
                }

                // Optional: Enable this for stricter security (requires user verification on new devices)
                // Uncomment the block below to require additional verification for new devices
                /*
                if ($deviceCheck['is_new'] && config('security.require_device_verification', false)) {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'New device detected. Please verify your identity.',
                        'requires_verification' => true
                    ]);
                    exit;
                }
                */
            }

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
     * Get user type from payload
     * 
     * @param array $payload JWT payload
     * @return string User type
     */
    private static function getUserType(array $payload): string
    {
        // Check for explicit type field
        if (isset($payload['type'])) {
            $type = $payload['type'];
            
            // Map super_admin to admin
            if ($type === 'super_admin') {
                return 'admin';
            }
            
            // Return type if it's customer or client
            if (in_array($type, ['customer', 'client'])) {
                return $type;
            }
        }
        
        // Fallback to role field
        if (isset($payload['role'])) {
            $role = $payload['role'];
            
            // Map admin role to admin type
            if ($role === 'admin') {
                return 'admin';
            }
            
            // Return role if it matches our user types
            if (in_array($role, ['customer', 'client'])) {
                return $role;
            }
        }
        
        // Default to client if nothing matches
        return 'client';
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
