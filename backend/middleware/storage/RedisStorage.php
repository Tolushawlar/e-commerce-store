<?php

namespace App\Middleware\Storage;

use Redis;
use Exception;

/**
 * Redis-based Storage for Rate Limiting
 * Recommended for production and distributed deployments
 */
class RedisStorage implements StorageInterface
{
    private ?Redis $redis = null;
    private bool $available = false;

    public function __construct(
        string $host = '127.0.0.1',
        int $port = 6379,
        ?string $password = null,
        int $database = 0,
        int $timeout = 2
    ) {
        try {
            $this->redis = new Redis();
            $this->redis->connect($host, $port, $timeout);
            
            if ($password) {
                $this->redis->auth($password);
            }
            
            $this->redis->select($database);
            $this->available = true;
        } catch (Exception $e) {
            error_log("Redis connection failed: " . $e->getMessage());
            $this->available = false;
        }
    }

    public function get(string $key): int
    {
        if (!$this->available) {
            return 0;
        }

        try {
            $value = $this->redis->get($key);
            return $value !== false ? (int)$value : 0;
        } catch (Exception $e) {
            error_log("Redis get failed: " . $e->getMessage());
            return 0;
        }
    }

    public function increment(string $key, int $ttl): int
    {
        if (!$this->available) {
            return 0;
        }

        try {
            // Use MULTI/EXEC for atomic operation
            $this->redis->multi();
            $this->redis->incr($key);
            $this->redis->expire($key, $ttl);
            $result = $this->redis->exec();
            
            return $result[0] ?? 1;
        } catch (Exception $e) {
            error_log("Redis increment failed: " . $e->getMessage());
            return 0;
        }
    }

    public function reset(string $key): bool
    {
        if (!$this->available) {
            return false;
        }

        try {
            return $this->redis->del($key) > 0;
        } catch (Exception $e) {
            error_log("Redis reset failed: " . $e->getMessage());
            return false;
        }
    }

    public function ttl(string $key): int
    {
        if (!$this->available) {
            return 0;
        }

        try {
            $ttl = $this->redis->ttl($key);
            return $ttl > 0 ? $ttl : 0;
        } catch (Exception $e) {
            error_log("Redis TTL failed: " . $e->getMessage());
            return 0;
        }
    }

    public function isAvailable(): bool
    {
        if (!$this->available) {
            return false;
        }

        try {
            return $this->redis->ping() === '+PONG';
        } catch (Exception $e) {
            $this->available = false;
            return false;
        }
    }

    public function cleanup(): void
    {
        // Redis handles expiration automatically
        // No manual cleanup needed
    }

    public function __destruct()
    {
        if ($this->redis && $this->available) {
            try {
                $this->redis->close();
            } catch (Exception $e) {
                // Ignore errors on cleanup
            }
        }
    }
}
