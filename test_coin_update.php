<?php
require_once 'config.php';

echo "<h1>💰 Coin Update Test</h1>";

if (!isLoggedIn()) {
    echo "<p style='color:red'>Please <a href='login.php'>login</a> first</p>";
    exit;
}

$user = getCurrentUser();
echo "<h2>Current User: " . htmlspecialchars($user['username']) . "</h2>";
echo "<p>💰 Current Coins in Session: <strong style='font-size: 24px; color: green;'>" . ($_SESSION['user_coins'] ?? 0) . "</strong></p>";
echo "<p>💰 Current Coins in Database: <strong style='font-size: 24px; color: blue;'>" . ($user['coins'] ?? 0) . "</strong></p>";

// Test adding coins
if (isset($_POST['add_coins'])) {
    $amount = intval($_POST['amount']);
    echo "<h3>Attempting to add $amount coins...</h3>";
    
    $result = updateUserCoins($_SESSION['user_id'], $amount);
    
    if ($result) {
        echo "<p style='color:green'>✓ Successfully added $amount coins!</p>";
        // Refresh user data
        $user = getCurrentUser();
        echo "<p>💰 New Coin Total: <strong style='font-size: 24px; color: green;'>" . ($user['coins'] ?? 0) . "</strong></p>";
    } else {
        echo "<p style='color:red'>✗ Failed to add coins</p>";
    }
}

// Show all users
echo "<h2>📊 All Users in Database:</h2>";
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT id, username, email, coins, created_at FROM users ORDER BY id");
    $users = $stmt->fetchAll();
    
    if (count($users) == 0) {
        echo "<p>No users found. Please <a href='register.php'>register</a> an account.</p>";
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
        echo "<tr style='background: #333; color: white;'>";
        echo "<th>ID</th><th>Username</th><th>Email</th><th>💰 Coins</th><th>Created At</th>";
        echo "</tr>";
        foreach ($users as $u) {
            echo "<tr>";
            echo "<td>{$u['id']}</td>";
            echo "<td><strong>{$u['username']}</strong></td>";
            echo "<td>{$u['email']}</td>";
            echo "<td style='font-weight: bold; color: green; font-size: 18px;'>{$u['coins']}</td>";
            echo "<td>{$u['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Test direct SQL update
if (isset($_POST['direct_update'])) {
    $amount = intval($_POST['direct_amount']);
    echo "<h3>Testing Direct SQL Update:</h3>";
    
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
        $stmt->execute([$amount, $_SESSION['user_id']]);
        echo "<p style='color:green'>✓ Direct SQL update successful!</p>";
        
        // Refresh
        $user = getCurrentUser();
        echo "<p>💰 Updated Coins: <strong>" . ($user['coins'] ?? 0) . "</strong></p>";
        
    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<h3>Test Forms:</h3>

<form method="POST" style="margin: 10px 0; padding: 10px; border: 1px solid #ccc;">
    <h4>Add Coins (using updateUserCoins function):</h4>
    <input type="number" name="amount" value="10" required style="padding: 5px;">
    <button type="submit" name="add_coins" style="padding: 5px 10px;">Add Coins</button>
</form>

<form method="POST" style="margin: 10px 0; padding: 10px; border: 1px solid #ccc;">
    <h4>Add Coins (Direct SQL):</h4>
    <input type="number" name="direct_amount" value="10" required style="padding: 5px;">
    <button type="submit" name="direct_update" style="padding: 5px 10px;">Add Coins Direct</button>
</form>

<p><a href="index.php">← Back to Game</a></p>