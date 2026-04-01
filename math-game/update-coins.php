<?php
require_once 'config.php';
requireLogin();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['coins'])) {
    echo json_encode(["status" => "error"]);
    exit;
}

$coins = (int)$data['coins'];

$stmt = $pdo->prepare("UPDATE users SET coins = ? WHERE id = ?");
$stmt->execute([$coins, $_SESSION['user_id']]);

echo json_encode(["status" => "success"]);