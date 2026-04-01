<?php
require_once 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $pdo = getDB();

    // Get user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        // Create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];

        // REMOVED: The progress column check since it doesn't exist in PostgreSQL
        // Progress will be handled by the game progress table instead

        // Redirect to home
        header("Location: index.php");
        exit;

    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
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