<?php
// coins.php - Simple coin operations with error handling
require_once 'config.php';

// Clear any output buffering to prevent HTML from leaking
ob_clean();

// Set JSON header
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in', 'coins' => 0]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $pdo = getDB();
    
    // GET request - Load coins
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->prepare("SELECT COALESCE(coins, 0) as coins FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $coins = $result ? intval($result['coins']) : 0;
        
        echo json_encode(['success' => true, 'coins' => $coins]);
    }
    // POST not supported — coin changes go through save-score.php or update-coins.php
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
    else {
        echo json_encode(['error' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Error in coins.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error', 'coins' => 0]);
}
?>