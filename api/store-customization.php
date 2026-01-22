<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));
$storeId = end($segments);

switch ($method) {
    case 'GET':
        if (is_numeric($storeId)) {
            // Get store customization data
            $stmt = $db->prepare("
                SELECT s.*, c.name as client_name 
                FROM stores s 
                JOIN clients c ON s.client_id = c.id 
                WHERE s.id = ?
            ");
            $stmt->execute([$storeId]);
            $store = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($store) {
                // Get store sections
                $stmt = $db->prepare("
                    SELECT * FROM store_sections 
                    WHERE store_id = ? 
                    ORDER BY sort_order ASC
                ");
                $stmt->execute([$storeId]);
                $store['sections'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get navigation items
                $stmt = $db->prepare("
                    SELECT * FROM store_navigation 
                    WHERE store_id = ? AND is_active = 1 
                    ORDER BY sort_order ASC
                ");
                $stmt->execute([$storeId]);
                $store['navigation'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            echo json_encode($store);
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $db->beginTransaction();
            
            // Update store basic info
            $stmt = $db->prepare("
                UPDATE stores SET 
                    store_name = ?, 
                    store_slug = ?, 
                    tagline = ?,
                    description = ?,
                    primary_color = ?, 
                    accent_color = ?,
                    logo_url = ?,
                    hero_background_url = ?,
                    header_style = ?,
                    product_grid_columns = ?,
                    font_family = ?,
                    button_style = ?,
                    show_search = ?,
                    show_cart = ?,
                    show_wishlist = ?,
                    footer_text = ?,
                    social_facebook = ?,
                    social_instagram = ?,
                    social_twitter = ?,
                    custom_css = ?,
                    status = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['store_name'],
                $data['store_slug'],
                $data['tagline'] ?? null,
                $data['description'] ?? null,
                $data['primary_color'] ?? '#064E3B',
                $data['accent_color'] ?? '#BEF264',
                $data['logo_url'] ?? null,
                $data['hero_background_url'] ?? null,
                $data['header_style'] ?? 'default',
                $data['product_grid_columns'] ?? 4,
                $data['font_family'] ?? 'Plus Jakarta Sans',
                $data['button_style'] ?? 'rounded',
                $data['show_search'] ?? true,
                $data['show_cart'] ?? true,
                $data['show_wishlist'] ?? false,
                $data['footer_text'] ?? null,
                $data['social_facebook'] ?? null,
                $data['social_instagram'] ?? null,
                $data['social_twitter'] ?? null,
                $data['custom_css'] ?? null,
                $data['status'] ?? 'active',
                $storeId
            ]);
            
            // Update sections if provided
            if (isset($data['sections'])) {
                foreach ($data['sections'] as $section) {
                    if (isset($section['id'])) {
                        // Update existing section
                        $stmt = $db->prepare("
                            UPDATE store_sections SET 
                                title = ?, 
                                content = ?, 
                                background_color = ?, 
                                text_color = ?, 
                                is_visible = ?,
                                sort_order = ?
                            WHERE id = ? AND store_id = ?
                        ");
                        $stmt->execute([
                            $section['title'],
                            $section['content'],
                            $section['background_color'] ?? null,
                            $section['text_color'] ?? null,
                            $section['is_visible'] ?? true,
                            $section['sort_order'] ?? 0,
                            $section['id'],
                            $storeId
                        ]);
                    } else {
                        // Create new section
                        $stmt = $db->prepare("
                            INSERT INTO store_sections 
                            (store_id, section_type, title, content, background_color, text_color, is_visible, sort_order) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([
                            $storeId,
                            $section['section_type'],
                            $section['title'],
                            $section['content'],
                            $section['background_color'] ?? null,
                            $section['text_color'] ?? null,
                            $section['is_visible'] ?? true,
                            $section['sort_order'] ?? 0
                        ]);
                    }
                }
            }
            
            // Update navigation if provided
            if (isset($data['navigation'])) {
                // Clear existing navigation
                $stmt = $db->prepare("DELETE FROM store_navigation WHERE store_id = ?");
                $stmt->execute([$storeId]);
                
                // Insert new navigation items
                foreach ($data['navigation'] as $nav) {
                    $stmt = $db->prepare("
                        INSERT INTO store_navigation 
                        (store_id, label, url, target, sort_order, is_active) 
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $storeId,
                        $nav['label'],
                        $nav['url'],
                        $nav['target'] ?? '_self',
                        $nav['sort_order'] ?? 0,
                        $nav['is_active'] ?? true
                    ]);
                }
            }
            
            $db->commit();
            echo json_encode(['success' => true]);
            
        } catch (PDOException $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>