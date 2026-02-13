<?php

namespace App\Services;

use App\Config\Database;
use PDO;

/**
 * Token Security Service
 * Handles token validation, blacklisting, and device fingerprinting
 * Provides multi-layer protection against JWT token theft
 */
class TokenSecurityService
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Generate device fingerprint from request
     * Creates a unique hash based on browser characteristics
     * 
     * @return string SHA-256 hash of device fingerprint
     */
    public function generateFingerprint(): string
    {
        $components = [
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
            $_SERVER['HTTP_ACCEPT'] ?? '',
            $_SERVER['HTTP_SEC_CH_UA'] ?? '', // Chrome/Edge client hints
            $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '',
            $_SERVER['HTTP_SEC_CH_UA_MOBILE'] ?? '',
            // Add referer to detect if coming from your frontend
            $_SERVER['HTTP_REFERER'] ?? '',
            $_SERVER['HTTP_ORIGIN'] ?? '',
        ];
        
        return hash('sha256', implode('|', $components));
    }

    /**
     * Get client IP address with proxy support
     * 
     * @return string Client IP address
     */
    private function getClientIp(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',  // Proxy
            'HTTP_X_REAL_IP',        // Nginx
            'REMOTE_ADDR'            // Direct connection
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                
                // Handle comma-separated IPs (X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }
                
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }

    /**
     * Validate device fingerprint and track device
     * Checks if the device is recognized and trusted
     * 
     * @param int $userId User ID
     * @param string $userType User type (admin, client, customer)
     * @param string $fingerprint Device fingerprint hash
     * @return array ['trusted' => bool, 'is_new' => bool]
     */
    public function validateDevice(int $userId, string $userType, string $fingerprint): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM token_devices 
            WHERE user_id = ? AND user_type = ? AND fingerprint = ? AND is_trusted = 1
            LIMIT 1
        ");
        $stmt->execute([$userId, $userType, $fingerprint]);
        $device = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentIp = $this->getClientIp();
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (!$device) {
            // New device - register it
            $this->registerDevice($userId, $userType, $fingerprint, $currentIp);
            
            // Log security event
            $this->logSecurityEvent(
                $userId,
                $userType,
                'new_device',
                'medium',
                [
                    'fingerprint' => substr($fingerprint, 0, 16) . '...',
                    'ip' => $currentIp,
                    'user_agent' => substr($currentUserAgent, 0, 100)
                ]
            );
            
            return ['trusted' => true, 'is_new' => true];
        }

        // Check if user agent has drastically changed (possible token theft)
        if ($device['user_agent'] && $currentUserAgent) {
            $similarity = $this->calculateUserAgentSimilarity($device['user_agent'], $currentUserAgent);
            
            // If user agent is less than 70% similar, log suspicious activity
            if ($similarity < 0.7) {
                $this->logSecurityEvent(
                    $userId,
                    $userType,
                    'user_agent_mismatch',
                    'high',
                    [
                        'original_ua' => substr($device['user_agent'], 0, 100),
                        'current_ua' => substr($currentUserAgent, 0, 100),
                        'similarity' => round($similarity * 100, 2) . '%',
                        'fingerprint' => substr($fingerprint, 0, 16) . '...',
                        'ip' => $currentIp
                    ]
                );
                
                // Optional: Enable strict mode to block this
                if (config('security.strict_device_fingerprint', false)) {
                    return ['trusted' => false, 'is_new' => false, 'reason' => 'user_agent_mismatch'];
                }
            }
        }

        // Check for suspicious IP changes
        if ($device['ip_address'] !== $currentIp) {
            $this->logSecurityEvent(
                $userId,
                $userType,
                'ip_change',
                'medium',
                [
                    'old_ip' => $device['ip_address'],
                    'new_ip' => $currentIp,
                    'fingerprint' => substr($fingerprint, 0, 16) . '...'
                ]
            );
            
            // Update IP address
            $this->updateDeviceIp($device['id'], $currentIp);
        } else {
            // Update last used timestamp
            $this->touchDevice($device['id']);
        }

        return ['trusted' => true, 'is_new' => false];
    }

    /**
     * Calculate similarity between two user agent strings
     * 
     * @param string $ua1 First user agent
     * @param string $ua2 Second user agent
     * @return float Similarity score (0-1)
     */
    private function calculateUserAgentSimilarity(string $ua1, string $ua2): float
    {
        if ($ua1 === $ua2) {
            return 1.0;
        }
        
        // Use similar_text for basic similarity
        similar_text(strtolower($ua1), strtolower($ua2), $percent);
        
        return $percent / 100;
    }

    /**
     * Register new device
     * 
     * @param int $userId User ID
     * @param string $userType User type
     * @param string $fingerprint Device fingerprint
     * @param string $ip IP address
     */
    private function registerDevice(int $userId, string $userType, string $fingerprint, string $ip): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO token_devices (user_id, user_type, fingerprint, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $userType,
            $fingerprint,
            $ip,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    /**
     * Update device IP address
     * 
     * @param int $deviceId Device ID
     * @param string $ip New IP address
     */
    private function updateDeviceIp(int $deviceId, string $ip): void
    {
        $stmt = $this->db->prepare("
            UPDATE token_devices SET ip_address = ? WHERE id = ?
        ");
        $stmt->execute([$ip, $deviceId]);
    }

    /**
     * Touch device (update last_used_at timestamp)
     * 
     * @param int $deviceId Device ID
     */
    private function touchDevice(int $deviceId): void
    {
        $stmt = $this->db->prepare("
            UPDATE token_devices SET last_used_at = CURRENT_TIMESTAMP WHERE id = ?
        ");
        $stmt->execute([$deviceId]);
    }

    /**
     * Check if token is blacklisted
     * 
     * @param string $jti JWT ID
     * @return bool True if blacklisted
     */
    public function isBlacklisted(string $jti): bool
    {
        $stmt = $this->db->prepare("
            SELECT id FROM token_blacklist 
            WHERE token_jti = ? AND expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([$jti]);
        return $stmt->fetch() !== false;
    }

    /**
     * Blacklist a specific token
     * 
     * @param string $jti JWT ID
     * @param int $userId User ID
     * @param string $userType User type
     * @param int $expiresAt Token expiration timestamp
     * @param string|null $reason Reason for blacklisting
     */
    public function blacklistToken(string $jti, int $userId, string $userType, int $expiresAt, ?string $reason = null): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO token_blacklist (token_jti, user_id, user_type, expires_at, reason)
            VALUES (?, ?, ?, FROM_UNIXTIME(?), ?)
            ON DUPLICATE KEY UPDATE reason = VALUES(reason)
        ");
        $stmt->execute([$jti, $userId, $userType, $expiresAt, $reason]);

        $this->logSecurityEvent(
            $userId,
            $userType,
            'token_blacklisted',
            'high',
            ['jti' => substr($jti, 0, 16) . '...', 'reason' => $reason]
        );
    }

    /**
     * Blacklist all tokens for a user (e.g., password change, suspicious activity)
     * This invalidates all devices for the user
     * 
     * @param int $userId User ID
     * @param string $userType User type
     * @param string $reason Reason for revocation
     */
    public function blacklistAllUserTokens(int $userId, string $userType, string $reason = 'security_revoke'): void
    {
        // Mark all devices as untrusted
        $stmt = $this->db->prepare("
            UPDATE token_devices SET is_trusted = 0 
            WHERE user_id = ? AND user_type = ?
        ");
        $stmt->execute([$userId, $userType]);

        $this->logSecurityEvent(
            $userId,
            $userType,
            'all_tokens_revoked',
            'high',
            ['reason' => $reason]
        );
    }

    /**
     * Log security event
     * 
     * @param int|null $userId User ID
     * @param string|null $userType User type
     * @param string $eventType Event type
     * @param string $severity Severity level
     * @param array|null $details Additional details
     */
    public function logSecurityEvent(
        ?int $userId,
        ?string $userType,
        string $eventType,
        string $severity = 'medium',
        ?array $details = null
    ): void {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO security_events (user_id, user_type, event_type, severity, ip_address, user_agent, details)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $userType,
                $eventType,
                $severity,
                $this->getClientIp(),
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $details ? json_encode($details) : null
            ]);
        } catch (\Exception $e) {
            // Fail silently - don't break auth flow if logging fails
            error_log("Failed to log security event: " . $e->getMessage());
        }
    }

    /**
     * Get recent security events for a user
     * 
     * @param int $userId User ID
     * @param string $userType User type
     * @param int $limit Number of events to retrieve
     * @return array Security events
     */
    public function getRecentEvents(int $userId, string $userType, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM security_events 
            WHERE user_id = ? AND user_type = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $userType, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all trusted devices for a user
     * 
     * @param int $userId User ID
     * @param string $userType User type
     * @return array Trusted devices
     */
    public function getTrustedDevices(int $userId, string $userType): array
    {
        $stmt = $this->db->prepare("
            SELECT id, fingerprint, ip_address, user_agent, last_used_at, created_at
            FROM token_devices 
            WHERE user_id = ? AND user_type = ? AND is_trusted = 1
            ORDER BY last_used_at DESC
        ");
        $stmt->execute([$userId, $userType]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Revoke/untrust a specific device
     * 
     * @param int $deviceId Device ID
     * @param int $userId User ID (for security check)
     * @param string $userType User type (for security check)
     */
    public function revokeDevice(int $deviceId, int $userId, string $userType): bool
    {
        $stmt = $this->db->prepare("
            UPDATE token_devices 
            SET is_trusted = 0 
            WHERE id = ? AND user_id = ? AND user_type = ?
        ");
        $stmt->execute([$deviceId, $userId, $userType]);
        
        if ($stmt->rowCount() > 0) {
            $this->logSecurityEvent(
                $userId,
                $userType,
                'device_revoked',
                'medium',
                ['device_id' => $deviceId]
            );
            return true;
        }
        
        return false;
    }

    /**
     * Clean up expired data (run via cron job)
     * Removes old blacklisted tokens, security events, and inactive devices
     */
    public function cleanup(): void
    {
        // Remove expired blacklisted tokens
        $this->db->exec("DELETE FROM token_blacklist WHERE expires_at < NOW()");
        
        // Remove old security events (90 days)
        $this->db->exec("DELETE FROM security_events WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
        
        // Remove inactive devices (90 days)
        $this->db->exec("DELETE FROM token_devices WHERE last_used_at < DATE_SUB(NOW(), INTERVAL 90 DAY) AND is_trusted = 1");
    }
}
