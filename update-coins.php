<?php
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

// Accept a delta (positive or negative), not an absolute value
if (!isset($data['delta'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing delta"]);
    exit;
}

$delta = intval($data['delta']);

// Clamp to a sane per-request maximum so a tampered request can't grant huge coins
if ($delta > 500 || $delta < -10000) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid delta"]);
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE users SET coins = GREATEST(0, coins + ?) WHERE id = ? RETURNING coins");
    $stmt->execute([$delta, $_SESSION['user_id']]);
    $row = $stmt->fetch();
    $newCoins = $row ? intval($row['coins']) : 0;
    $_SESSION['user_coins'] = $newCoins;
    echo json_encode(["status" => "success", "coins" => $newCoins]);
} catch (PDOException $e) {
    error_log("update-coins.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error"]);
}
?>
