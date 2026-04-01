<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Debug Information</h1>";

// Try to connect
try {
    $host = 'dpg-d76ddqfkijhs73bfrnd0-a';
    $port = '5432';
    $dbname = 'math_quest';
    $user = 'math_user';
    $password = 'U3oUoGJEFt3j1ozOnbNtINIQeHB8cLRr';
    
    echo "<p>Attempting to connect to PostgreSQL...</p>";
    echo "Host: $host<br>";
    echo "Port: $port<br>";
    echo "Database: $dbname<br>";
    echo "User: $user<br>";
    
    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
    
    if (!$conn) {
        echo "<p style='color:red'>Connection failed: " . pg_last_error() . "</p>";
    } else {
        echo "<p style='color:green'>✓ Connected successfully!</p>";
        
        // Check if tables exist
        $result = pg_query($conn, "SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
        
        echo "<h2>Existing Tables:</h2>";
        if (pg_num_rows($result) == 0) {
            echo "<p>No tables found. You need to create your database tables!</p>";
        } else {
            echo "<ul>";
            while ($row = pg_fetch_assoc($result)) {
                echo "<li>" . $row['table_name'] . "</li>";
            }
            echo "</ul>";
        }
        
        pg_close($conn);
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
