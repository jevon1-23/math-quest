<?php
echo "<h1>PHP Extension Test</h1>";

echo "<h2>Loaded Extensions:</h2>";
$extensions = get_loaded_extensions();
sort($extensions);
echo "<ul>";
foreach ($extensions as $ext) {
    $color = in_array($ext, ['pdo_pgsql', 'pgsql', 'pdo_mysql']) ? 'green' : 'black';
    echo "<li style='color: $color;'>$ext</li>";
}
echo "</ul>";

echo "<h2>PDO Drivers:</h2>";
echo "<pre>";
print_r(PDO::getAvailableDrivers());
echo "</pre>";

echo "<h2>Testing Database Connection:</h2>";
try {
    $host = 'dpg-d76ddqfkijhs73bfrnd0-a';
    $port = '5432';
    $dbname = 'math_quest';
    $user = 'math_user';
    $pass = 'U3oUoGJEFt3j1ozOnbNtINIQeHB8cLRr';
    
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require", $user, $pass);
    echo "<p style='color:green'>✓ Database connection successful!</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $row = $stmt->fetch();
    echo "<p>Total users: " . $row['count'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Connection failed: " . $e->getMessage() . "</p>";
}
?>