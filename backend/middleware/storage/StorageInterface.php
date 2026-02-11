<?php

namespace App\Middleware\Storage;

/**
 * Storage Interface for Rate Limiting
 * Defines contract for different storage backends
 */
interface StorageInterface
{
    /**
     * Get the current count for a key
     * 
     * @param string $key The rate limit key
     * @return int Current count
     */
    public function get(string $key): int;

    /**
     * Increment the count for a key
     * 
     * @param string $key The rate limit key
     * @param int $ttl Time to live in seconds
     * @return int New count after increment
     */
    public function increment(string $key, int $ttl): int;

    /**
     * Reset/delete a key
     * 
     * @param string $key The rate limit key
     * @return bool Success status
     */
    public function reset(string $key): bool;

    /**
     * Get time until key expires
     * 
     * @param string $key The rate limit key
     * @return int Seconds until expiration, 0 if expired/not found
     */
    public function ttl(string $key): int;

    /**
     * Check if storage is available
     * 
     * @return bool Storage availability status
     */
    public function isAvailable(): bool;

    /**
     * Clean up expired entries (for file-based storage)
     * 
     * @return void
     */
    public function cleanup(): void;
}
