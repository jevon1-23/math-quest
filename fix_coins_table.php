<?php
require_once 'config.php';

echo "<h1>Fixing Coins System</h1>";

try {
    $pdo = getDB();
    
    // Check if coins column exists in users table
    $stmt = $pdo->query("
        SELECT column_name 
        FROM information_schema.columns 
        WHERE table_name='users' AND column_name='coins'
    ");
    
    if ($stmt->rowCount() == 0) {
        echo "<p>Adding coins column to users table...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN coins INTEGER DEFAULT 0");
        echo "<p style='color:green'>✓ Coins column added!</p>";
    } else {
        echo "<p style='color:blue'>✓ Coins column already exists</p>";
    }
    
    // Set default coins for all users
    $pdo->exec("UPDATE users SET coins = COALESCE(coins, 0)");
    echo "<p>✓ All users have default coins set</p>";
    
    // Show all users
    $stmt = $pdo->query("SELECT id, username, coins FROM users");
    echo "<h2>Current Users:</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th><th>Coins</th></tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['username']}</td>";
        echo "<td style='font-weight:bold;color:green'>{$row['coins']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><a href='coins.php'>Test coins.php</a></p>";
    echo "<p><a href='index.php'>Back to Game</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>