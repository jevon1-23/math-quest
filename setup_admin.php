<?php
// setup_admin.php - Run this once to create admin accounts
require_once 'config.php';

$pdo = getDB();

// Hash password once
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);

echo "<!DOCTYPE html>";
echo "<html><head><title>Math Quest - Admin Setup</title>";
echo "<link rel='stylesheet' href='style.css'>";
echo "</head><body style='display:flex; justify-content:center; align-items:center; min-height:100vh;'>";
echo "<div class='card' style='max-width:500px;'>";

echo "<h2>🎮 Math Quest - Admin Setup</h2><hr>";

// ==========================
// ADMIN 1: jevon1234
// ==========================
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute(['jevon1234']);
$exists = $stmt->fetch();

if (!$exists) {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, coins) VALUES (?, ?, 'admin', 999999)");
    if ($stmt->execute(['jevon1234', $adminPassword])) {
        echo "<p style='color:green;'>✅ Admin 'jevon1234' created</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create 'jevon1234'</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠️ 'jevon1234' already exists</p>";
}

// ==========================
// ADMIN 2: adiah1234
// ==========================
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute(['adiah1234']);
$exists = $stmt->fetch();

if (!$exists) {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, coins) VALUES (?, ?, 'admin', 999999)");
    if ($stmt->execute(['adiah1234', $adminPassword])) {
        echo "<p style='color:green;'>✅ Admin 'adiah1234' created</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create 'adiah1234'</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠️ 'adiah1234' already exists</p>";
}

// ==========================
// TEST USER
// ==========================
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute(['testuser']);
$exists = $stmt->fetch();

if (!$exists) {
    $testPassword = password_hash('test123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, coins) VALUES (?, ?, 'user', 100)");
    if ($stmt->execute(['testuser', $testPassword])) {
        echo "<p style='color:green;'>✅ Test user created</p>";
    }
}

echo "<hr>";
echo "<h3>📋 Login Details:</h3>";
echo "<p><strong>Admins:</strong><br>";
echo "jevon1234 / admin123<br>";
echo "adiah1234 / admin123</p>";

echo "<p><strong>Test User:</strong><br>";
echo "testuser / test123</p>";

echo "<hr>";
echo "<a href='login.php' class='btn'>Go to Login →</a>";

echo "</div></body></html>";
?>