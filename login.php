<?php
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Simple rate limiting: max 10 attempts per 15 minutes per IP
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $attemptKey = 'login_attempts_' . md5($ip);
    $timeKey    = 'login_time_' . md5($ip);

    $attempts  = intval($_SESSION[$attemptKey] ?? 0);
    $firstTime = intval($_SESSION[$timeKey]    ?? 0);

    if ($firstTime && (time() - $firstTime) > 900) {
        // Reset window
        $attempts  = 0;
        $firstTime = 0;
    }

    if ($attempts >= 10) {
        $error = "Too many login attempts. Please wait 15 minutes.";
    } else {
        $_SESSION[$attemptKey] = $attempts + 1;
        if (!$firstTime) $_SESSION[$timeKey] = time();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID on login to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];

            // Clear rate limit on success
            unset($_SESSION[$attemptKey], $_SESSION[$timeKey]);

            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: " . $redirect);
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Math Quest</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="card">
    <h2>🔐 Login to Math Quest</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit" class="btn">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>
    <p><a href="index.php">Back to Home</a></p>
</div>

</body>
</html>