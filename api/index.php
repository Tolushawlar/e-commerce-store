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
$path = str_replace('/ecommerce-platform/api/', '', $path);
$segments = explode('/', trim($path, '/'));

$resource = $segments[0] ?? '';
$id = $segments[1] ?? null;

switch ($resource) {
    case 'products':
        handleProducts($db, $method, $id);
        break;
    case 'orders':
        handleOrders($db, $method, $id);
        break;
    case 'clients':
        handleClients($db, $method, $id);
        break;
    case 'stores':
        handleStores($db, $method, $id);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
}

function handleProducts($db, $method, $id) {
    switch ($method) {
        case 'GET':
            if ($id === 'featured') {
                $stmt = $db->prepare("SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 8");
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($products);
            } elseif ($id) {
                $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($product);
            } else {
                $store_id = $_GET['store_id'] ?? null;
                if ($store_id) {
                    $stmt = $db->prepare("SELECT * FROM products WHERE store_id = ? ORDER BY created_at DESC");
                    $stmt->execute([$store_id]);
                } else {
                    $stmt = $db->prepare("SELECT * FROM products ORDER BY created_at DESC");
                    $stmt->execute();
                }
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($products);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $db->prepare("INSERT INTO products (store_id, name, description, price, category, image_url, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['store_id'],
                $data['name'],
                $data['description'],
                $data['price'],
                $data['category'],
                $data['image_url'],
                $data['stock_quantity'] ?? 0
            ]);
            echo json_encode(['success' => $result, 'id' => $db->lastInsertId()]);
            break;
    }
}

function handleOrders($db, $method, $id) {
    switch ($method) {
        case 'GET':
            $store_id = $_GET['store_id'] ?? null;
            if ($store_id) {
                $stmt = $db->prepare("SELECT * FROM orders WHERE store_id = ? ORDER BY created_at DESC");
                $stmt->execute([$store_id]);
            } else {
                $stmt = $db->prepare("SELECT * FROM orders ORDER BY created_at DESC");
                $stmt->execute();
            }
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($orders);
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $db->prepare("INSERT INTO orders (store_id, customer_name, customer_email, customer_phone, total_amount) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['store_id'],
                $data['customer_name'],
                $data['customer_email'],
                $data['customer_phone'],
                $data['total_amount']
            ]);
            echo json_encode(['success' => $result, 'id' => $db->lastInsertId()]);
            break;
    }
}

function handleClients($db, $method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
                $stmt->execute([$id]);
                $client = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($client);
            } else {
                $stmt = $db->prepare("SELECT * FROM clients ORDER BY created_at DESC");
                $stmt->execute();
                $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($clients);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO clients (name, email, password, company_name, phone, subscription_plan) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['name'],
                $data['email'],
                $hashed_password,
                $data['company_name'],
                $data['phone'],
                $data['subscription_plan'] ?? 'basic'
            ]);
            echo json_encode(['success' => $result, 'id' => $db->lastInsertId()]);
            break;
    }
}

function handleStores($db, $method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $db->prepare("SELECT * FROM stores WHERE id = ?");
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
            $stmt = $db->prepare("INSERT INTO stores (client_id, store_name, store_slug, domain, template_id, primary_color, accent_color, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['client_id'],
                $data['store_name'],
                $data['store_slug'],
                $data['domain'],
                $data['template_id'] ?? 1,
                $data['primary_color'] ?? '#064E3B',
                $data['accent_color'] ?? '#BEF264',
                $data['description']
            ]);
            echo json_encode(['success' => $result, 'id' => $db->lastInsertId()]);
            break;
    }
}
?>