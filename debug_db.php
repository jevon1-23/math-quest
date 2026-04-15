<?php
// debug_db.php - Check database status (PostgreSQL version)
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

echo "<h1>Database Debug</h1>";
echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
echo "<p><strong>User:</strong> " . DB_USER . "</p>";
echo "<p><strong>Port:</strong> " . DB_PORT . "</p>";

try {
    $pdo = getDB();
    echo "<p style='color:green'>✅ Database connected!</p>";

    // PostgreSQL: list tables using information_schema
    $tables = $pdo->query("
        SELECT table_name FROM information_schema.tables
        WHERE table_schema = 'public' ORDER BY table_name
    ")->fetchAll();

    echo "<h3>Tables in database:</h3>";
    if (count($tables) == 0) {
        echo "<p style='color:red'>❌ No tables found!</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table['table_name'] . "</li>";
        }
        echo "</ul>";
    }

    // Count users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch()['count'];
    echo "<p>📊 Total users: <strong>" . $count . "</strong></p>";

    if ($count > 0) {
        $stmt = $pdo->query("SELECT id, username, email, coins FROM users LIMIT 5");
        $users = $stmt->fetchAll();
        echo "<h3>Existing users:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Coins</th></tr>";
        foreach ($users as $user) {
            echo "<tr><td>" . $user['id'] . "</td><td>" . htmlspecialchars($user['username']) . "</td><td>" . htmlspecialchars($user['email']) . "</td><td>" . $user['coins'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:orange'>⚠️ No users yet. <a href='register.php'>Register an account</a></p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<hr><h3>Troubleshooting:</h3><ul>";
    echo "<li>Make sure your Supabase project is <strong>active</strong> (not paused)</li>";
    echo "<li>Verify DB_USER in config.php includes the project ref: <code>postgres.YOUR_PROJECT_REF</code></li>";
    echo "<li>Double-check the password is correct</li>";
    echo "</ul>";
}
?>