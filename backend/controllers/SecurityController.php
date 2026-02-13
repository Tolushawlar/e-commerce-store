<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\TokenSecurityService;
use App\Helpers\JWT;

/**
 * Security Controller
 * Manages user security settings, device management, and security events
 */
class SecurityController extends Controller
{
    private TokenSecurityService $securityService;

    public function __construct()
    {
        $this->securityService = new TokenSecurityService();
    }

    /**
     * Get trusted devices for authenticated user
     * GET /api/security/devices
     * 
     * @OA\Get(
     *     path="/api/security/devices",
     *     tags={"Security"},
     *     summary="Get trusted devices",
     *     description="Retrieve list of all trusted devices for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Devices retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function getDevices(): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $userType = $authUser['type'] === 'super_admin' ? 'admin' : $authUser['type'];
        $devices = $this->securityService->getTrustedDevices($authUser['user_id'], $userType);

        // Add current device indicator
        $currentFingerprint = $this->securityService->generateFingerprint();
        foreach ($devices as &$device) {
            $device['is_current'] = $device['fingerprint'] === $currentFingerprint;
            
            // Truncate fingerprint for display
            $device['fingerprint'] = substr($device['fingerprint'], 0, 16) . '...';
            
            // Parse user agent for better display
            $device['browser'] = $this->parseUserAgent($device['user_agent']);
        }

        $this->success($devices);
    }

    /**
     * Revoke a specific device
     * DELETE /api/security/devices/{id}
     * 
     * @OA\Delete(
     *     path="/api/security/devices/{id}",
     *     tags={"Security"},
     *     summary="Revoke device",
     *     description="Revoke trust for a specific device. All tokens from this device will be invalidated.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Device ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device revoked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Device revoked successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Device not found", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function revokeDevice(int $deviceId): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $userType = $authUser['type'] === 'super_admin' ? 'admin' : $authUser['type'];
        $result = $this->securityService->revokeDevice($deviceId, $authUser['user_id'], $userType);

        if (!$result) {
            $this->error('Device not found or already revoked', 404);
        }

        $this->success(null, 'Device revoked successfully. You will need to login again from that device.');
    }

    /**
     * Revoke all devices (except current)
     * POST /api/security/devices/revoke-all
     * 
     * @OA\Post(
     *     path="/api/security/devices/revoke-all",
     *     tags={"Security"},
     *     summary="Revoke all devices",
     *     description="Revoke trust for all devices except the current one",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="All devices revoked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="All other devices have been logged out")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function revokeAllDevices(): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $userType = $authUser['type'] === 'super_admin' ? 'admin' : $authUser['type'];
        $this->securityService->blacklistAllUserTokens($authUser['user_id'], $userType, 'user_revoke_all_devices');

        $this->success(null, 'All other devices have been logged out. You will need to login again from those devices.');
    }

    /**
     * Get security events for authenticated user
     * GET /api/security/events
     * 
     * @OA\Get(
     *     path="/api/security/events",
     *     tags={"Security"},
     *     summary="Get security events",
     *     description="Retrieve recent security events for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Number of events to retrieve (default: 20)",
     *         @OA\Schema(type="integer", minimum=1, maximum=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Security events retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function getSecurityEvents(): void
    {
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        $limit = min((int)($_GET['limit'] ?? 20), 100);
        $userType = $authUser['type'] === 'super_admin' ? 'admin' : $authUser['type'];
        
        $events = $this->securityService->getRecentEvents($authUser['user_id'], $userType, $limit);

        // Format events for better readability
        foreach ($events as &$event) {
            if ($event['details']) {
                $event['details'] = json_decode($event['details'], true);
            }
        }

        $this->success($events);
    }

    /**
     * Parse user agent string for display
     * 
     * @param string|null $userAgent User agent string
     * @return string Parsed browser/device info
     */
    private function parseUserAgent(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Unknown Device';
        }

        // Simple user agent parsing
        if (preg_match('/Chrome\/[\d.]+/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari\/[\d.]+/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Firefox\/[\d.]+/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Edge\/[\d.]+/i', $userAgent)) {
            $browser = 'Edge';
        } else {
            $browser = 'Other Browser';
        }

        // Detect OS
        if (preg_match('/Windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac OS X/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iPhone|iPad/i', $userAgent)) {
            $os = 'iOS';
        } else {
            $os = 'Unknown OS';
        }

        return "$browser on $os";
    }
}
