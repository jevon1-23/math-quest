<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = getDB();
        
        if (isset($_POST['update_profile'])) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $_SESSION['user_id']]);
            $message = "✅ Profile updated successfully!";
            
            // Update session
            $_SESSION['user_name'] = $username;
            $user = getCurrentUser(); // Refresh user data
        }
        
        if (isset($_POST['change_password'])) {
            $current = $_POST['current_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user_data = $stmt->fetch();
            
            if (password_verify($current, $user_data['password'])) {
                if ($new === $confirm) {
                    $new_hash = password_hash($new, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$new_hash, $_SESSION['user_id']]);
                    $message = "✅ Password changed successfully!";
                } else {
                    $message = "❌ New passwords don't match!";
                }
            } else {
                $message = "❌ Current password is incorrect!";
            }
        }
        
    } catch (PDOException $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings - Math Quest</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .settings-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #45a049;
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
        .stat {
            margin: 10px 0;
            padding: 8px;
            background: #f5f5f5;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="settings-container">
        <h1>⚙️ Settings</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '❌') !== false ? 'error-message' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>👤 Profile Information</h2>
            <form method="POST">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                
                <button type="submit" name="update_profile" class="btn">Update Profile</button>
            </form>
        </div>
        
        <div class="card">
            <h2>🔒 Change Password</h2>
            <form method="POST">
                <label>Current Password:</label>
                <input type="password" name="current_password" required>
                
                <label>New Password:</label>
                <input type="password" name="new_password" required>
                
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_password" required>
                
                <button type="submit" name="change_password" class="btn">Change Password</button>
            </form>
        </div>
        
        <div class="card">
            <h2>📊 Account Statistics</h2>
            <div class="stat"><strong>Username:</strong> <?php echo htmlspecialchars($user['username'] ?? ''); ?></div>
            <div class="stat"><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
            <div class="stat"><strong>💰 Coins:</strong> <?php echo $user['coins'] ?? 0; ?></div>
            <div class="stat"><strong>👑 Role:</strong> <?php echo $user['role'] ?? 'User'; ?></div>
            <div class="stat"><strong>📅 Member since:</strong> <?php echo $user['created_at'] ?? 'Unknown'; ?></div>
        </div>
        
        <div class="card">
            <h2>🎮 Game Links</h2>
            <p><a href="index.php">🏠 Home</a></p>
            <p><a href="profile.php">👤 Profile</a></p>
            <p><a href="shop.php">🛒 Shop</a></p>
            <p><a href="logout.php">🚪 Logout</a></p>
        </div>
    </div>
</body>
</html>