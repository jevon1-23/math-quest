<?php
// logout.php - Clears both session and localStorage
session_start();

// Clear all session data
$_SESSION = array();

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logging out...</title>
    <script>
        // Clear ALL localStorage data
        localStorage.clear();
        
        // Also clear sessionStorage if used
        if (sessionStorage) {
            sessionStorage.clear();
        }
        
        // Redirect to login page
        window.location.href = 'login.php';
    </script>
</head>
<body>
    <p>Logging out...</p>
</body>
</html>