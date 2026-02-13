<?php
/**
 * Token Security Cleanup Cron Job
 * 
 * This script cleans up expired tokens and old security data.
 * Run this script periodically (e.g., daily) via cron:
 * 
 * Cron entry example (runs daily at 2:00 AM):
 * 0 2 * * * /usr/bin/php /path/to/your/project/backend/helpers/cleanup_tokens.php
 * 
 * Or on Windows Task Scheduler:
 * C:\path\to\php.exe C:\path\to\your\project\backend\helpers\cleanup_tokens.php
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Services\TokenSecurityService;

try {
    $securityService = new TokenSecurityService();
    $securityService->cleanup();
    
    echo "[" . date('Y-m-d H:i:s') . "] Token security cleanup completed successfully\n";
} catch (\Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error during cleanup: " . $e->getMessage() . "\n";
    exit(1);
}
