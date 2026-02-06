<?php

namespace App\Models;

use App\Core\Model;

/**
 * PasswordReset Model
 * Manages password reset tokens
 */
class PasswordReset extends Model
{
    protected string $table = 'password_resets';

    protected array $fillable = [
        'user_id',
        'user_type',
        'token',
        'expires_at'
    ];

    /**
     * Create a new password reset token
     */
    public function createToken(int $userId, string $userType = 'client'): array
    {
        // Generate a random token
        $token = bin2hex(random_bytes(32));
        
        // Hash the token for storage
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        
        // Set expiration to 1 hour from now
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Delete any existing tokens for this user
        $this->deleteUserTokens($userId, $userType);
        
        // Create the new token
        $this->create([
            'user_id' => $userId,
            'user_type' => $userType,
            'token' => $hashedToken,
            'expires_at' => $expiresAt
        ]);
        
        return [
            'token' => $token, // Return unhashed token for email
            'expires_at' => $expiresAt
        ];
    }

    /**
     * Verify a reset token
     */
    public function verifyToken(string $token): ?array
    {
        // Get all non-expired tokens
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE expires_at > NOW()
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        $tokens = $stmt->fetchAll();

        // Check each token hash against the provided token
        foreach ($tokens as $tokenData) {
            if (password_verify($token, $tokenData['token'])) {
                return $tokenData;
            }
        }

        return null;
    }

    /**
     * Delete a token after use
     */
    public function deleteToken(int $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Delete all tokens for a user
     */
    public function deleteUserTokens(int $userId, string $userType = 'client'): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} 
            WHERE user_id = ? AND user_type = ?
        ");
        return $stmt->execute([$userId, $userType]);
    }

    /**
     * Clean up expired tokens
     */
    public function cleanExpiredTokens(): int
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE expires_at < NOW()");
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Get token count for rate limiting
     */
    public function getRecentTokenCount(int $userId, string $userType, int $minutes = 15): int
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM {$this->table} 
            WHERE user_id = ? AND user_type = ? AND created_at > ?
        ");
        $stmt->execute([$userId, $userType, $since]);
        $result = $stmt->fetch();
        
        return (int)($result['count'] ?? 0);
    }

    /**
     * Check if token exists and is valid
     */
    public function isValidToken(string $token): bool
    {
        return $this->verifyToken($token) !== null;
    }
}
