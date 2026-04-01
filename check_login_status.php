<?php
require_once 'config.php';

echo "<h1>🔐 Login Status Check</h1>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['user_id'])) {
    echo "<p style='color:green'>✅ User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<p>Username: " . ($_SESSION['user_name'] ?? 'Not set') . "</p>";
    echo "<p>Session Coins: " . ($_SESSION['user_coins'] ?? 0) . "</p>";
    
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, username, coins FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<p style='color:green'>✅ User found in database: " . htmlspecialchars($user['username']) . "</p>";
            echo "<p>💰 Database Coins: <strong style='font-size:24px;color:green;'>" . $user['coins'] . "</strong></p>";
            
            if ($_SESSION['user_coins'] != $user['coins']) {
                echo "<p style='color:orange'>⚠️ Session coins ({$_SESSION['user_coins']}) don't match database coins ({$user['coins']})</p>";
                // Fix the session
                $_SESSION['user_coins'] = $user['coins'];
                echo "<p>✅ Session fixed!</p>";
            }
        } else {
            echo "<p style='color:red'>❌ User not found in database!</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>❌ NOT LOGGED IN</p>";
    echo "<p><a href='login.php'>Click here to login</a></p>";
}

echo "<h2>Test coins.php:</h2>";
$ch = curl_init('https://math-quest-9o52.onrender.com/coins.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '');
$response = curl_exec($ch);
echo "<pre>" . htmlspecialchars($response) . "</pre>";
?>