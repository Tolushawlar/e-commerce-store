<?php

/**
 * Router script for PHP built-in server
 * This handles routing for the built-in PHP server
 */

// Only run this in CLI server mode
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $path;

    // If it's a directory, try to serve index.html
    if (is_dir($file)) {
        $file = rtrim($file, '/') . '/index.html';
    }

    // List of file extensions to serve directly
    $allowedExtensions = ['json', 'html', 'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico'];
    $extension = pathinfo($file, PATHINFO_EXTENSION);

    // Serve static files if they exist
    if (is_file($file) && in_array($extension, $allowedExtensions)) {
        return false; // Let PHP serve the file
    }
}

// All other requests go through index.php
require_once __DIR__ . '/index.php';
