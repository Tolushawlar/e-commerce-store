<?php

namespace App\Helpers;

/**
 * JWT (JSON Web Token) Helper
 * Simple JWT implementation without external dependencies
 */
class JWT
{
    private static string $algorithm = 'HS256';

    /**
     * Generate JWT token
     */
    public static function encode(array $payload, ?string $secret = null, ?int $expiration = null): string
    {
        $secret = $secret ?? config('security.jwt_secret');

        // Validate secret is configured
        if (empty($secret)) {
            throw new \Exception('JWT secret not configured. Set JWT_SECRET in .env file or check config/config.php');
        }

        // Add standard claims
        $payload['iat'] = time(); // Issued at
        $payload['exp'] = time() + ($expiration ?? config('security.session_lifetime', 7200)); // Expiration

        // Create header
        $header = [
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ];

        // Encode header and payload
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        // Create signature
        $signature = self::sign("$headerEncoded.$payloadEncoded", $secret);

        return "$headerEncoded.$payloadEncoded.$signature";
    }

    /**
     * Generate access and refresh token pair
     */
    public static function generateTokenPair(array $payload): array
    {
        // Access token (15 minutes)
        $accessToken = self::encode($payload, null, 900);

        // Refresh token (7 days) - with type flag
        $refreshPayload = array_merge($payload, ['type' => 'refresh']);
        $refreshToken = self::encode($refreshPayload, null, 604800);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 900, // 15 minutes in seconds
            'token_type' => 'Bearer'
        ];
    }

    /**
     * Decode and verify JWT token
     */
    public static function decode(string $token, ?string $secret = null): array
    {
        $secret = $secret ?? config('security.jwt_secret');

        // Validate secret is configured
        if (empty($secret)) {
            throw new \Exception('JWT secret not configured. Set JWT_SECRET in .env file or check config/config.php');
        }

        // Split token
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new \Exception('Invalid token format');
        }

        [$headerEncoded, $payloadEncoded, $signature] = $parts;

        // Verify signature
        $expectedSignature = self::sign("$headerEncoded.$payloadEncoded", $secret);

        if (!hash_equals($signature, $expectedSignature)) {
            throw new \Exception('Invalid signature');
        }

        // Decode payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);

        if (!$payload) {
            throw new \Exception('Invalid payload');
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new \Exception('Token expired');
        }

        return $payload;
    }

    /**
     * Create signature
     */
    private static function sign(string $data, string $secret): string
    {
        return self::base64UrlEncode(
            hash_hmac('sha256', $data, $secret, true)
        );
    }

    /**
     * Base64 URL encode
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     */
    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Get token from request
     */
    public static function getTokenFromRequest(): ?string
    {
        // Check Authorization header
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];

            // Remove 'Bearer ' prefix
            if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                return $matches[1];
            }

            return $auth;
        }

        // Check query parameter
        if (isset($_GET['token'])) {
            return $_GET['token'];
        }

        return null;
    }
}
