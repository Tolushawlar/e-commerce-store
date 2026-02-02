<?php

/**
 * Sync Template Files to Database
 * 
 * This script reads HTML templates from /store-templates/ directory
 * and updates the corresponding records in the store_templates table.
 * 
 * Usage: php sync_templates_to_db.php
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Models\Template;

$templateModel = new Template();

// Template file mappings
$templates = [
    1 => 'campmart-style.html',
    2 => 'minimal-clean.html',
    3 => 'bold-modern.html',
    4 => 'classic-ecommerce.html',
    5 => 'premium-luxury.html'
];

$templatesDir = dirname(__DIR__, 2) . '/store-templates';
$updated = 0;
$failed = 0;

echo "Starting template sync...\n\n";

foreach ($templates as $templateId => $filename) {
    $filePath = $templatesDir . '/' . $filename;

    if (!file_exists($filePath)) {
        echo "❌ Template file not found: {$filename}\n";
        $failed++;
        continue;
    }

    $htmlContent = file_get_contents($filePath);

    if ($htmlContent === false) {
        echo "❌ Failed to read file: {$filename}\n";
        $failed++;
        continue;
    }

    try {
        // Update the template in database
        $result = $templateModel->update($templateId, [
            'html_template' => $htmlContent
        ]);

        if ($result) {
            echo "✅ Updated template #{$templateId}: {$filename}\n";
            $updated++;
        } else {
            echo "⚠️  No changes for template #{$templateId}: {$filename}\n";
        }
    } catch (Exception $e) {
        echo "❌ Error updating template #{$templateId}: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Sync completed!\n";
echo "✅ Updated: {$updated}\n";
echo "❌ Failed: {$failed}\n";
echo str_repeat("=", 50) . "\n\n";

echo "Next steps:\n";
echo "1. Regenerate your stores from the admin dashboard\n";
echo "2. The updated templates will be used automatically\n";
