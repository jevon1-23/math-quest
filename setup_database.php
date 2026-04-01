<?php
echo "<h1>Setting up Math Quest Database</h1>";

$host = 'dpg-d76ddqfkijhs73bfrnd0-a';
$port = '5432';
$dbname = 'math_quest';
$user = 'math_user';
$password = 'U3oUoGJEFt3j1ozOnbNtINIQeHB8cLRr';

echo "<p>Connecting to database...</p>";

try {
    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require");
    
    if (!$conn) {
        die("Connection failed: " . pg_last_error());
    }
    
    echo "<p style='color:green'>✓ Connected to database</p>";
    
    // Create users table
    $result = pg_query($conn, "
    CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        coins INTEGER DEFAULT 0,
        role VARCHAR(20) DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    if ($result) {
        echo "<p>✓ Users table created</p>";
    }
    
    // Create user_progress table
    $result = pg_query($conn, "
    CREATE TABLE IF NOT EXISTS user_progress (
        id SERIAL PRIMARY KEY,
        user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
        skill VARCHAR(50) NOT NULL,
        level INTEGER NOT NULL,
        score INTEGER DEFAULT 0,
        stars INTEGER DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(user_id, skill, level)
    )");
    
    if ($result) {
        echo "<p>✓ User Progress table created</p>";
    }
    
    // Create user_achievements table
    $result = pg_query($conn, "
    CREATE TABLE IF NOT EXISTS user_achievements (
        id SERIAL PRIMARY KEY,
        user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
        achievement_id INTEGER NOT NULL,
        earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(user_id, achievement_id)
    )");
    
    if ($result) {
        echo "<p>✓ User Achievements table created</p>";
    }
    
    // Create user_activity table
    $result = pg_query($conn, "
    CREATE TABLE IF NOT EXISTS user_activity (
        id SERIAL PRIMARY KEY,
        user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
        action VARCHAR(100) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    if ($result) {
        echo "<p>✓ User Activity table created</p>";
    }
    
    // Create indexes
    pg_query($conn, "CREATE INDEX IF NOT EXISTS idx_user_progress_user_id ON user_progress(user_id)");
    pg_query($conn, "CREATE INDEX IF NOT EXISTS idx_user_progress_skill ON user_progress(skill)");
    pg_query($conn, "CREATE INDEX IF NOT EXISTS idx_user_achievements_user_id ON user_achievements(user_id)");
    
    echo "<p style='color:green'>✓ All tables created successfully!</p>";
    
    // Create test user
    $test_pass = password_hash('password123', PASSWORD_DEFAULT);
    $result = pg_query($conn, "
        INSERT INTO users (username, email, password, coins) 
        VALUES ('testuser', 'test@example.com', '$test_pass', 100)
        ON CONFLICT (username) DO NOTHING");
    
    if ($result) {
        echo "<p style='color:green'>✓ Test user created! (testuser / password123)</p>";
    }
    
    // Show all tables
    $result = pg_query($conn, "SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
    echo "<h2>Created Tables:</h2><ul>";
    while ($row = pg_fetch_assoc($result)) {
        echo "<li>" . $row['table_name'] . "</li>";
    }
    echo "</ul>";
    
    pg_close($conn);
    
    echo "<hr>";
    echo "<h2>Next Steps:</h2>";
    echo "<ol>";
    echo "<li><a href='register.php'>Create an account</a></li>";
    echo "<li><a href='login.php'>Login</a></li>";
    echo "<li><a href='index.php'>Start playing Math Quest!</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>