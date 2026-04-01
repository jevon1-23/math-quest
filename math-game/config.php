<?php
// config.php - Database and configuration

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'math_quest');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site URL (auto-detect for better portability)
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/math-game');

// Site name
define('SITE_NAME', 'Math Quest');

// Game configuration
define('COINS_PER_CORRECT_ANSWER', 10);
define('COINS_PER_STAR', 50);
define('FREE_SPIN_COOLDOWN_HOURS', 24);

// Database connection with error handling
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            // Log error instead of dying
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }
    
    return $pdo;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Require login with optional redirect URL
function requireLogin($redirectUrl = 'login.php') {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . $redirectUrl);
        exit;
    }
}

// Require admin access
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('HTTP/1.0 403 Forbidden');
        die('Access denied. Admin only.');
    }
}

// Get current user data with caching
function getCurrentUser() {
    static $user = null;
    
    if (!isLoggedIn()) return null;
    
    if ($user === null) {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            // Update session data
            if ($user) {
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['user_coins'] = $user['coins'] ?? 0;
                $_SESSION['user_role'] = $user['role'] ?? 'user';
            }
        } catch (PDOException $e) {
            error_log("Failed to get current user: " . $e->getMessage());
            return null;
        }
    }
    
    return $user;
}

// Get user statistics
function getUserStats($user_id) {
    try {
        $pdo = getDB();
        
        // Get levels completed and stars
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_levels,
                SUM(stars) as total_stars,
                SUM(CASE WHEN stars = 3 THEN 1 ELSE 0 END) as perfect_levels,
                MAX(score) as highest_score,
                SUM(score) as total_score
            FROM user_progress 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        
        return [
            'total_levels' => $result['total_levels'] ?? 0,
            'total_stars' => $result['total_stars'] ?? 0,
            'perfect_levels' => $result['perfect_levels'] ?? 0,
            'highest_score' => $result['highest_score'] ?? 0,
            'total_score' => $result['total_score'] ?? 0
        ];
    } catch (PDOException $e) {
        error_log("Failed to get user stats: " . $e->getMessage());
        return [
            'total_levels' => 0,
            'total_stars' => 0,
            'perfect_levels' => 0,
            'highest_score' => 0,
            'total_score' => 0
        ];
    }
}

// Update user coins with validation
function updateUserCoins($user_id, $amount) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
        $success = $stmt->execute([$amount, $user_id]);
        
        if ($success && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
            $_SESSION['user_coins'] = ($_SESSION['user_coins'] ?? 0) + $amount;
        }
        
        return $success;
    } catch (PDOException $e) {
        error_log("Failed to update user coins: " . $e->getMessage());
        return false;
    }
}

// Get user coins
function getUserCoins($user_id = null) {
    if ($user_id === null && isLoggedIn()) {
        return $_SESSION['user_coins'] ?? 0;
    }
    
    if ($user_id) {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT coins FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            return $result ? $result['coins'] : 0;
        } catch (PDOException $e) {
            error_log("Failed to get user coins: " . $e->getMessage());
            return 0;
        }
    }
    
    return 0;
}

// Save game progress
function saveGameProgress($user_id, $skill, $level, $score, $stars, $coins_earned = 0) {
    try {
        $pdo = getDB();
        
        // Check if progress exists
        $stmt = $pdo->prepare("SELECT id FROM user_progress WHERE user_id = ? AND skill = ? AND level = ?");
        $stmt->execute([$user_id, $skill, $level]);
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
            return $stmt->execute([$score, $stars, $user_id, $skill, $level]);
        } else {
            // Insert new progress
            $stmt = $pdo->prepare("
                INSERT INTO user_progress (user_id, skill, level, score, stars, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$user_id, $skill, $level, $score, $stars]);
        }
    } catch (PDOException $e) {
        error_log("Failed to save game progress: " . $e->getMessage());
        return false;
    }
}

// Get user progress for a skill
function getUserProgress($user_id, $skill) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM user_progress WHERE user_id = ? AND skill = ? ORDER BY level ASC");
        $stmt->execute([$user_id, $skill]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Failed to get user progress: " . $e->getMessage());
        return [];
    }
}

// Get unlocked levels
function getUnlockedLevels($user_id, $skill) {
    $progress = getUserProgress($user_id, $skill);
    $unlocked = [1]; // Level 1 is always unlocked
    
    foreach ($progress as $p) {
        if ($p['level'] + 1 <= 20) { // Assuming 20 levels per skill
            $unlocked[] = $p['level'] + 1;
        }
    }
    
    return array_unique($unlocked);
}

// Check if level is unlocked
function isLevelUnlocked($user_id, $skill, $level) {
    if ($level == 1) return true;
    
    $unlocked = getUnlockedLevels($user_id, $skill);
    return in_array($level, $unlocked);
}

// Add achievement
function addAchievement($user_id, $achievement_id) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_achievements (user_id, achievement_id, earned_at) VALUES (?, ?, NOW())");
        return $stmt->execute([$user_id, $achievement_id]);
    } catch (PDOException $e) {
        error_log("Failed to add achievement: " . $e->getMessage());
        return false;
    }
}

// Check if user has achievement
function hasAchievement($user_id, $achievement_id) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT 1 FROM user_achievements WHERE user_id = ? AND achievement_id = ?");
        $stmt->execute([$user_id, $achievement_id]);
        return (bool) $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Failed to check achievement: " . $e->getMessage());
        return false;
    }
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Log user activity (for analytics)
function logActivity($user_id, $action, $details = null) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT INTO user_activity (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return $stmt->execute([$user_id, $action, $details, $ip]);
    } catch (PDOException $e) {
        error_log("Failed to log activity: " . $e->getMessage());
        return false;
    }
}

// Format time (for leaderboards)
function formatTime($seconds) {
    if ($seconds < 60) {
        return $seconds . 's';
    } elseif ($seconds < 3600) {
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        return $minutes . 'm ' . $secs . 's';
    } else {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return $hours . 'h ' . $minutes . 'm';
    }
}

// Get leaderboard for a specific skill and level
function getLeaderboard($skill, $level, $limit = 10) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("
            SELECT 
                u.username,
                up.score,
                up.stars,
                up.created_at
            FROM user_progress up
            JOIN users u ON u.id = up.user_id
            WHERE up.skill = ? AND up.level = ?
            ORDER BY up.score DESC, up.stars DESC, up.created_at ASC
            LIMIT ?
        ");
        $stmt->execute([$skill, $level, $limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Failed to get leaderboard: " . $e->getMessage());
        return [];
    }
}
?>