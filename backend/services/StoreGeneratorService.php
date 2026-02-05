<?php

namespace App\Services;

use App\Models\Template;

/**
 * Store Generator Service
 * Handles dynamic store HTML generation using templates
 */
class StoreGeneratorService
{
    private string $storesPath;
    private string $templatesPath;
    private Template $templateModel;

    public function __construct()
    {
        $this->storesPath = config('paths.stores');
        $this->templatesPath = config('paths.templates');
        $this->templateModel = new Template();
    }

    /**
     * Generate store files
     */
    public function generate(array $store): array
    {
        $storeId = $store['id'];
        $storeSlug = $store['store_slug'];
        $storeDir = $this->storesPath . "/{$storeSlug}";

        // Create store directory
        if (!file_exists($storeDir)) {
            mkdir($storeDir, 0755, true);
        }

        // Load template from database
        $templateId = $store['template_id'] ?? 1;
        $template = $this->templateModel->findWithTemplateData($templateId);

        // Fallback to default template if not found
        if (!$template || empty($template['html_template'])) {
            $template = $this->templateModel->getDefault();
        }

        // Generate HTML using template
        $html = $this->generateHTML($store, $template);

        // Save index.html
        file_put_contents("{$storeDir}/index.html", $html);

        // Generate config.json
        $config = $this->generateConfig($store);
        file_put_contents("{$storeDir}/config.json", json_encode($config, JSON_PRETTY_PRINT));

        // Copy store.js to the store directory
        $storeJsSource = dirname(__DIR__, 2) . '/app/assets/js/store/store.js';
        $storeJsDest = "{$storeDir}/store.js";
        if (file_exists($storeJsSource)) {
            copy($storeJsSource, $storeJsDest);
        }

        // Generate product.html using template
        $productHtml = $this->generateProductHTML($store, $template);
        file_put_contents("{$storeDir}/product.html", $productHtml);

        // Copy product-detail.js to the store directory
        $productJsSource = dirname(__DIR__, 2) . '/app/assets/js/store/product-detail.js';
        $productJsDest = "{$storeDir}/product-detail.js";
        if (file_exists($productJsSource)) {
            copy($productJsSource, $productJsDest);
        }

        // Copy cart.js to the store directory
        $cartJsSource = dirname(__DIR__, 2) . '/app/assets/js/cart.js';
        $cartJsDest = "{$storeDir}/cart.js";
        if (file_exists($cartJsSource)) {
            copy($cartJsSource, $cartJsDest);
        }

        // Copy checkout.js to the store directory
        $checkoutJsSource = dirname(__DIR__, 2) . '/app/assets/js/checkout.js';
        $checkoutJsDest = "{$storeDir}/checkout.js";
        if (file_exists($checkoutJsSource)) {
            copy($checkoutJsSource, $checkoutJsDest);
        }

        // Copy customer-auth.js to the store directory
        $customerAuthJsSource = dirname(__DIR__, 2) . '/app/assets/js/store/customer-auth.js';
        $customerAuthJsDest = "{$storeDir}/customer-auth.js";
        if (file_exists($customerAuthJsSource)) {
            copy($customerAuthJsSource, $customerAuthJsDest);
        }

        // Copy profile-header.js to the store directory
        $profileHeaderJsSource = dirname(__DIR__, 2) . '/app/assets/js/components/profile-header.js';
        $profileHeaderJsDest = "{$storeDir}/profile-header.js";
        if (file_exists($profileHeaderJsSource)) {
            copy($profileHeaderJsSource, $profileHeaderJsDest);
        }

        // Generate cart.html from template
        $cartHtml = $this->generateCartHTML($store);
        file_put_contents("{$storeDir}/cart.html", $cartHtml);

        // Generate checkout.html from template
        $checkoutHtml = $this->generateCheckoutHTML($store);
        file_put_contents("{$storeDir}/checkout.html", $checkoutHtml);

        // Generate login.html from template
        $loginHtml = $this->generateLoginHTML($store);
        file_put_contents("{$storeDir}/login.html", $loginHtml);

        // Generate profile.html from template
        $profileHtml = $this->generateProfileHTML($store);
        file_put_contents("{$storeDir}/profile.html", $profileHtml);

        // Generate orders.html from template
        $ordersHtml = $this->generateOrdersHTML($store);
        file_put_contents("{$storeDir}/orders.html", $ordersHtml);

        // Generate order-success.html from template
        $successHtml = $this->generateOrderSuccessHTML($store);
        file_put_contents("{$storeDir}/order-success.html", $successHtml);

        return [
            'store_id' => $storeId,
            'store_url' => "/stores/{$storeSlug}/",
            'files_generated' => ['index.html', 'config.json', 'store.js', 'product.html', 'product-detail.js', 'cart.js', 'checkout.js', 'customer-auth.js', 'profile-header.js', 'cart.html', 'checkout.html', 'login.html', 'profile.html', 'orders.html', 'order-success.html'],
            'template_used' => $template['name'] ?? 'Default'
        ];
    }

    /**
     * Generate store HTML from template with placeholder replacement
     */
    private function generateHTML(array $store, ?array $template): string
    {
        // If no template provided or no html_template, generate basic HTML
        if (!$template || empty($template['html_template'])) {
            return $this->generateFallbackHTML($store);
        }

        // Replace placeholders in template
        $html = $this->replacePlaceholders($template['html_template'], $store);

        return $html;
    }

    /**
     * Replace placeholders in template with actual store data
     */
    private function replacePlaceholders(string $template, array $store): string
    {
        $placeholders = [
            '{{store_name}}' => $store['store_name'] ?? 'My Store',
            '{{store_description}}' => $store['description'] ?? 'Welcome to our online store',
            '{{primary_color}}' => $store['primary_color'] ?? '#064E3B',
            '{{accent_color}}' => $store['accent_color'] ?? '#BEF264',
            '{{logo_url}}' => $store['logo_url'] ?? '',
            '{{tagline}}' => $store['tagline'] ?? 'Your premium marketplace',
            '{{store_id}}' => $store['id'] ?? 1,
        ];

        // Replace all placeholders
        $result = str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $template
        );

        return $result;
    }

    /**
     * Generate fallback HTML when no template available
     */
    private function generateFallbackHTML(array $store): string
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
            <div id=\"products-container\">
                <!-- Products will be loaded here via API -->
            </div>
        </div>
    </section>

    " . ($store['footer_text'] ? $this->generateFooter($store) : '') . "
    
    <script src=\"cart.js\"></script>
    <script src=\"store.js\"></script>
    <script>
        // Initialize store
        const storeConfig = {
            storeId: {$store['id']},
            store_id: {$store['id']},
            apiUrl: window.location.origin + '/api',
            groupByCategory: " . ($store['group_by_category'] ? 'true' : 'false') . ",
            productGridColumns: {$productGrid},
            showCategoryImages: " . (isset($store['show_category_images']) && $store['show_category_images'] ? 'true' : 'false') . "
        };
        window.storeConfig = storeConfig;
        loadProducts(storeConfig);
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
     * Generate product detail page HTML
     */
    private function generateProductHTML(array $store, ?array $template): string
    {
        $primaryColor = $store['primary_color'] ?? '#064E3B';
        $accentColor = $store['accent_color'] ?? '#BEF264';
        $fontFamily = $store['font_family'] ?? 'Plus Jakarta Sans';

        return "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title id=\"page-title\">Product Details</title>
    
    <!-- Tailwind CSS -->
    <script src=\"https://cdn.tailwindcss.com\"></script>
    
    <!-- Fonts -->
    <link href=\"https://fonts.googleapis.com/css2?family=" . str_replace(' ', '+', $fontFamily) . ":wght@300;400;500;600;700;800&display=swap\" rel=\"stylesheet\">
    <link href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap\" rel=\"stylesheet\">
    
    <!-- Swiper CSS for Image Carousel -->
    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css\"/>
    
    <style>
        :root {
            --primary: {$primaryColor};
            --accent: {$accentColor};
        }
        body { font-family: '{$fontFamily}', sans-serif; }
        
        .swiper-button-next, .swiper-button-prev {
            color: var(--primary);
            background: white;
            padding: 20px;
            border-radius: 50%;
            width: 44px;
            height: 44px;
        }
        
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 20px;
        }
        
        .swiper-pagination-bullet-active {
            background: var(--primary);
        }
        
        .thumbnail-swiper .swiper-slide {
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.3s;
        }
        
        .thumbnail-swiper .swiper-slide-thumb-active {
            opacity: 1;
            border-color: var(--primary);
        }
        
        .product-info-skeleton {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
    </style>
</head>
<body class=\"bg-gray-50\">
    <!-- Header -->
    <header class=\"sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm\">
        <div class=\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between\">
            <div class=\"flex items-center gap-3\">
                <a href=\"index.html\" class=\"flex items-center gap-3\">
                    <img id=\"store-logo\" src=\"\" alt=\"Store Logo\" class=\"h-10 w-auto hidden\">
                    <span id=\"store-name\" class=\"text-xl font-bold\" style=\"color: var(--primary);\"></span>
                </a>
            </div>
            <div class=\"flex items-center gap-4\">
                <a href=\"index.html\" class=\"text-gray-600 hover:text-gray-900 flex items-center gap-2\">
                    <span class=\"material-symbols-outlined\">arrow_back</span>
                    <span class=\"hidden sm:inline\">Back to Store</span>
                </a>
                <a href=\"cart.html\" class=\"p-2 text-gray-600 hover:text-gray-900 relative\">
                    <span class=\"material-symbols-outlined\">shopping_cart</span>
                    <span id=\"cart-badge\" class=\"absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden\">0</span>
                </a>
            </div>
        </div>
    </header>

    <main class=\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8\">
        <!-- Breadcrumb -->
        <nav class=\"mb-6 text-sm\" id=\"breadcrumb\">
            <ol class=\"flex items-center gap-2 text-gray-500\">
                <li><a href=\"index.html\" class=\"hover:text-gray-900\">Home</a></li>
                <li><span class=\"material-symbols-outlined text-xs\">chevron_right</span></li>
                <li id=\"breadcrumb-category\">Loading...</li>
                <li><span class=\"material-symbols-outlined text-xs\">chevron_right</span></li>
                <li class=\"text-gray-900\" id=\"breadcrumb-product\">Loading...</li>
            </ol>
        </nav>

        <!-- Product Details Section -->
        <div class=\"grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mb-16\">
            <!-- Left: Image Gallery -->
            <div class=\"space-y-4\">
                <!-- Main Image Swiper -->
                <div class=\"swiper main-swiper rounded-2xl overflow-hidden shadow-lg bg-white\">
                    <div class=\"swiper-wrapper\" id=\"main-swiper-wrapper\">
                        <div class=\"swiper-slide flex items-center justify-center bg-gray-100\" style=\"min-height: 500px;\">
                            <div class=\"product-info-skeleton w-full h-full bg-gray-200\"></div>
                        </div>
                    </div>
                    <div class=\"swiper-button-next\"></div>
                    <div class=\"swiper-button-prev\"></div>
                    <div class=\"swiper-pagination\"></div>
                </div>
                
                <!-- Thumbnail Swiper -->
                <div class=\"swiper thumbnail-swiper\">
                    <div class=\"swiper-wrapper\" id=\"thumbnail-swiper-wrapper\"></div>
                </div>
            </div>

            <!-- Right: Product Information -->
            <div class=\"space-y-6\">
                <!-- Category Badge -->
                <div id=\"category-badge-container\" class=\"flex items-center gap-2\">
                    <div class=\"product-info-skeleton h-6 w-32 bg-gray-200 rounded-full\"></div>
                </div>

                <!-- Product Title -->
                <div>
                    <h1 id=\"product-name\" class=\"text-3xl lg:text-4xl font-bold text-gray-900 mb-2\">
                        <div class=\"product-info-skeleton h-10 bg-gray-200 rounded w-3/4\"></div>
                    </h1>
                    <p id=\"product-sku\" class=\"text-sm text-gray-500\"></p>
                </div>

                <!-- Price and Stock -->
                <div class=\"flex items-center gap-4 pb-6 border-b border-gray-200\">
                    <div id=\"product-price\" class=\"text-4xl font-bold\" style=\"color: var(--primary);\">
                        <div class=\"product-info-skeleton h-12 w-40 bg-gray-200 rounded\"></div>
                    </div>
                    <div id=\"stock-badge\" class=\"flex items-center gap-2\">
                        <div class=\"product-info-skeleton h-6 w-24 bg-gray-200 rounded-full\"></div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <h3 class=\"text-lg font-semibold mb-3 text-gray-900\">Description</h3>
                    <div id=\"product-description\" class=\"text-gray-600 leading-relaxed\">
                        <div class=\"product-info-skeleton h-4 bg-gray-200 rounded mb-2\"></div>
                        <div class=\"product-info-skeleton h-4 bg-gray-200 rounded mb-2\"></div>
                        <div class=\"product-info-skeleton h-4 bg-gray-200 rounded w-2/3\"></div>
                    </div>
                </div>

                <!-- Product Details Grid -->
                <div class=\"bg-gray-50 rounded-xl p-6\">
                    <h3 class=\"text-lg font-semibold mb-4 text-gray-900\">Product Details</h3>
                    <div class=\"grid grid-cols-2 gap-4 text-sm\">
                        <div>
                            <p class=\"text-gray-500 mb-1\">Category</p>
                            <p id=\"detail-category\" class=\"font-semibold text-gray-900\">-</p>
                        </div>
                        <div>
                            <p class=\"text-gray-500 mb-1\">Stock</p>
                            <p id=\"detail-stock\" class=\"font-semibold text-gray-900\">-</p>
                        </div>
                        <div>
                            <p class=\"text-gray-500 mb-1\">SKU</p>
                            <p id=\"detail-sku\" class=\"font-semibold text-gray-900\">-</p>
                        </div>
                        <div>
                            <p class=\"text-gray-500 mb-1\">Status</p>
                            <p id=\"detail-status\" class=\"font-semibold\">-</p>
                        </div>
                    </div>
                </div>

                <!-- Quantity Selector and Add to Cart -->
                <div class=\"space-y-4\">
                    <div class=\"flex items-center gap-4\">
                        <label class=\"text-sm font-semibold text-gray-900\">Quantity:</label>
                        <div class=\"flex items-center border-2 border-gray-300 rounded-lg overflow-hidden\">
                            <button id=\"qty-decrease\" class=\"px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold\" style=\"border-right: 2px solid #e5e7eb;\">
                                <span class=\"material-symbols-outlined\">remove</span>
                            </button>
                            <input type=\"number\" id=\"quantity\" value=\"1\" min=\"1\" class=\"w-20 text-center font-semibold py-2 border-none focus:outline-none focus:ring-0\" />
                            <button id=\"qty-increase\" class=\"px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold\" style=\"border-left: 2px solid #e5e7eb;\">
                                <span class=\"material-symbols-outlined\">add</span>
                            </button>
                        </div>
                    </div>

                    <div class=\"flex gap-3\">
                        <button id=\"add-to-cart\" class=\"flex-1 py-4 rounded-lg font-bold text-lg text-white transition-all duration-200 hover:brightness-110 shadow-md flex items-center justify-center gap-2\" style=\"background-color: var(--primary);\">
                            <span class=\"material-symbols-outlined\">shopping_cart</span>
                            Add to Cart
                        </button>
                        <button class=\"px-6 py-4 border-2 rounded-lg font-bold text-lg transition-all duration-200 hover:bg-gray-50\" style=\"border-color: var(--primary); color: var(--primary);\">
                            <span class=\"material-symbols-outlined\">favorite_border</span>
                        </button>
                    </div>

                    <button class=\"w-full py-4 rounded-lg font-bold text-lg text-white transition-all duration-200 hover:brightness-110\" style=\"background-color: var(--accent);\">
                        Buy Now
                    </button>
                </div>

                <!-- Share and Actions -->
                <div class=\"pt-6 border-t border-gray-200 flex items-center justify-between\">
                    <div class=\"flex items-center gap-3\">
                        <span class=\"text-sm text-gray-600\">Share:</span>
                        <button class=\"p-2 rounded-full hover:bg-gray-100 text-gray-600\">
                            <span class=\"material-symbols-outlined\">share</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        <section class=\"mt-16\">
            <div class=\"flex items-center justify-between mb-8\">
                <h2 class=\"text-3xl font-bold\" style=\"color: var(--primary);\">More from this Category</h2>
                <a href=\"index.html\" class=\"text-sm font-semibold flex items-center gap-1 hover:underline\" style=\"color: var(--primary);\">
                    View All
                    <span class=\"material-symbols-outlined text-lg\">arrow_forward</span>
                </a>
            </div>
            
            <div id=\"related-products\" class=\"grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6\">
                <div class=\"product-info-skeleton bg-white rounded-xl p-4\" style=\"height: 350px;\"></div>
                <div class=\"product-info-skeleton bg-white rounded-xl p-4\" style=\"height: 350px;\"></div>
                <div class=\"product-info-skeleton bg-white rounded-xl p-4\" style=\"height: 350px;\"></div>
                <div class=\"product-info-skeleton bg-white rounded-xl p-4\" style=\"height: 350px;\"></div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class=\"bg-gray-900 text-white mt-20\">
        <div class=\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12\">
            <div class=\"text-center\">
                <p id=\"footer-store-name\" class=\"text-xl font-bold mb-2\"></p>
                <p class=\"text-gray-400\">© 2026 All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Swiper JS -->
    <script src=\"https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js\"></script>
    
    <!-- Cart Service -->
    <script src=\"cart.js\"></script>
    
    <!-- Product Detail Script -->
    <script src=\"product-detail.js\"></script>
</body>
</html>";
    }

    /**
     * Generate cart.html from template
     */
    private function generateCartHTML(array $store): string
    {
        $templatePath = $this->templatesPath . '/cart.html';
        if (file_exists($templatePath)) {
            $html = file_get_contents($templatePath);
            return $this->replacePlaceholders($html, $store);
        }
        return '';
    }

    /**
     * Generate checkout.html from template
     */
    private function generateCheckoutHTML(array $store): string
    {
        $templatePath = $this->templatesPath . '/checkout.html';
        if (file_exists($templatePath)) {
            $html = file_get_contents($templatePath);
            return $this->replacePlaceholders($html, $store);
        }
        return '';
    }

    /**
     * Generate order-success.html from template
     */
    private function generateOrderSuccessHTML(array $store): string
    {
        $templatePath = $this->templatesPath . '/order-success.html';
        if (file_exists($templatePath)) {
            $html = file_get_contents($templatePath);
            return $this->replacePlaceholders($html, $store);
        }
        return '';
    }

    /**
     * Generate login.html from template
     */
    private function generateLoginHTML(array $store): string
    {
        $templatePath = $this->templatesPath . '/login.html';
        if (file_exists($templatePath)) {
            $html = file_get_contents($templatePath);
            return $this->replacePlaceholders($html, $store);
        }
        return '';
    }

    /**
     * Generate profile.html from template
     */
    private function generateProfileHTML(array $store): string
    {
        $templatePath = $this->templatesPath . '/profile.html';
        if (file_exists($templatePath)) {
            $html = file_get_contents($templatePath);
            return $this->replacePlaceholders($html, $store);
        }
        return '';
    }

    /**
     * Generate orders.html from template
     */
    private function generateOrdersHTML(array $store): string
    {
        $templatePath = $this->templatesPath . '/orders.html';
        if (file_exists($templatePath)) {
            $html = file_get_contents($templatePath);
            return $this->replacePlaceholders($html, $store);
        }
        return '';
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
                'show_wishlist' => (bool)$store['show_wishlist'],
                'group_by_category' => (bool)($store['group_by_category'] ?? false),
                'show_category_images' => (bool)($store['show_category_images'] ?? true)
            ]
        ];
    }
}
