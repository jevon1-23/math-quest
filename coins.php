<?php
// coins.php - Simple coin operations
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in', 'coins' => 0]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $pdo = getDB();
    
    // GET request - Load coins
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->prepare("SELECT coins FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $coins = $result ? intval($result['coins']) : 0;
        
        echo json_encode(['success' => true, 'coins' => $coins]);
    }
    // POST request - Save coins
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
        
        if (!$data || !isset($data['coins'])) {
            echo json_encode(['error' => 'Invalid input']);
            exit;
        }
        
        $coins = intval($data['coins']);
        
        $stmt = $pdo->prepare("UPDATE users SET coins = ? WHERE id = ?");
        $stmt->execute([$coins, $userId]);
        
        $_SESSION['user_coins'] = $coins;
        
        echo json_encode(['success' => true, 'coins' => $coins]);
    }
    else {
        echo json_encode(['error' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Error in coins.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>