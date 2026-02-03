<?php

/**
 * Populate store templates with HTML content from different template files
 * Run this script to load unique HTML layouts for each template
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Models\Template;
use App\Config\Database;

$templatesDir = __DIR__ . '/../../store-templates/';

// Template configuration: ID => [file, description]
$templates = [
    1 => [
        'file' => 'campmart-style.html',
        'description' => 'Modern marketplace design with bold colors and clean layout inspired by campus commerce'
    ],
    2 => [
        'file' => 'minimal-clean.html',
        'description' => 'Clean minimalist template with focus on whitespace and simplicity'
    ],
    3 => [
        'file' => 'bold-modern.html',
        'description' => 'Bold and vibrant design with energetic layout and strong CTAs for modern brands'
    ],
    4 => [
        'file' => 'classic-ecommerce.html',
        'description' => 'Traditional ecommerce layout with sidebar navigation and proven conversion design'
    ],
    5 => [
        'file' => 'premium-luxury.html',
        'description' => 'Elegant luxury template with sophisticated typography and refined spacing'
    ]
];

try {
    $db = Database::getConnection();

    foreach ($templates as $id => $config) {
        $filePath = $templatesDir . $config['file'];

        if (!file_exists($filePath)) {
            echo "⚠️  Warning: Template file not found: {$config['file']}\n";
            continue;
        }

        $html = file_get_contents($filePath);

        $stmt = $db->prepare("
            UPDATE store_templates 
            SET html_template = ?, 
                description = ?
            WHERE id = ?
        ");

        $stmt->execute([$html, $config['description'], $id]);

        echo "✓ Updated template ID {$id}: {$config['file']}\n";
    }

    echo "\n✅ All " . count($templates) . " templates populated successfully!\n";
    echo "\n📋 Template Summary:\n";
    echo "  1. CampMart Style - Modern marketplace (campmart-style.html)\n";
    echo "  2. Minimal Clean - Minimalist design (minimal-clean.html)\n";
    echo "  3. Bold Modern - Vibrant & energetic (bold-modern.html)\n";
    echo "  4. Classic Ecommerce - Traditional layout (classic-ecommerce.html)\n";
    echo "  5. Premium Luxury - Elegant & sophisticated (premium-luxury.html)\n";
    echo "\n🎨 Each template has a unique layout and design!\n";
    echo "\n📝 Supported placeholders:\n";
    echo "  - {{store_name}} - Store name\n";
    echo "  - {{store_description}} - Store description\n";
    echo "  - {{primary_color}} - Primary brand color\n";
    echo "  - {{accent_color}} - Accent brand color\n";
    echo "  - {{logo_url}} - Store logo URL\n";
    echo "  - {{store_id}} - Store ID\n";
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
