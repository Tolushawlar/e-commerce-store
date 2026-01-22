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
$id = end($segments);

switch ($method) {
    case 'GET':
        if (is_numeric($id)) {
            $stmt = $db->prepare("SELECT s.*, c.name as client_name FROM stores s JOIN clients c ON s.client_id = c.id WHERE s.id = ?");
            $stmt->execute([$id]);
            $store = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($store);
        } else {
            $stmt = $db->prepare("SELECT s.*, c.name as client_name FROM stores s JOIN clients c ON s.client_id = c.id ORDER BY s.created_at DESC");
            $stmt->execute();
            $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($stores);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data['client_id'] || !$data['store_name'] || !$data['store_slug']) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        try {
            $stmt = $db->prepare("INSERT INTO stores (client_id, store_name, store_slug, domain, template_id, primary_color, accent_color, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['client_id'],
                $data['store_name'],
                $data['store_slug'],
                $data['domain'] ?? null,
                $data['template_id'] ?? 1,
                $data['primary_color'] ?? '#064E3B',
                $data['accent_color'] ?? '#BEF264',
                $data['description'] ?? null
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to create store']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $stmt = $db->prepare("UPDATE stores SET store_name = ?, store_slug = ?, domain = ?, primary_color = ?, accent_color = ?, description = ?, status = ? WHERE id = ?");
            $result = $stmt->execute([
                $data['store_name'],
                $data['store_slug'],
                $data['domain'],
                $data['primary_color'],
                $data['accent_color'],
                $data['description'],
                $data['status'],
                $id
            ]);
            
            echo json_encode(['success' => $result]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'DELETE':
        try {
            $stmt = $db->prepare("DELETE FROM stores WHERE id = ?");
            $result = $stmt->execute([$id]);
            echo json_encode(['success' => $result]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>