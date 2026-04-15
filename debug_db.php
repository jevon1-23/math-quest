<?php
// debug_db.php - Check database status
require_once 'config.php';

echo "<h1>Database Debug</h1>";

try {
    $pdo = getDB();
    echo "<p style='color:green'>✅ Database connected!</p>";
    
    // Check if users table exists
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();
    echo "<h3>Tables in database:</h3>";
    if (count($tables) == 0) {
        echo "<p style='color:red'>❌ No tables found! Database is empty.</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table['name'] . "</li>";
        }
        echo "</ul>";
    }
    
    // Count users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch()['count'];
    echo "<p>📊 Total users: <strong>" . $count . "</strong></p>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT id, username, email, coins FROM users LIMIT 5");
        $users = $stmt->fetchAll();
        echo "<h3>Existing users:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Coins</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['coins'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:orange'>⚠️ No users found. Please register a new account.</p>";
        echo "<a href='register.php'>Go to Register</a>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>