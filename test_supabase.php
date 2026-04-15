<?php
// test_supabase.php - Test Supabase connection
echo "<h1>Testing Supabase Connection</h1>";

// Test 1: Can we reach the host?
$host = 'db.ebprinvbcgwuelefdqkz.supabase.co';
echo "<h3>Test 1: Pinging host...</h3>";
$connection = @fsockopen($host, 5432, $errno, $errstr, 5);
if ($connection) {
    echo "<p style='color:green'>✅ Can reach $host on port 5432</p>";
    fclose($connection);
} else {
    echo "<p style='color:red'>❌ Cannot reach $host: $errstr</p>";
}

// Test 2: Try PDO connection
echo "<h3>Test 2: Testing PDO connection...</h3>";
try {
    $dsn = "pgsql:host=db.ebprinvbcgwuelefdqkz.supabase.co;port=5432;dbname=postgres;sslmode=require";
    $pdo = new PDO($dsn, 'postgres', 'Jevon.andrews22', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<p style='color:green'>✅ Successfully connected to Supabase!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT NOW() as time");
    $result = $stmt->fetch();
    echo "<p>Server time: " . $result['time'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Connection failed: " . $e->getMessage() . "</p>";
}
?>