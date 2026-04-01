<?php
require_once 'config.php';

echo "<h1>💰 Force Add Coins</h1>";

if (!isLoggedIn()) {
    echo "<p>Please <a href='login.php'>login</a> first</p>";
    exit;
}

$user = getCurrentUser();
echo "<p>Current User: <strong>" . htmlspecialchars($user['username']) . "</strong></p>";
echo "<p>💰 Current Coins: <strong style='color:blue; font-size:24px;'>" . ($user['coins'] ?? 0) . "</strong></p>";

if (isset($_POST['add_coins'])) {
    $amount = intval($_POST['amount']);
    $result = updateUserCoins($_SESSION['user_id'], $amount);
    
    if ($result) {
        echo "<p style='color:green'>✅ Added $amount coins successfully!</p>";
        $user = getCurrentUser();
        echo "<p>💰 New Coin Total: <strong style='color:green; font-size:28px;'>" . ($user['coins'] ?? 0) . "</strong></p>";
    } else {
        echo "<p style='color:red'>❌ Failed to add coins</p>";
    }
}

// Show all users
echo "<h2>All Users:</h2>";
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT id, username, coins FROM users ORDER BY coins DESC");
    $users = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr style='background:#333;color:white;'><th>ID</th><th>Username</th><th>💰 Coins</th></tr>";
    foreach ($users as $u) {
        echo "<tr>";
        echo "<td>{$u['id']}</td>";
        echo "<td>{$u['username']}</td>";
        echo "<td style='color:green;font-weight:bold;font-size:18px;'>{$u['coins']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>

<form method="POST" style="margin-top:20px;">
    <input type="number" name="amount" value="100" required style="padding:8px;">
    <button type="submit" name="add_coins" style="padding:8px 16px;">Add Coins</button>
</form>

<p><a href="index.php">← Back to Game</a></p>