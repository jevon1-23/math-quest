<?php
// profile.php - User Profile with Database-Backed Unlockables & Account Management
require_once 'config.php';
requireLogin();

$user = getCurrentUser();
$message = "";
$error = "";

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = getDB();
        
        if (isset($_POST['update_profile'])) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $_SESSION['user_id']]);
            $message = "✅ Profile updated successfully!";
            
            // Update session
            $_SESSION['user_name'] = $username;
            $user = getCurrentUser();
        }
        
        if (isset($_POST['change_password'])) {
            $current = $_POST['current_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user_data = $stmt->fetch();
            
            if (password_verify($current, $user_data['password'])) {
                if ($new === $confirm) {
                    $new_hash = password_hash($new, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$new_hash, $_SESSION['user_id']]);
                    $message = "✅ Password changed successfully!";
                } else {
                    $error = "❌ New passwords don't match!";
                }
            } else {
                $error = "❌ Current password is incorrect!";
            }
        }
        
        // Handle equip item from AJAX
        if (isset($_POST['equip_item'])) {
            $item_type = $_POST['item_type'];
            $item_id = $_POST['item_id'];
            
            // Update database with equipped item
            $stmt = $pdo->prepare("UPDATE users SET current_{$item_type} = ? WHERE id = ?");
            $stmt->execute([$item_id, $_SESSION['user_id']]);
            
            echo json_encode(['success' => true]);
            exit;
        }
        
    } catch (PDOException $e) {
        $error = "❌ Database error: " . $e->getMessage();
    }
}

// Get user stats from database
function getUserStatsFromDB($userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT 
        COALESCE(SUM(levels_completed), 0) as total_levels,
        COALESCE(SUM(perfect_levels), 0) as perfect_levels
        FROM user_progress WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

$stats = getUserStatsFromDB($_SESSION['user_id']);

// Get unlocked items from database
function getUnlockedItems($userId, $type) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT item_id FROM user_unlocks WHERE user_id = ? AND item_type = ?");
    $stmt->execute([$userId, $type]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest - My Profile</title>
    <link rel="stylesheet" href="style.css?v=2">
    <style>
        /* Profile Page Styles */
        .profile-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
        }
        
        .profile-card {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .profile-card h3 {
            color: #ffd700;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 2px solid rgba(255,215,0,0.3);
            padding-bottom: 8px;
        }
        
        .character-card {
            text-align: center;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
        }
        
        .avatar-main {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            transition: all 0.3s;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
        }
        
        .stat-icon {
            font-size: 2rem;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffd700;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
        }
        
        .section-title {
            color: #ffd700;
            font-size: 1.2rem;
            margin: 20px 0 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid rgba(255,215,0,0.3);
        }
        
        .badges-grid, .themes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .badge-item, .theme-item, .avatar-item, .frame-item {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .badge-item:hover, .theme-item:hover, .avatar-item:hover, .frame-item:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.2);
        }
        
        .badge-item.locked, .theme-item.locked, .avatar-item.locked, .frame-item.locked {
            opacity: 0.5;
            filter: grayscale(0.3);
            cursor: not-allowed;
        }
        
        .badge-item.equipped, .theme-item.equipped, .avatar-item.equipped, .frame-item.equipped {
            border: 2px solid #ffd700;
            box-shadow: 0 0 15px rgba(255,215,0,0.5);
        }
        
        .item-icon {
            font-size: 2.5rem;
        }
        
        .item-name {
            margin-top: 8px;
            font-size: 0.8rem;
        }
        
        .item-desc {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.6);
            margin-top: 4px;
        }
        
        input, .btn {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .btn {
            background: #4CAF50;
            cursor: pointer;
            font-weight: bold;
        }
        
        .btn:hover {
            background: #45a049;
        }
        
        .wheel-info {
            background: rgba(255,215,0,0.1);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
        }
        
        .wheel-info a {
            color: #ffd700;
        }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="profile-container">
    
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="message error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Account Management Section (from second settings.php) -->
    <div class="profile-card">
        <h3>👤 Account Settings</h3>
        <form method="POST" action="">
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
            
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            
            <button type="submit" name="update_profile" class="btn">Update Profile</button>
        </form>
    </div>
    
    <div class="profile-card">
        <h3>🔒 Change Password</h3>
        <form method="POST" action="">
            <label>Current Password:</label>
            <input type="password" name="current_password" required>
            
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required>
            
            <button type="submit" name="change_password" class="btn">Change Password</button>
        </form>
    </div>
    
    <!-- Character Card -->
    <div class="character-card">
        <div class="avatar-main" id="profileAvatar">🧑</div>
        <div style="margin-top: 10px; color: rgba(255,255,255,0.7);" id="profileTheme"></div>
    </div>
    
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🪙</div>
            <div class="stat-value" id="profileCoins"><?php echo number_format($user['coins'] ?? 0); ?></div>
            <div class="stat-label">Total Coins</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-value" id="profileLevels"><?php echo $stats['total_levels'] ?? 0; ?></div>
            <div class="stat-label">Levels Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-value" id="profilePerfect"><?php echo $stats['perfect_levels'] ?? 0; ?></div>
            <div class="stat-label">Perfect Levels</div>
        </div>
    </div>
    
    <!-- Avatars Section -->
    <div class="section-title">👤 My Avatars <span style="font-size: 0.8rem;">(Click to equip)</span></div>
    <div class="badges-grid" id="avatarsList"></div>
    
    <!-- Frames Section -->
    <div class="section-title">🖼️ My Frames <span style="font-size: 0.8rem;">(Click to equip)</span></div>
    <div class="badges-grid" id="framesList"></div>
    
    <!-- Badges Section -->
    <div class="section-title">🏷️ My Badges <span style="font-size: 0.8rem;">(Earned through achievements)</span></div>
    <div class="badges-grid" id="badgesList"></div>
    
    <!-- Themes Section -->
    <div class="section-title">🎨 My Themes <span style="font-size: 0.8rem;">(Earn through achievements or Spin Wheel)</span></div>
    <div class="themes-grid" id="themesList"></div>
    
    <!-- Wheel Info -->
    <div class="wheel-info">
        🎡 <strong>Want more themes?</strong> 🎡<br>
        Visit the <a href="daily-rewards.php">Daily Rewards</a> and spin the wheel for a chance to win exclusive themes!
    </div>
    
</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<script>
// Profile Manager with Database Backend
class ProfileManager {
    constructor() {
        this.currentUser = <?php echo json_encode($user); ?>;
        this.unlockedAvatars = <?php echo json_encode(getUnlockedItems($_SESSION['user_id'], 'avatar')); ?>;
        this.unlockedFrames = <?php echo json_encode(getUnlockedItems($_SESSION['user_id'], 'frame')); ?>;
        this.unlockedBadges = <?php echo json_encode(getUnlockedItems($_SESSION['user_id'], 'badge')); ?>;
        this.unlockedThemes = <?php echo json_encode(getUnlockedItems($_SESSION['user_id'], 'theme')); ?>;
        
        this.avatarEmojis = {
            'default': '🧑', 'wizard': '🧙', 'knight': '⚔️', 'ninja': '🥷',
            'pirate': '🏴‍☠️', 'robot': '🤖', 'dragon': '🐉', 'phoenix': '🔥',
            'unicorn': '🦄', 'viking': '⚡'
        };
        
        this.frameStyles = {
            'default': '', 
            'gold': 'box-shadow: 0 0 0 4px #ffd700',
            'silver': 'box-shadow: 0 0 0 4px #c0c0c0',
            'diamond': 'box-shadow: 0 0 0 4px #b9f2ff',
            'ruby': 'box-shadow: 0 0 0 4px #ff4444'
        };
        
        this.allAvatars = [
            { id: 'default', name: 'Default', icon: '🧑', howTo: 'Starting avatar' },
            { id: 'wizard', name: 'Wizard', icon: '🧙', howTo: 'Complete all algebra levels' },
            { id: 'knight', name: 'Knight', icon: '⚔️', howTo: 'Complete 50 levels' },
            { id: 'ninja', name: 'Ninja', icon: '🥷', howTo: 'Get 100 perfect answers' },
            { id: 'dragon', name: 'Dragon', icon: '🐉', howTo: 'Reach Grand Master rank' }
        ];
        
        this.allFrames = [
            { id: 'default', name: 'Default', icon: '🖼️', howTo: 'Starting frame' },
            { id: 'gold', name: 'Gold Frame', icon: '✨', howTo: 'Earn 5000 coins' },
            { id: 'silver', name: 'Silver Frame', icon: '⭐', howTo: 'Complete 100 levels' },
            { id: 'diamond', name: 'Diamond Frame', icon: '💎', howTo: 'Get 50 perfect levels' }
        ];
        
        this.allBadges = [
            { id: 'Math Wizard', name: 'Math Wizard', icon: '🧙', desc: 'Master of all mathematical arts!', howTo: 'Complete all algebra levels' },
            { id: 'Speed King', name: 'Speed King', icon: '⚡', desc: 'Lightning fast calculations!', howTo: 'Answer 50 questions under 2 seconds' },
            { id: 'Perfect Master', name: 'Perfect Master', icon: '⭐', desc: 'Perfection in every level!', howTo: 'Get 3 stars on 50 levels' },
            { id: 'Coin Hoarder', name: 'Coin Hoarder', icon: '🪙', desc: 'Rich beyond measure!', howTo: 'Earn 10,000 total coins' }
        ];
        
        this.allThemes = [
            { id: 'default', name: 'Default Theme', icon: '🌌', howTo: 'Starting theme' },
            { id: 'ocean', name: 'Ocean Blue', icon: '🌊', howTo: 'Get 3 stars on 20 levels' },
            { id: 'sunset', name: 'Sunset Red', icon: '🌅', howTo: 'Complete 10 levels with no mistakes' },
            { id: 'forest', name: 'Forest Green', icon: '🌲', howTo: 'Answer 100 questions correctly' }
        ];
        
        this.init();
    }
    
    equipItem(type, id) {
        // Send to database via AJAX
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `equip_item=1&item_type=${type}&item_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showMessage(`✨ Equipped ${type}!`, false);
                this.render();
            } else {
                this.showMessage('❌ Failed to equip', true);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showMessage('❌ Network error', true);
        });
    }
    
    showMessage(text, isError = false) {
        const existingMsg = document.querySelector('.message-floating');
        if (existingMsg) existingMsg.remove();
        
        const msgDiv = document.createElement('div');
        msgDiv.className = 'message-floating';
        msgDiv.textContent = text;
        msgDiv.style.cssText = `position:fixed;top:20px;right:20px;padding:12px 20px;border-radius:10px;z-index:1000;background:${isError ? '#ef4444' : '#48bb78'};color:white;font-weight:bold;animation:slideInRight 0.3s ease;`;
        document.body.appendChild(msgDiv);
        setTimeout(() => msgDiv.remove(), 3000);
    }
    
    render() {
        // Render avatars
        const avatarsContainer = document.getElementById('avatarsList');
        if (avatarsContainer) {
            avatarsContainer.innerHTML = this.allAvatars.map(avatar => `
                <div class="avatar-item ${!this.unlockedAvatars.includes(avatar.id) ? 'locked' : ''} ${this.currentUser.current_avatar === avatar.id ? 'equipped' : ''}" 
                     onclick="${this.unlockedAvatars.includes(avatar.id) ? `profile.equipItem('avatar', '${avatar.id}')` : ''}">
                    <div class="item-icon">${avatar.icon}</div>
                    <div class="item-name">${avatar.name}</div>
                    <div class="item-desc">${!this.unlockedAvatars.includes(avatar.id) ? '🔒 ' + avatar.howTo : ''}</div>
                </div>
            `).join('');
        }
        
        // Similar for frames, badges, themes...
        const framesContainer = document.getElementById('framesList');
        if (framesContainer) {
            framesContainer.innerHTML = this.allFrames.map(frame => `
                <div class="frame-item ${!this.unlockedFrames.includes(frame.id) ? 'locked' : ''} ${this.currentUser.current_frame === frame.id ? 'equipped' : ''}"
                     onclick="${this.unlockedFrames.includes(frame.id) ? `profile.equipItem('frame', '${frame.id}')` : ''}">
                    <div class="item-icon">${frame.icon}</div>
                    <div class="item-name">${frame.name}</div>
                    <div class="item-desc">${!this.unlockedFrames.includes(frame.id) ? '🔒 ' + frame.howTo : ''}</div>
                </div>
            `).join('');
        }
        
        // Update avatar display
        const avatarDiv = document.getElementById('profileAvatar');
        if (avatarDiv && this.currentUser.current_avatar) {
            avatarDiv.innerHTML = this.avatarEmojis[this.currentUser.current_avatar] || '🧑';
        }
    }
    
    init() {
        this.render();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.profile = new ProfileManager();
});
</script>

<?php include 'background-music.php'; ?>

</body>
</html>