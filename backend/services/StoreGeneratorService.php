<?php

namespace App\Services;

/**
 * Store Generator Service
 * Handles dynamic store HTML generation
 */
class StoreGeneratorService
{
    private string $storesPath;
    private string $templatesPath;

    public function __construct()
    {
        $this->storesPath = config('paths.stores');
        $this->templatesPath = config('paths.templates');
    }

    /**
     * Generate store files
     */
    public function generate(array $store): array
    {
        $storeId = $store['id'];
        $storeDir = $this->storesPath . "/store-{$storeId}";

        // Create store directory
        if (!file_exists($storeDir)) {
            mkdir($storeDir, 0755, true);
        }

        // Generate HTML
        $html = $this->generateHTML($store);

        // Save index.html
        file_put_contents("{$storeDir}/index.html", $html);

        // Generate config.json
        $config = $this->generateConfig($store);
        file_put_contents("{$storeDir}/config.json", json_encode($config, JSON_PRETTY_PRINT));

        // Copy store.js to the store directory
        $storeJsSource = dirname(__DIR__, 2) . '/app/assets/js/store.js';
        $storeJsDest = "{$storeDir}/store.js";
        if (file_exists($storeJsSource)) {
            copy($storeJsSource, $storeJsDest);
        }

        return [
            'store_id' => $storeId,
            'store_url' => "/stores/store-{$storeId}/",
            'files_generated' => ['index.html', 'config.json', 'store.js']
        ];
    }

    /**
     * Generate store HTML
     */
    private function generateHTML(array $store): string
    {
        $primaryColor = $store['primary_color'] ?? '#064E3B';
        $accentColor = $store['accent_color'] ?? '#BEF264';
        $fontFamily = $store['font_family'] ?? 'Plus Jakarta Sans';
        $buttonStyle = $store['button_style'] ?? 'rounded';
        $productGrid = $store['product_grid_columns'] ?? 4;

        $buttonClass = match ($buttonStyle) {
            'square' => 'rounded-none',
            'pill' => 'rounded-full',
            default => 'rounded-lg'
        };

        $html = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$store['store_name']}</title>
    <script src=\"https://cdn.tailwindcss.com\"></script>
    <link href=\"https://fonts.googleapis.com/css2?family=" . str_replace(' ', '+', $fontFamily) . ":wght@300;400;500;600;700;800&display=swap\" rel=\"stylesheet\">
    <link href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap\" rel=\"stylesheet\">
    <style>
        :root {
            --primary: {$primaryColor};
            --accent: {$accentColor};
        }
        body { font-family: '{$fontFamily}', sans-serif; }
        " . ($store['custom_css'] ?? '') . "
    </style>
</head>
<body class=\"bg-white\">
    <!-- Header -->
    <header class=\"sticky top-0 z-50 bg-white border-b border-gray-200\">
        <div class=\"max-w-7xl mx-auto px-6 h-16 flex items-center justify-between\">
            <div class=\"flex items-center gap-3\">
                " . (isset($store['logo_url']) && $store['logo_url'] ?
            "<img src=\"{$store['logo_url']}\" alt=\"{$store['store_name']}\" class=\"h-10 w-auto\">" :
            "<div class=\"w-8 h-8 {$buttonClass} flex items-center justify-center text-white\" style=\"background-color: var(--primary);\">
                        <span class=\"material-symbols-outlined text-lg\">shopping_bag</span>
                    </div>"
        ) . "
                <span class=\"text-xl font-bold\" style=\"color: var(--primary);\">{$store['store_name']}</span>
            </div>
            <div class=\"flex items-center gap-4\">
                " . ($store['show_search'] ? '<button class="p-2 text-gray-600 hover:text-gray-900"><span class="material-symbols-outlined">search</span></button>' : '') . "
                " . ($store['show_wishlist'] ? '<button class="p-2 text-gray-600 hover:text-gray-900"><span class="material-symbols-outlined">favorite</span></button>' : '') . "
                " . ($store['show_cart'] ? '<button class="p-2 text-gray-600 hover:text-gray-900"><span class="material-symbols-outlined">shopping_cart</span></button>' : '') . "
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class=\"py-20 text-center text-white\" style=\"" .
            (isset($store['hero_background_url']) && $store['hero_background_url'] ?
                "background-image: linear-gradient(135deg, rgba(6, 78, 59, 0.8), rgba(6, 78, 59, 0.85)), url('{$store['hero_background_url']}'); background-size: cover; background-position: center; background-repeat: no-repeat;" :
                "background: linear-gradient(135deg, var(--primary), var(--primary)dd);"
            ) . "\">
        <div class=\"max-w-4xl mx-auto px-6\">
            <h1 class=\"text-5xl font-bold mb-4\">Welcome to {$store['store_name']}</h1>
            <p class=\"text-xl mb-8 opacity-90\">" . ($store['tagline'] ?? 'Your premium marketplace') . "</p>
            <p class=\"text-lg mb-8 opacity-80\">" . ($store['description'] ?? 'Discover amazing products at great prices') . "</p>
            <button class=\"px-8 py-3 {$buttonClass} font-bold text-lg\" style=\"background-color: var(--accent); color: var(--primary);\">
                Shop Now
            </button>
        </div>
    </section>

    <!-- Products Section -->
    <section class=\"py-16\">
        <div class=\"max-w-7xl mx-auto px-6\">
            <h2 class=\"text-3xl font-bold mb-8\" style=\"color: var(--primary);\">Featured Products</h2>
            <div class=\"grid grid-cols-{$productGrid} gap-6\" id=\"products-grid\">
                <!-- Products will be loaded here via API -->
            </div>
        </div>
    </section>

    " . ($store['footer_text'] ? $this->generateFooter($store) : '') . "
    
    <script src=\"store.js\"></script>
    <script>
        // Initialize store
        const storeConfig = {
            storeId: {$store['id']},
            apiUrl: window.location.origin + '/api'
        };
        loadProducts(storeConfig.storeId);
    </script>
</body>
</html>";

        return $html;
    }

    /**
     * Generate footer HTML
     */
    private function generateFooter(array $store): string
    {
        $social = [];

        if (!empty($store['social_facebook'])) {
            $social[] = "<a href=\"{$store['social_facebook']}\" class=\"text-gray-400 hover:text-white\">Facebook</a>";
        }
        if (!empty($store['social_instagram'])) {
            $social[] = "<a href=\"{$store['social_instagram']}\" class=\"text-gray-400 hover:text-white\">Instagram</a>";
        }
        if (!empty($store['social_twitter'])) {
            $social[] = "<a href=\"{$store['social_twitter']}\" class=\"text-gray-400 hover:text-white\">Twitter</a>";
        }

        $socialLinks = !empty($social) ? '<div class="flex justify-center gap-4 mt-4">' . implode('', $social) . '</div>' : '';

        return "
    <footer class=\"bg-gray-900 text-white py-8\">
        <div class=\"max-w-7xl mx-auto px-6 text-center\">
            <p>{$store['footer_text']}</p>
            {$socialLinks}
        </div>
    </footer>";
    }

    /**
     * Generate store configuration JSON
     */
    private function generateConfig(array $store): array
    {
        return [
            'store_id' => $store['id'],
            'store_name' => $store['store_name'],
            'store_slug' => $store['store_slug'],
            'colors' => [
                'primary' => $store['primary_color'],
                'accent' => $store['accent_color']
            ],
            'settings' => [
                'font_family' => $store['font_family'],
                'button_style' => $store['button_style'],
                'product_grid_columns' => $store['product_grid_columns'],
                'show_search' => (bool)$store['show_search'],
                'show_cart' => (bool)$store['show_cart'],
                'show_wishlist' => (bool)$store['show_wishlist']
            ]
        ];
    }
}
