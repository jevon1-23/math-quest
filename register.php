<?php
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $pdo = getDB();
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already taken';
        }
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered';
        }
        
        if (empty($error)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, coins) VALUES (?, ?, ?, 'user', 100)");
            
            if ($stmt->execute([$username, $email, $hashedPassword])) {
                $success = 'Registration successful! You can now login.';
                $_POST = [];
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest - Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">📝</div>
                <h2>Create Account</h2>
                <p class="login-subtitle">Join Math Quest and start your adventure</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <span>⚠️</span>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <span>✅</span>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>👤 Username</label>
                    <input type="text" name="username" placeholder="Choose a username (min 3 characters)" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>📧 Email</label>
                    <input type="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>🔒 Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" placeholder="Create a password (min 6 characters)" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">👁️</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>🔒 Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">👁️</button>
                    </div>
                </div>
                <button type="submit" class="login-btn">Create Account</button>
            </form>
            
            <div class="divider">
                <span>or</span>
            </div>
            
            <div class="register-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const password = document.getElementById(fieldId);
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;
        }
    </script>
</body>
</html>