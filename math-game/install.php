<?php
// install.php - Run once to set up database on Render
require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Math Quest - Installation</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        .install-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #667eea; margin-bottom: 20px; }
        .success { color: #48bb78; }
        .error { color: #f56565; }
        .step { 
            padding: 10px; 
            margin: 10px 0; 
            border-left: 4px solid #667eea;
            background: #f7fafc;
        }
        .step.success { border-left-color: #48bb78; }
        .step.error { border-left-color: #f56565; }
        button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            font-size: 16px;
        }
        button:hover { transform: translateY(-2px); }
        .warning { background: #fef5e7; border-left-color: #f39c12; }
    </style>
</head>
<body>
    <div class='install-container'>
        <h1>🎮 Math Quest Installation</h1>";

try {
    $pdo = getDB();
    echo "<div class='step success'>✅ Database connection successful</div>";
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        coins INT DEFAULT 0,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        INDEX idx_username (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<div class='step success'>✅ Users table created</div>";
    
    // Create user_progress table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        skill VARCHAR(20) NOT NULL,
        level INT NOT NULL,
        score INT DEFAULT 0,
        stars INT DEFAULT 0,
        time_played INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_progress (user_id, skill, level),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_skill (user_id, skill)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<div class='step success'>✅ User Progress table created</div>";
    
    // Create achievements table
    $pdo->exec("CREATE TABLE IF NOT EXISTS achievements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        icon VARCHAR(50),
        coins_reward INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<div class='step success'>✅ Achievements table created</div>";
    
    // Create user_achievements table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_achievements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        achievement_id INT NOT NULL,
        earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_achievement (user_id, achievement_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<div class='step success'>✅ User Achievements table created</div>";
    
    // Create shop_items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS shop_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        type ENUM('avatar', 'theme', 'powerup') DEFAULT 'avatar',
        price INT NOT NULL,
        icon VARCHAR(50),
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<div class='step success'>✅ Shop Items table created</div>";
    
    // Create user_inventory table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_inventory (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        item_id INT NOT NULL,
        is_equipped BOOLEAN DEFAULT FALSE,
        purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_item (user_id, item_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES shop_items(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<div class='step success'>✅ User Inventory table created</div>";
    
    // Create daily_rewards table
    $pdo->exec("CREATE TABLE IF NOT EXISTS daily_rewards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        day_number INT NOT NULL,
        claimed_at DATE NOT NULL,
        coins_reward INT NOT NULL,
        UNIQUE KEY unique_daily (user_id, day_number),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_date (user_id, claimed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<div class='step success'>✅ Daily Rewards table created</div>";
    
    // Create user_activity table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_activity (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        action VARCHAR(100) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_action (user_id, action)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<div class='step success'>✅ User Activity table created</div>";
    
    // Insert default achievements
    $achievements = [
        ['First Steps', 'Complete your first level', '🎯', 50],
        ['Star Collector', 'Earn 10 stars total', '⭐', 100],
        ['Speed Demon', 'Complete a level in under 30 seconds', '⚡', 150],
        ['Perfect Score', 'Get 3 stars on any level', '💯', 200],
        ['Math Master', 'Complete all beginner levels', '🏆', 500],
        ['Coin Hoarder', 'Collect 1000 coins', '🪙', 100],
        ['Question Slayer', 'Answer 100 questions correctly', '🗡️', 150],
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO achievements (name, description, icon, coins_reward) VALUES (?, ?, ?, ?)");
    foreach ($achievements as $ach) {
        $stmt->execute($ach);
    }
    echo "<div class='step success'>✅ Sample achievements added</div>";
    
    // Insert default shop items
    $shopItems = [
        ['Math Wizard', 'Legendary wizard avatar', 'avatar', 500, '🧙'],
        ['Dragon Slayer', 'Epic dragon slayer avatar', 'avatar', 800, '🐉'],
        ['Ninja Master', 'Stealth ninja avatar', 'avatar', 600, '🥷'],
        ['Golden Theme', 'Premium gold theme', 'theme', 1000, '✨'],
        ['Silver Theme', 'Shiny silver theme', 'theme', 800, '🥈'],
        ['Extra Time', '+30 seconds in game', 'powerup', 200, '⏰'],
        ['Hint', 'Get a hint for current question', 'powerup', 100, '💡'],
        ['Shield', 'Protect from wrong answer', 'powerup', 150, '🛡️'],
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO shop_items (name, description, type, price, icon) VALUES (?, ?, ?, ?, ?)");
    foreach ($shopItems as $item) {
        $stmt->execute($item);
    }
    echo "<div class='step success'>✅ Sample shop items added</div>";
    
    // Check if admin exists, if not create it
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'admin@mathquest.com'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, coins) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Admin', 'admin@mathquest.com', $adminPass, 'admin', 1000]);
        echo "<div class='step success'>✅ Admin user created (email: admin@mathquest.com, password: admin123)</div>";
    } else {
        echo "<div class='step success'>✅ Admin user already exists</div>";
    }
    
    echo "<div class='step success' style='background: #c6f6d5;'>
            <strong>🎉 Installation Complete!</strong><br>
            Your Math Quest database is ready to use.
          </div>";
    
    echo "<div class='warning' style='background: #fef5e7; margin-top: 20px;'>
            <strong>⚠️ Important Security Note:</strong><br>
            Please <strong>DELETE or RENAME</strong> the install.php file after installation!
          </div>";
    
    echo "<button onclick=\"window.location.href='index.php'\">🎮 Go to Math Quest</button>";
    
} catch(PDOException $e) {
    echo "<div class='step error'>❌ Error: " . $e->getMessage() . "</div>";
    echo "<div class='step warning'>💡 Make sure you have created a database named 'math_quest' and configured it in Render.</div>";
    echo "<button onclick=\"window.location.href='index.php'\">← Back to Home</button>";
}

echo "</div></body></html>";
?>