<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $db->prepare("SELECT * FROM clients ORDER BY created_at DESC");
        $stmt->execute();
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($clients);
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data['name'] || !$data['email'] || !$data['password']) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        try {
            $stmt = $db->prepare("INSERT INTO clients (name, email, password, company_name, phone, subscription_plan) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $data['name'],
                $data['email'],
                $hashed_password,
                $data['company_name'] ?? null,
                $data['phone'] ?? null,
                $data['subscription_plan'] ?? 'basic'
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to insert client']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>