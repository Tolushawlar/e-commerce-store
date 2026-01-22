<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $storeId = $data['store_id'];
    
    try {
        // Get store customization data
        $stmt = $db->prepare("SELECT * FROM stores WHERE id = ?");
        $stmt->execute([$storeId]);
        $store = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$store) {
            echo json_encode(['success' => false, 'error' => 'Store not found']);
            exit;
        }
        
        // Generate store HTML
        $storeHTML = generateStoreHTML($store);
        
        // Create store directory
        $storeDir = "../stores/store-{$storeId}";
        if (!file_exists($storeDir)) {
            mkdir($storeDir, 0755, true);
        }
        
        // Save index.html
        file_put_contents("{$storeDir}/index.html", $storeHTML);
        
        echo json_encode(['success' => true, 'url' => "/stores/store-{$storeId}/"]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function generateStoreHTML($store) {
    $primaryColor = $store['primary_color'] ?? '#064E3B';
    $accentColor = $store['accent_color'] ?? '#BEF264';
    $fontFamily = $store['font_family'] ?? 'Plus Jakarta Sans';
    $buttonStyle = $store['button_style'] ?? 'rounded';
    $productGrid = $store['product_grid_columns'] ?? 4;
    
    $buttonClass = '';
    switch ($buttonStyle) {
        case 'square': $buttonClass = 'rounded-none'; break;
        case 'pill': $buttonClass = 'rounded-full'; break;
        default: $buttonClass = 'rounded-lg';
    }
    
    return "<!DOCTYPE html>
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
        {$store['custom_css']}
    </style>
</head>
<body class=\"bg-white\">
    <header class=\"sticky top-0 z-50 bg-white border-b border-gray-200\">
        <div class=\"max-w-7xl mx-auto px-6 h-16 flex items-center justify-between\">
            <div class=\"flex items-center gap-3\">
                <div class=\"w-8 h-8 {$buttonClass} flex items-center justify-center text-white\" style=\"background-color: var(--primary);\">
                    <span class=\"material-symbols-outlined text-lg\">shopping_bag</span>
                </div>
                <span class=\"text-xl font-bold\" style=\"color: var(--primary);\">{$store['store_name']}</span>
            </div>
            <div class=\"flex items-center gap-4\">
                " . ($store['show_search'] ? '<button class="p-2 text-gray-600 hover:text-gray-900"><span class="material-symbols-outlined">search</span></button>' : '') . "
                " . ($store['show_wishlist'] ? '<button class="p-2 text-gray-600 hover:text-gray-900"><span class="material-symbols-outlined">favorite</span></button>' : '') . "
                " . ($store['show_cart'] ? '<button class="p-2 text-gray-600 hover:text-gray-900"><span class="material-symbols-outlined">shopping_cart</span></button>' : '') . "
            </div>
        </div>
    </header>

    <section class=\"py-20 text-center text-white\" style=\"background: linear-gradient(135deg, var(--primary), var(--primary)dd);\">
        <div class=\"max-w-4xl mx-auto px-6\">
            <h1 class=\"text-5xl font-bold mb-4\">Welcome to {$store['store_name']}</h1>
            <p class=\"text-xl mb-8 opacity-90\">" . ($store['tagline'] ?? 'Your premium marketplace') . "</p>
            <p class=\"text-lg mb-8 opacity-80\">" . ($store['description'] ?? 'Discover amazing products at great prices') . "</p>
            <button class=\"px-8 py-3 {$buttonClass} font-bold text-lg\" style=\"background-color: var(--accent); color: var(--primary);\">
                Shop Now
            </button>
        </div>
    </section>

    <section class=\"py-16\">
        <div class=\"max-w-7xl mx-auto px-6\">
            <h2 class=\"text-3xl font-bold mb-8\" style=\"color: var(--primary);\">Featured Products</h2>
            <div class=\"grid grid-cols-{$productGrid} gap-6\">
                " . generateSampleProducts($productGrid, $buttonClass, $primaryColor) . "
            </div>
        </div>
    </section>

    " . ($store['footer_text'] ? "
    <footer class=\"bg-gray-900 text-white py-8\">
        <div class=\"max-w-7xl mx-auto px-6 text-center\">
            <p>{$store['footer_text']}</p>
            <div class=\"flex justify-center gap-4 mt-4\">
                " . ($store['social_facebook'] ? "<a href=\"{$store['social_facebook']}\" class=\"text-gray-400 hover:text-white\">Facebook</a>" : '') . "
                " . ($store['social_instagram'] ? "<a href=\"{$store['social_instagram']}\" class=\"text-gray-400 hover:text-white\">Instagram</a>" : '') . "
                " . ($store['social_twitter'] ? "<a href=\"{$store['social_twitter']}\" class=\"text-gray-400 hover:text-white\">Twitter</a>" : '') . "
            </div>
        </div>
    </footer>
    " : '') . "
</body>
</html>";
}

function generateSampleProducts($count, $buttonClass, $primaryColor) {
    $products = '';
    for ($i = 1; $i <= $count; $i++) {
        $price = rand(5000, 50000);
        $products .= "
            <div class=\"bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow\">
                <div class=\"aspect-square bg-gray-200\"></div>
                <div class=\"p-4\">
                    <h3 class=\"font-bold text-gray-800 mb-2\">Sample Product {$i}</h3>
                    <p class=\"font-bold\" style=\"color: {$primaryColor};\">₦{$price}</p>
                    <button class=\"w-full mt-3 py-2 {$buttonClass} font-bold text-white\" style=\"background-color: {$primaryColor};\">
                        Add to Cart
                    </button>
                </div>
            </div>
        ";
    }
    return $products;
}
?>