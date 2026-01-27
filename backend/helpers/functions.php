<?php

/**
 * Global Helper Functions
 */

if (!function_exists('config')) {
    /**
     * Get configuration value by dot notation
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        static $config = null;

        if ($config === null) {
            $configFile = __DIR__ . '/../config/config.php';
            if (file_exists($configFile)) {
                $config = require $configFile;
            } else {
                $config = [];
            }
        }

        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}

if (!function_exists('base_path')) {
    /**
     * Get base path
     *
     * @param string $path
     * @return string
     */
    function base_path(string $path = ''): string
    {
        return dirname(__DIR__) . '/' . ltrim($path, '/');
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get storage path
     *
     * @param string $path
     * @return string
     */
    function storage_path(string $path = ''): string
    {
        return base_path('storage/' . ltrim($path, '/'));
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        // Convert string booleans to actual booleans
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}
