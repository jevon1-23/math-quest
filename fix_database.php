<?php
echo "<h1>Fixing Database Schema</h1>";

$host = 'dpg-d76ddqfkijhs73bfrnd0-a';
$port = '5432';
$dbname = 'math_quest';
$user = 'math_user';
$password = 'U3oUoGJEFt3j1ozOnbNtINIQeHB8cLRr';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Check if progress column exists
$result = pg_query($conn, "SELECT column_name FROM information_schema.columns WHERE table_name='users' AND column_name='progress'");
$exists = pg_num_rows($result) > 0;

if (!$exists) {
    echo "<p>Adding 'progress' column to users table...</p>";
    pg_query($conn, "ALTER TABLE users ADD COLUMN progress INTEGER DEFAULT 0");
    echo "<p style='color:green'>✓ Progress column added</p>";
} else {
    echo "<p style='color:blue'>✓ Progress column already exists</p>";
}

// Check for other common missing columns
$columns = ['level', 'experience', 'total_score'];
foreach ($columns as $col) {
    $result = pg_query($conn, "SELECT column_name FROM information_schema.columns WHERE table_name='users' AND column_name='$col'");
    if (pg_num_rows($result) == 0) {
        echo "<p>Adding '$col' column...</p>";
        pg_query($conn, "ALTER TABLE users ADD COLUMN $col INTEGER DEFAULT 0");
        echo "<p style='color:green'>✓ $col column added</p>";
    }
}

// Show current users table structure
echo "<h2>Current Users Table Structure:</h2>";
$result = pg_query($conn, "SELECT column_name, data_type FROM information_schema.columns WHERE table_name='users' ORDER BY ordinal_position");
echo "<ul>";
while ($row = pg_fetch_assoc($result)) {
    echo "<li>" . $row['column_name'] . " (" . $row['data_type'] . ")</li>";
}
echo "</ul>";

pg_close($conn);

echo "<hr>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
?>