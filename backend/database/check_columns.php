<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Config\Database;

$database = new Database();
$pdo = $database->getConnection();

echo "Current columns in stores table:\n";
echo str_repeat("=", 60) . "\n";

$result = $pdo->query("DESCRIBE stores");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("%-30s %s\n", $row['Field'], $row['Type']);
}
