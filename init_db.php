<?php
// init_db.php - Initialize database with test user
require_once 'config.php';

$pdo = getDB();

echo "<h1>Database Initialization</h1>";

// Check if users table is empty
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$count = $stmt->fetch()['count'];

if ($count == 0) {
    echo "<p>No users found. Creating test user...</p>";
    
    // Create a test user
    $username = 'demo';
    $email = 'demo@mathquest.com';
    $password = password_hash('demo123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, coins) VALUES (?, ?, ?, 500)");
    $stmt->execute([$username, $email, $password]);
    
    echo "<p style='color:green'>✅ Test user created!</p>";
    echo "<p>Username: <strong>demo</strong></p>";
    echo "<p>Password: <strong>demo123</strong></p>";
} else {
    echo "<p>✅ Database already has $count user(s).</p>";
}

// List all users
$stmt = $pdo->query("SELECT id, username, email, coins FROM users");
$users = $stmt->fetchAll();

echo "<h3>Current Users:</h3>";
echo "<ul>";
foreach ($users as $user) {
    echo "<li>" . $user['username'] . " (Coins: " . $user['coins'] . ")</li>";
}
echo "</ul>";

echo "<br><a href='login.php'>Go to Login</a>";
?>