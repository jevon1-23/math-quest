<?php
// settings_fixed.php - Main Settings Page
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user = getCurrentUser();
$message = "";
$error = "";

// Handle settings update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = getDB();
        
        if (isset($_POST['update_profile'])) {
            $username = sanitize($_POST['username']);
            $email = sanitize($_POST['email']);
            
            // Check if username already exists
            $check = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $check->execute([$username, $_SESSION['user_id']]);
            if ($check->fetch()) {
                $error = "Username already taken!";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->execute([$username, $email, $_SESSION['user_id']]);
                $message = "✅ Profile updated successfully!";
                $_SESSION['user_name'] = $username;
                $user = getCurrentUser();
            }
        }
        
        if (isset($_POST['change_password'])) {
            $current = $_POST['current_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            
            if (strlen($new) < 6) {
                $error = "Password must be at least 6 characters!";
            } elseif ($new !== $confirm) {
                $error = "New passwords don't match!";
            } else {
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user_data = $stmt->fetch();
                
                if (password_verify($current, $user_data['password'])) {
                    $new_hash = password_hash($new, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$new_hash, $_SESSION['user_id']]);
                    $message = "✅ Password changed successfully!";
                } else {
                    $error = "Current password is incorrect!";
                }
            }
        }
        
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get updated user data
$user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Settings - Math Quest</title>
    <link rel="stylesheet" href="style.css?v=2">
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #060f3a, #0a2a6e);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .settings-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px 40px;
        }
        
        .settings-section {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
        }
        
        .settings-section h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #ffd700;
            border-bottom: 2px solid rgba(255,215,0,0.3);
            padding-bottom: 10px;
            font-size: 1.3rem;
        }
        
        .settings-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .settings-row:last-child {
            border-bottom: none;
        }
        
        .toggle {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 26px;
        }
        
        .toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 34px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: #4CAF50;
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        input[type="range"] {
            width: 150px;
            margin: 0 10px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            margin-top: 20px;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76,175,80,0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #f44336, #d32f2f);
        }
        
        .btn-danger:hover {
            box-shadow: 0 5px 15px rgba(244,67,54,0.4);
        }
        
        .message {
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        
        .message.success {
            background: rgba(76,175,80,0.2);
            color: #4CAF50;
            border: 1px solid #4CAF50;
        }
        
        .message.error {
            background: rgba(244,67,54,0.2);
            color: #f44336;
            border: 1px solid #f44336;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            background: rgba(255,255,255,0.9);
            font-size: 14px;
            transition: all 0.3s;
        }
        
        input:focus {
            border-color: #ffd700;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255,215,0,0.2);
        }
        
        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        h2 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #ffd700;
            font-size: 1.2rem;
        }
        
        .stat {
            margin: 12px 0;
            padding: 12px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .stat strong {
            color: rgba(255,255,255,0.8);
        }
        
        .stat span {
            color: #ffd700;
            font-weight: bold;
        }
        
        .coin-value {
            color: #ffd700;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            display: inline-block;
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            padding: 10px 25px;
            border-radius: 30px;
            transition: all 0.3s;
        }
        
        .back-link a:hover {
            background: rgba(255,215,0,0.3);
            transform: translateY(-2px);
        }
        
        footer {
            text-align: center;
            padding: 20px;
            color: rgba(255,255,255,0.5);
            font-size: 0.8rem;
        }
        
        #logisticsPanel {
            margin-top: 20px;
            padding: 20px;
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
        }
        
        #logisticsPanel h4 {
            color: #ffd700;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        #logisticsPanel ul, #logisticsPanel p {
            color: rgba(255,255,255,0.8);
        }
        
        #logisticsPanel li {
            margin: 5px 0;
            color: rgba(255,255,255,0.8);
        }
        
        #moreBtn, #lessBtn {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            margin-top: 15px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        #moreBtn:hover, #lessBtn:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(33,150,243,0.4);
        }
        
        @media (max-width: 600px) {
            .settings-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .settings-section {
                padding: 20px;
            }
            
            .settings-container {
                padding: 0 15px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            .stat {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="settings-container">
    <h1>⚙️ Settings</h1>
    
    <?php if ($message): ?>
        <div class="message success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Profile Settings -->
    <div class="settings-section">
        <h3>👤 Profile Information</h3>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
            
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            
            <button type="submit" name="update_profile" class="btn">Update Profile</button>
        </form>
    </div>

    <!-- Password Settings -->
    <div class="settings-section">
        <h3>🔒 Change Password</h3>
        <form method="POST">
            <label>Current Password:</label>
            <input type="password" name="current_password" required>
            
            <label>New Password:</label>
            <input type="password" name="new_password" required minlength="6">
            
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required>
            
            <button type="submit" name="change_password" class="btn">Change Password</button>
        </form>
    </div>

    <!-- Audio Settings -->
    <div class="settings-section">
        <h3>🔊 Audio</h3>
        <div class="settings-row">
            <span>Game sounds</span>
            <label class="toggle">
                <input type="checkbox" id="soundToggle" checked onchange="toggleSound(this)">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="settings-row">
            <span>Music</span>
            <label class="toggle">
                <input type="checkbox" id="musicToggle" onchange="toggleMusic(this)">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="settings-row" id="musicVolumeRow">
            <span>Music volume</span>
            <div>
                <input type="range" id="musicVolumeSlider" min="0" max="100" value="50" oninput="setMusicVolume(this.value)">
                <span id="volumeValue">50%</span>
            </div>
        </div>
    </div>

    <!-- Game Settings -->
    <div class="settings-section">
        <h3>🎮 Game</h3>
        <div class="settings-row">
            <span>Show fastest time</span>
            <label class="toggle">
                <input type="checkbox" id="fastestTimeToggle" checked onchange="saveSetting('showFastestTime', this.checked)">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="settings-row">
            <span>Animations</span>
            <label class="toggle">
                <input type="checkbox" id="animToggle" checked onchange="saveSetting('animations', this.checked)">
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>

    <!-- Account Statistics -->
    <div class="settings-section">
        <h3>📊 Account Statistics</h3>
        <div class="stat"><strong>Username:</strong> <span><?php echo htmlspecialchars($user['username'] ?? ''); ?></span></div>
        <div class="stat"><strong>Email:</strong> <span><?php echo htmlspecialchars($user['email'] ?? ''); ?></span></div>
        <div class="stat"><strong>💰 Coins:</strong> <span class="coin-value"><?php echo number_format($user['coins'] ?? 0); ?></span></div>
        <div class="stat"><strong>👑 Role:</strong> <span><?php echo ucfirst($user['role'] ?? 'User'); ?></span></div>
        <div class="stat"><strong>📅 Member since:</strong> <span><?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></span></div>
    </div>

    <!-- About Section -->
    <div class="settings-section">
        <h3>📖 About Math Quest</h3>
        <div>
            <p style="color: rgba(255,255,255,0.9);"><strong>Math Quest</strong> is an interactive math learning game designed to make practicing math fun and engaging for students of all levels.</p>
            <p style="color: rgba(255,255,255,0.9);">From beginner addition all the way up to grand master logarithms and trigonometry — earn stars, climb the leaderboard, and master every level!</p>
            <p style="color: rgba(255,255,255,0.7);">© 2026 Math Quest | Created by Jevon Andrews</p>
            <p><strong style="color: #ffd700;">Version 2.0</strong></p>

            <div>
                <button id="moreBtn" onclick="toggleLogistics()">📖 Learn More ▼</button>
            </div>

            <div id="logisticsPanel" style="display:none;">
                <div>
                    <h4>🗺️ How Levels Work</h4>
                    <p>There are <strong>45 levels</strong> per game mode, split into three zones with <strong>3 Boss levels</strong>:</p>
                    <ul>
                        <li>🟢 <strong>Levels 1–10</strong> — Easy (13 questions each)</li>
                        <li>🟡 <strong>Levels 11–25</strong> — Medium (13 questions) · 👹 Level 11 is a Boss (23 questions)</li>
                        <li>🔴 <strong>Levels 26–45</strong> — Hard (13 questions) · 👹 Levels 25 & 45 are Bosses (23 questions)</li>
                    </ul>
                    <p>Complete a level with at least <strong>1 star</strong> to unlock the next one.</p>
                </div>

                <div>
                    <h4>⭐ Star System</h4>
                    <p>Stars are earned based on correct answers. Thresholds differ for regular vs boss levels:</p>
                    <ul>
                        <li><strong>Regular levels (13 questions):</strong> ⭐⭐⭐ 11–13 correct | ⭐⭐ 8–10 correct | ⭐ 4–7 correct | ☆☆☆ 3 or fewer</li>
                        <li><strong>Boss levels (23 questions):</strong> ⭐⭐⭐ 20–23 correct | ⭐⭐ 9–19 correct | ⭐ 6–8 correct | ☆☆☆ 5 or fewer</li>
                    </ul>
                </div>

                <div>
                    <h4>💰 Scoring</h4>
                    <p>Points are awarded for every correct answer. The faster you answer, the bigger your speed bonus. Higher levels multiply your score — by Level 45 you earn roughly <strong>7× more</strong> per correct answer than Level 1.</p>
                </div>

                <div>
                    <h4>🏆 Leaderboard</h4>
                    <p>Each game mode has its own leaderboard. Set your name in the leaderboard panel before playing. Your personal best score is saved — only your highest score counts.</p>
                    <p><strong>Tiers:</strong> 🏆 Champion (1st) · 💎 Diamond (2–4) · 🥇 Gold (5–8) · 🥈 Silver (9–13) · 🥉 Bronze (14–20)</p>
                </div>

                <div>
                    <h4>⏱️ Timer</h4>
                    <p>Each question has a countdown timer. If time runs out the level ends — so stay sharp! The timer turns orange when time is running low and red when almost out.</p>
                </div>

                <div>
                    <h4>🧮 Calculator</h4>
                    <p>A built-in scientific calculator is available for <strong>Advance, Expert, and Grand Master</strong> modes. It supports sin, cos, tan, log, ln, √, powers, and π. You can switch between degree and radian mode for trig functions.</p>
                </div>

                <div>
                    <h4>🎓 Skill Levels</h4>
                    <ul>
                        <li>🌱 <strong>Beginner</strong> — Addition, Subtraction, Multiplication, Division</li>
                        <li>🚀 <strong>Advance</strong> — Decimals, Fractions, Perimeter, Rounding</li>
                        <li>⚡ <strong>Expert</strong> — Algebra, Area, Factorization, Percentage, Simplifying</li>
                        <li>👑 <strong>Grand Master</strong> — Logarithms, Pythagorean Theorem, Trigonometry</li>
                    </ul>
                </div>

                <button id="lessBtn" onclick="toggleLogistics()">Show less ▲</button>
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="settings-section">
        <h3>🚪 Session</h3>
        <div style="text-align: center;">
            <form action="logout.php" method="POST">
                <button type="submit" class="btn btn-danger">🚪 Logout</button>
            </form>
            <p style="margin-top: 10px; color: rgba(255,255,255,0.5);">Log out of your Math Quest account</p>
        </div>
    </div>

    <div class="back-link">
        <a href="index.php">← Back to Home</a>
    </div>
</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<script>
function toggleLogistics() {
    const panel = document.getElementById('logisticsPanel');
    const moreBtn = document.getElementById('moreBtn');
    const lessBtn = document.getElementById('lessBtn');
    
    if (panel.style.display === 'block') {
        panel.style.display = 'none';
        if (moreBtn) moreBtn.style.display = 'inline-block';
    } else {
        panel.style.display = 'block';
        if (moreBtn) moreBtn.style.display = 'none';
    }
}

function loadSettings() {
    const sound = localStorage.getItem('mq_sound') !== 'false';
    const music = localStorage.getItem('mq_music') === 'true';
    const musicVolume = localStorage.getItem('bgMusicVolume') || '50';
    const fastest = localStorage.getItem('mq_showFastestTime') !== 'false';
    const anim = localStorage.getItem('mq_animations') !== 'false';
    
    const soundToggle = document.getElementById('soundToggle');
    const musicToggle = document.getElementById('musicToggle');
    const fastestToggle = document.getElementById('fastestTimeToggle');
    const animToggle = document.getElementById('animToggle');
    const volumeSlider = document.getElementById('musicVolumeSlider');
    const volumeValue = document.getElementById('volumeValue');
    const musicVolumeRow = document.getElementById('musicVolumeRow');
    
    if (soundToggle) soundToggle.checked = sound;
    if (musicToggle) musicToggle.checked = music;
    if (fastestToggle) fastestToggle.checked = fastest;
    if (animToggle) animToggle.checked = anim;
    
    if (volumeSlider) {
        volumeSlider.value = musicVolume;
        if (volumeValue) volumeValue.textContent = musicVolume + '%';
    }
    
    if (musicVolumeRow) {
        musicVolumeRow.style.display = music ? 'flex' : 'none';
    }
}

function toggleSound(el) {
    localStorage.setItem('mq_sound', el.checked);
}

function toggleMusic(el) {
    localStorage.setItem('mq_music', el.checked);
    const musicVolumeRow = document.getElementById('musicVolumeRow');
    if (musicVolumeRow) {
        musicVolumeRow.style.display = el.checked ? 'flex' : 'none';
    }
}

function setMusicVolume(value) {
    const volume = parseInt(value);
    const volumeValue = document.getElementById('volumeValue');
    if (volumeValue) volumeValue.textContent = volume + '%';
    localStorage.setItem('bgMusicVolume', volume);
}

function saveSetting(key, val) {
    localStorage.setItem('mq_' + key, val);
}

window.addEventListener('storage', function(e) {
    if (e.key === 'mq_music') {
        const musicToggle = document.getElementById('musicToggle');
        if (musicToggle) musicToggle.checked = e.newValue === 'true';
        const musicVolumeRow = document.getElementById('musicVolumeRow');
        if (musicVolumeRow) musicVolumeRow.style.display = e.newValue === 'true' ? 'flex' : 'none';
    }
    if (e.key === 'bgMusicVolume') {
        const volumeSlider = document.getElementById('musicVolumeSlider');
        const volumeValue = document.getElementById('volumeValue');
        if (volumeSlider) volumeSlider.value = e.newValue;
        if (volumeValue) volumeValue.textContent = e.newValue + '%';
    }
});

document.addEventListener('DOMContentLoaded', loadSettings);
</script>

<script src="/js/coin_sync.js"></script>
</body>
</html>