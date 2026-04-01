<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();
$message = "";

// Handle settings update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = getDB();
        
        if (isset($_POST['update_profile'])) {
            $username = sanitize($_POST['username']);
            $email = sanitize($_POST['email']);
            
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $_SESSION['user_id']]);
            $message = "Profile updated successfully!";
            
            // Update session
            $_SESSION['user_name'] = $username;
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
                    $message = "Password changed successfully!";
                } else {
                    $message = "New passwords don't match!";
                }
            } else {
                $message = "Current password is incorrect!";
            }
        }
        
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Get updated user data
$user = getCurrentUser();
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
        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="settings-container">
        <h1>Settings</h1>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Profile Information</h2>
            <form method="POST">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                
                <button type="submit" name="update_profile" class="btn">Update Profile</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Change Password</h2>
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
            <h2>Account Statistics</h2>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username'] ?? ''); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            <p><strong>Coins:</strong> <?php echo $user['coins'] ?? 0; ?></p>
            <p><strong>Role:</strong> <?php echo $user['role'] ?? 'User'; ?></p>
            <p><strong>Member since:</strong> <?php echo $user['created_at'] ?? 'Unknown'; ?></p>
        </div>
    </div>
</body>
</html>