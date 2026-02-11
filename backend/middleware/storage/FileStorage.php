<?php

namespace App\Middleware\Storage;

/**
 * File-based Storage for Rate Limiting
 * Suitable for development and single-server deployments
 */
class FileStorage implements StorageInterface
{
    private string $storageDir;
    private int $cleanupProbability = 100; // 1% chance on each request

    public function __construct(?string $storageDir = null)
    {
        $this->storageDir = $storageDir ?? sys_get_temp_dir() . '/rate_limits';
        
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }

        // Probabilistic cleanup
        if (rand(1, 10000) <= $this->cleanupProbability) {
            $this->cleanup();
        }
    }

    public function get(string $key): int
    {
        $file = $this->getFilePath($key);
        
        if (!file_exists($file)) {
            return 0;
        }

        $data = json_decode(file_get_contents($file), true);
        
        if (!$data || $data['expires'] < time()) {
            $this->reset($key);
            return 0;
        }

        return $data['count'] ?? 0;
    }

    public function increment(string $key, int $ttl): int
    {
        $file = $this->getFilePath($key);
        $currentTime = time();
        
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            
            if ($data && $data['expires'] >= $currentTime) {
                $data['count']++;
                file_put_contents($file, json_encode($data), LOCK_EX);
                return $data['count'];
            }
        }

        // Create new entry
        $data = [
            'count' => 1,
            'expires' => $currentTime + $ttl,
            'created' => $currentTime
        ];
        
        file_put_contents($file, json_encode($data), LOCK_EX);
        return 1;
    }

    public function reset(string $key): bool
    {
        $file = $this->getFilePath($key);
        
        if (file_exists($file)) {
            return unlink($file);
        }
        
        return true;
    }

    public function ttl(string $key): int
    {
        $file = $this->getFilePath($key);
        
        if (!file_exists($file)) {
            return 0;
        }

        $data = json_decode(file_get_contents($file), true);
        
        if (!$data) {
            return 0;
        }

        $remaining = $data['expires'] - time();
        return max(0, $remaining);
    }

    public function isAvailable(): bool
    {
        return is_dir($this->storageDir) && is_writable($this->storageDir);
    }

    public function cleanup(): void
    {
        if (!is_dir($this->storageDir)) {
            return;
        }

        $currentTime = time();
        $files = glob($this->storageDir . '/*.json');
        
        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            
            if ($data && $data['expires'] < $currentTime) {
                unlink($file);
            }
        }
    }

    private function getFilePath(string $key): string
    {
        $hash = md5($key);
        return $this->storageDir . '/' . $hash . '.json';
    }
}
