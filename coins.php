<?php
// coins.php - Handle coin operations (GET to load, POST to save)
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
    
    // Check if user_coins table exists, if not create it
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_coins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        coins INT DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY user_id (user_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    // GET request - Load coins
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->prepare("SELECT coins FROM user_coins WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $coins = $result ? intval($result['coins']) : 0;
        
        echo json_encode(['success' => true, 'coins' => $coins]);
    }
    // POST request - Save coins
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get JSON input
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate input
        if (!$data || !isset($data['coins'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            exit;
        }
        
        $coins = intval($data['coins']);
        
        // Insert or update coins - SET to exact value, NOT add
        $stmt = $pdo->prepare("
            INSERT INTO user_coins (user_id, coins, updated_at) 
            VALUES (?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE 
            coins = VALUES(coins),
            updated_at = NOW()
        ");
        
        $stmt->execute([$userId, $coins]);
        
        // Return the saved coins value
        echo json_encode(['success' => true, 'coins' => $coins]);
    }
    else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    error_log("Error in coins.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
?>