<?php
require_once 'config.php';

// Better session check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php?redirect=settings");
    exit;
}

$user = getCurrentUser();
$message = "";
$error = "";

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
            $user = getCurrentUser();
        }
        
        if (isset($_POST['change_password'])) {
            $current = $_POST['current_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Settings - Math Quest</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .settings-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 15px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .message {
            padding: 12px;
            border-radius: 8px;
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
            padding: 12px;
            margin: 8px 0 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        .btn {
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            font-weight: bold;
        }
        
        .btn:hover {
            background: #45a049;
        }
        
        h1 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        
        h2 {
            margin-top: 0;
            color: #333;
            font-size: 1.3rem;
        }
        
        .stat {
            margin: 12px 0;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .stat strong {
            display: inline-block;
            width: 120px;
            color: #555;
        }
        
        @media (max-width: 480px) {
            .settings-container {
                margin: 10px auto;
                padding: 10px;
            }
            
            .card {
                padding: 15px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            h2 {
                font-size: 1.2rem;
            }
            
            .stat strong {
                width: 100px;
                font-size: 0.9rem;
            }
            
            .stat {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <h1>⚙️ Settings</h1>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>👤 Profile Information</h2>
            <form method="POST" action="">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                
                <button type="submit" name="update_profile" class="btn">Update Profile</button>
            </form>
        </div>
        
        <div class="card">
            <h2>🔒 Change Password</h2>
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
        
        <div class="card">
            <h2>📊 Account Statistics</h2>
            <div class="stat"><strong>Username:</strong> <?php echo htmlspecialchars($user['username'] ?? ''); ?></div>
            <div class="stat"><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
            <div class="stat"><strong>💰 Coins:</strong> <?php echo number_format($user['coins'] ?? 0); ?></div>
            <div class="stat"><strong>👑 Role:</strong> <?php echo ucfirst($user['role'] ?? 'User'); ?></div>
            <div class="stat"><strong>📅 Member since:</strong> <?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></div>
        </div>
        
        <div class="card">
            <h2>🎮 Quick Links</h2>
            <div class="stat"><a href="index.php">🏠 Home</a></div>
            <div class="stat"><a href="profile.php">👤 Profile</a></div>
            <div class="stat"><a href="shop.php">🛒 Shop</a></div>
            <div class="stat"><a href="logout.php">🚪 Logout</a></div>
        </div>
    </div>
</body>
</html>