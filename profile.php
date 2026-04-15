<?php
// profile.php - Working profile page
require_once 'config.php';
requireLogin();

$user = getCurrentUser();
$message = "";
$error = "";

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = getDB();
        
        if (isset($_POST['update_profile'])) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $_SESSION['user_id']]);
            $message = "✅ Profile updated successfully!";
            
            $_SESSION['user_name'] = $username;
            $user = getCurrentUser();
        }
        
        if (isset($_POST['change_password'])) {
            $current = $_POST['current_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user_data = $stmt->fetch();
            
            if ($user_data && password_verify($current, $user_data['password'])) {
                if ($new === $confirm) {
                    $new_hash = password_hash($new, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$new_hash, $_SESSION['user_id']]);
                    $message = "✅ Password changed successfully!";
                } else {
                    $error = "❌ New passwords don't match!";
                }
            } else {
                $error = "❌ Current password is incorrect!";
            }
        }
    } catch (PDOException $e) {
        $error = "❌ Database error: " . $e->getMessage();
    }
}

// Get simple stats from user_progress
$totalLevels = 0;
$perfectLevels = 0;
try {
    $pdo = getDB();
    // Count total levels played
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user_progress WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $totalLevels = $stmt->fetch()['count'] ?? 0;
    
    // Count perfect levels (3 stars)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user_progress WHERE user_id = ? AND stars = 3");
    $stmt->execute([$_SESSION['user_id']]);
    $perfectLevels = $stmt->fetch()['count'] ?? 0;
} catch (Exception $e) {
    // Table might be empty or not exist yet
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest - My Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .card h2, .card h3 {
            margin-top: 0;
            color: #333;
        }
        .message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .btn:hover {
            background: #45a049;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-icon {
            font-size: 2rem;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #4CAF50;
        }
        .stat-label {
            font-size: 0.8rem;
            color: #666;
        }
        hr {
            margin: 20px 0;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: white;
        }
        .back-link a {
            color: white;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h1 style="color: white; text-align: center;">👤 My Profile</h1>
    
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="message error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Account Settings -->
    <div class="card">
        <h3>👤 Account Settings</h3>
        <form method="POST" action="">
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
            
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            
            <button type="submit" name="update_profile" class="btn">Update Profile</button>
        </form>
    </div>
    
    <!-- Change Password -->
    <div class="card">
        <h3>🔒 Change Password</h3>
        <form method="POST" action="">
            <label>Current Password:</label>
            <input type="password" name="current_password" required>
            
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required>
            
            <button type="submit" name="change_password" class="btn">Change Password</button>
        </form>
    </div>
    
    <!-- Stats -->
    <div class="card">
        <h3>📊 Game Statistics</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🪙</div>
                <div class="stat-value"><?php echo number_format($user['coins'] ?? 0); ?></div>
                <div class="stat-label">Total Coins</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value"><?php echo $totalLevels; ?></div>
                <div class="stat-label">Levels Played</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏆</div>
                <div class="stat-value"><?php echo $perfectLevels; ?></div>
                <div class="stat-label">Perfect Levels</div>
            </div>
        </div>
    </div>
    
    <!-- Account Info -->
    <div class="card">
        <h3>ℹ️ Account Info</h3>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username'] ?? ''); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
        <p><strong>Member since:</strong> <?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></p>
        <p><strong>Role:</strong> <?php echo ucfirst($user['role'] ?? 'User'); ?></p>
    </div>
    
    <!-- Quick Links -->
    <div class="card">
        <h3>🎮 Quick Links</h3>
        <p><a href="index.php">🏠 Home</a></p>
        <p><a href="play.php">🎮 Play Game</a></p>
        <p><a href="shop.php">🛒 Shop</a></p>
        <p><a href="settings.php">⚙️ Settings</a></p>
        <p><a href="logout.php">🚪 Logout</a></p>
    </div>
    
    <div class="back-link">
        <a href="index.php">← Back to Home</a>
    </div>
</div>

</body>
</html>