<?php

namespace App\Middleware;

use App\Middleware\Storage\StorageInterface;
use App\Middleware\Storage\FileStorage;
use App\Middleware\Storage\RedisStorage;

/**
 * Rate Limiter Core Class
 * Implements sliding window algorithm with multiple storage backends
 */
class RateLimiter
{
    private StorageInterface $storage;
    private array $config;
    private array $whitelist = [];
    private array $blacklist = [];

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'storage' => 'file',
            'default_limit' => 60,
            'default_window' => 60,
            'redis_host' => '127.0.0.1',
            'redis_port' => 6379,
            'redis_password' => null,
            'redis_database' => 0,
            'file_storage_dir' => null,
            'whitelist' => [],
            'blacklist' => [],
        ], $config);

        $this->whitelist = $this->config['whitelist'];
        $this->blacklist = $this->config['blacklist'];
        
        $this->initializeStorage();
    }

    /**
     * Check if request is allowed
     * 
     * @param string $identifier Unique identifier (IP, user ID, etc.)
     * @param int|null $limit Maximum requests allowed
     * @param int|null $window Time window in seconds
     * @return array ['allowed' => bool, 'limit' => int, 'remaining' => int, 'reset' => int]
     */
    public function attempt(string $identifier, ?int $limit = null, ?int $window = null): array
    {
        // Check blacklist
        if ($this->isBlacklisted($identifier)) {
            return [
                'allowed' => false,
                'limit' => 0,
                'remaining' => 0,
                'reset' => 0,
                'reason' => 'blacklisted'
            ];
        }

        // Check whitelist
        if ($this->isWhitelisted($identifier)) {
            return [
                'allowed' => true,
                'limit' => PHP_INT_MAX,
                'remaining' => PHP_INT_MAX,
                'reset' => 0,
                'reason' => 'whitelisted'
            ];
        }

        $limit = $limit ?? $this->config['default_limit'];
        $window = $window ?? $this->config['default_window'];
        
        $key = $this->generateKey($identifier, $window);
        $current = $this->storage->get($key);
        
        if ($current >= $limit) {
            $reset = $this->storage->ttl($key);
            
            return [
                'allowed' => false,
                'limit' => $limit,
                'remaining' => 0,
                'reset' => $reset,
                'reason' => 'rate_limit_exceeded'
            ];
        }

        $newCount = $this->storage->increment($key, $window);
        $remaining = max(0, $limit - $newCount);
        $reset = $this->storage->ttl($key);

        return [
            'allowed' => true,
            'limit' => $limit,
            'remaining' => $remaining,
            'reset' => $reset
        ];
    }

    /**
     * Reset rate limit for an identifier
     * 
     * @param string $identifier
     * @return bool
     */
    public function reset(string $identifier): bool
    {
        $key = $this->generateKey($identifier, $this->config['default_window']);
        return $this->storage->reset($key);
    }

    /**
     * Add IP to whitelist
     * 
     * @param string $ip
     * @return void
     */
    public function addToWhitelist(string $ip): void
    {
        if (!in_array($ip, $this->whitelist)) {
            $this->whitelist[] = $ip;
        }
    }

    /**
     * Add IP to blacklist
     * 
     * @param string $ip
     * @return void
     */
    public function addToBlacklist(string $ip): void
    {
        if (!in_array($ip, $this->blacklist)) {
            $this->blacklist[] = $ip;
        }
    }

    /**
     * Check if identifier is whitelisted
     * 
     * @param string $identifier
     * @return bool
     */
    public function isWhitelisted(string $identifier): bool
    {
        return in_array($identifier, $this->whitelist);
    }

    /**
     * Check if identifier is blacklisted
     * 
     * @param string $identifier
     * @return bool
     */
    public function isBlacklisted(string $identifier): bool
    {
        return in_array($identifier, $this->blacklist);
    }

    /**
     * Get storage instance
     * 
     * @return StorageInterface
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * Initialize storage backend
     * 
     * @return void
     */
    private function initializeStorage(): void
    {
        $storageType = $this->config['storage'];

        switch ($storageType) {
            case 'redis':
                $this->storage = new RedisStorage(
                    $this->config['redis_host'],
                    $this->config['redis_port'],
                    $this->config['redis_password'],
                    $this->config['redis_database']
                );
                
                // Fallback to file storage if Redis is unavailable
                if (!$this->storage->isAvailable()) {
                    error_log("Redis unavailable, falling back to file storage");
                    $this->storage = new FileStorage($this->config['file_storage_dir']);
                }
                break;

            case 'file':
            default:
                $this->storage = new FileStorage($this->config['file_storage_dir']);
                break;
        }
    }

    /**
     * Generate unique key for rate limiting
     * 
     * @param string $identifier
     * @param int $window
     * @return string
     */
    private function generateKey(string $identifier, int $window): string
    {
        // Include window in key to support different time windows
        $timeSlot = floor(time() / $window);
        return "rate_limit:{$identifier}:{$window}:{$timeSlot}";
    }
}
