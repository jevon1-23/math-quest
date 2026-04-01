<?php
require_once 'config.php';

// Show errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

echo "<h1>Coins Debug</h1>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Not logged in. <a href='login.php'>Login here</a></p>";
    echo json_encode(['error' => 'Not logged in', 'coins' => 0]);
    exit;
}

$userId = $_SESSION['user_id'];
echo "<p>User ID: $userId</p>";

try {
    $pdo = getDB();
    echo "<p>✓ Database connected</p>";
    
    // Check if users table exists and get coins directly
    $stmt = $pdo->prepare("SELECT coins FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<p>✓ Found user. Coins: " . $result['coins'] . "</p>";
        echo json_encode(['success' => true, 'coins' => intval($result['coins'])]);
    } else {
        echo "<p>✗ User not found in database!</p>";
        echo json_encode(['error' => 'User not found', 'coins' => 0]);
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Database error: " . $e->getMessage() . "</p>";
    echo json_encode(['error' => $e->getMessage()]);
}
?>