<?php
// save-score.php - PostgreSQL version with coin awards
require_once 'config.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['user_name'] ?? 'Player';

// Get JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

// Extract data
$score = intval($data['score'] ?? 0);
$skill = $data['skill'] ?? 'unknown';
$mode = $data['mode'] ?? 'unknown';
$level = intval($data['level'] ?? 1);
$stars = intval($data['stars'] ?? 0);
$correctCount = intval($data['correct_count'] ?? 0);
$totalQuestions = intval($data['total_questions'] ?? 0);
$fastestTime = isset($data['fastest_time']) ? floatval($data['fastest_time']) : null;
$totalTime = floatval($data['total_time'] ?? 0);
$isBoss = $data['is_boss'] ?? false;

try {
    $pdo = getDB();
    
    // Calculate coins earned
    $coinsEarned = 0;
    
    // Base coins: 10 coins per correct answer
    $coinsEarned += $correctCount * 10;
    
    // Star bonuses
    if ($stars >= 3) {
        $coinsEarned += 150;  // 3 stars bonus
    } elseif ($stars >= 2) {
        $coinsEarned += 100;  // 2 stars bonus
    } elseif ($stars >= 1) {
        $coinsEarned += 50;   // 1 star bonus
    }
    
    // Boss level bonus
    if ($isBoss) {
        $coinsEarned += 100;
    }
    
    // Speed bonus (if average time per question < 5 seconds)
    if ($correctCount > 0 && $totalTime > 0) {
        $avgTime = $totalTime / $correctCount;
        if ($avgTime < 5) {
            $coinsEarned += 50;
        }
    }
    
    // Update user coins
    if ($coinsEarned > 0) {
        $stmt = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
        $stmt->execute([$coinsEarned, $userId]);
        
        // Update session
        $_SESSION['user_coins'] = ($_SESSION['user_coins'] ?? 0) + $coinsEarned;
    }
    
    // 1. Save to leaderboard (using the existing leaderboard table)
    $stmt = $pdo->prepare("INSERT INTO leaderboard (username, score, skill, mode, created_at) 
                           VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$username, $score, $skill, $mode]);
    
    // 2. Save detailed game score (using user_progress table)
    // Check if progress exists
    $stmt = $pdo->prepare("SELECT id FROM user_progress WHERE user_id = ? AND skill = ? AND level = ?");
    $stmt->execute([$userId, $skill, $level]);
    $exists = $stmt->fetch();
    
    if ($exists) {
        // Update existing progress if new score is better
        $stmt = $pdo->prepare("
            UPDATE user_progress 
            SET score = GREATEST(score, ?), 
                stars = GREATEST(stars, ?), 
                updated_at = NOW() 
            WHERE user_id = ? AND skill = ? AND level = ?
        ");
        $stmt->execute([$score, $stars, $userId, $skill, $level]);
    } else {
        // Insert new progress
        $stmt = $pdo->prepare("
            INSERT INTO user_progress (user_id, skill, level, score, stars, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $skill, $level, $score, $stars]);
    }
    
    // Return success with coins earned
    echo json_encode([
        'success' => true,
        'message' => 'Score saved successfully',
        'coins_earned' => $coinsEarned,
        'total_coins' => getUserCoins($userId),
        'stars' => $stars
    ]);
    
} catch (PDOException $e) {
    error_log("Error saving score: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>