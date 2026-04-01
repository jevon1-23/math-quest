<?php
// save-score.php
require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Player';

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

try {
    // Start transaction
    $conn->begin_transaction();
    
    // 1. Save to leaderboard
    $stmt = $conn->prepare("INSERT INTO leaderboard (username, score, skill, mode, created_at) 
                           VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("siss", $username, $score, $skill, $mode);
    $stmt->execute();
    
    // 2. Save detailed game score
    $stmt = $conn->prepare("INSERT INTO game_scores 
                           (user_id, skill, game_mode, level, score, stars, 
                            correct_count, total_questions, fastest_time, total_time, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                           ON DUPLICATE KEY UPDATE 
                           score = GREATEST(score, VALUES(score)),
                           stars = GREATEST(stars, VALUES(stars)),
                           correct_count = VALUES(correct_count),
                           total_questions = VALUES(total_questions),
                           fastest_time = IF(fastest_time IS NULL OR VALUES(fastest_time) < fastest_time, 
                                             VALUES(fastest_time), fastest_time),
                           total_time = VALUES(total_time)");
    $stmt->bind_param("issiiiiidi", $userId, $skill, $mode, $level, $score, $stars, 
                      $correctCount, $totalQuestions, $fastestTime, $totalTime);
    $stmt->execute();
    
    // 3. Update user progress (unlocked levels)
    // Check if user has enough stars to unlock next level (need 2 stars)
    $newUnlockedLevel = $level;
    if ($stars >= 2) {
        $newUnlockedLevel = $level + 1;
    }
    
    $stmt = $conn->prepare("INSERT INTO user_progress 
                           (user_id, skill, game_mode, unlocked_level, total_score, updated_at) 
                           VALUES (?, ?, ?, ?, ?, NOW())
                           ON DUPLICATE KEY UPDATE 
                           unlocked_level = GREATEST(unlocked_level, VALUES(unlocked_level)),
                           total_score = total_score + VALUES(total_score)");
    $stmt->bind_param("issii", $userId, $skill, $mode, $newUnlockedLevel, $score);
    $stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Score saved successfully']);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    error_log("Error saving score: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>