<?php
require_once 'config.php';

echo "<h1>Test Save Score - Coin Awards</h1>";

if (!isLoggedIn()) {
    echo "<p>Please <a href='login.php'>login</a> first</p>";
    exit;
}

$user = getCurrentUser();
echo "<p>Current User: <strong>" . htmlspecialchars($user['username']) . "</strong></p>";
echo "<p>💰 Current Coins: <strong style='color:blue; font-size:20px;'>" . ($user['coins'] ?? 0) . "</strong></p>";

if (isset($_POST['test_save'])) {
    $correct = intval($_POST['correct']);
    $stars = intval($_POST['stars']);
    $isBoss = isset($_POST['is_boss']);
    
    // Calculate expected coins
    $expectedCoins = $correct * 10;
    if ($stars >= 3) $expectedCoins += 150;
    elseif ($stars >= 2) $expectedCoins += 100;
    elseif ($stars >= 1) $expectedCoins += 50;
    if ($isBoss) $expectedCoins += 100;
    
    $testData = [
        'score' => $correct * 100,
        'skill' => 'beginner',
        'mode' => 'add',
        'level' => 1,
        'stars' => $stars,
        'correct_count' => $correct,
        'total_questions' => 13,
        'total_time' => 30,
        'is_boss' => $isBoss
    ];
    
    echo "<h3>Sending Test Data:</h3>";
    echo "<pre>";
    print_r($testData);
    echo "</pre>";
    
    // Call save-score.php
    $ch = curl_init('https://math-quest-9o52.onrender.com/save-score.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_COOKIEFILE, '');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<h3>Response:</h3>";
    echo "<p>HTTP Code: $httpCode</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $result = json_decode($response, true);
    if ($result && isset($result['success'])) {
        echo "<p style='color:green'>✓ Score saved successfully!</p>";
        echo "<p>💰 Coins Earned: <strong style='color:green; font-size:18px;'>" . ($result['coins_earned'] ?? 0) . "</strong></p>";
        echo "<p>💰 Expected Coins: <strong>" . $expectedCoins . "</strong></p>";
        
        // Refresh user data
        $user = getCurrentUser();
        echo "<p>💰 New Total Coins: <strong style='color:green; font-size:20px;'>" . ($user['coins'] ?? 0) . "</strong></p>";
    } else {
        echo "<p style='color:red'>✗ Failed to save score</p>";
    }
}
?>

<form method="POST">
    <h3>Simulate Level Completion:</h3>
    <p>Correct Answers: <input type="number" name="correct" value="10" min="0" max="23"></p>
    <p>Stars Earned: 
        <select name="stars">
            <option value="3">3 Stars (Perfect)</option>
            <option value="2">2 Stars</option>
            <option value="1">1 Star</option>
            <option value="0">0 Stars</option>
        </select>
    </p>
    <p><label><input type="checkbox" name="is_boss"> Boss Level</label></p>
    <button type="submit" name="test_save">Test Save Score & Award Coins</button>
</form>

<p><a href="index.php">← Back to Game</a></p>