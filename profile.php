<?php
// profile.php - User Profile with Unlockable Badges & Themes
require_once 'config.php';
requireLogin();

$user = getCurrentUser();
$userStats = getUserStats($_SESSION['user_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest - My Profile</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="profile-container">
    
    <!-- Character Card -->
    <div class="character-card">
        <div class="character-avatar">
            <div id="profileAvatar" class="avatar-main">🧑</div>
            <div id="profileFrame" class="avatar-frame"></div>
        </div>
        <div id="profileBadge" class="character-badge" style="display: none;"></div>
        <div id="profileTheme" style="margin-top: 10px; color: rgba(255,255,255,0.7);"></div>
    </div>
    
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🪙</div>
            <div class="stat-value" id="profileCoins"><?php echo number_format($_SESSION['user_coins'] ?? 0); ?></div>
            <div class="stat-label">Total Coins</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-value" id="profileLevels">0</div>
            <div class="stat-label">Levels Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-value" id="profilePerfect">0</div>
            <div class="stat-label">Perfect Levels</div>
        </div>
    </div>
    
    <!-- Avatars Section -->
    <div class="section-title">
        <span>👤</span> My Avatars
        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">(Click to equip)</span>
    </div>
    <div class="badges-grid" id="avatarsList"></div>
    
    <!-- Badges Section -->
    <div class="section-title">
        <span>🏷️</span> My Badges
        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">(Earned through achievements)</span>
    </div>
    <div class="badges-grid" id="badgesList"></div>
    
    <!-- Themes Section -->
    <div class="section-title">
        <span>🎨</span> My Themes
        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">(Earn through achievements or Spin Wheel)</span>
    </div>
    <div class="themes-grid" id="themesList"></div>
    
    <!-- Frames Section -->
    <div class="section-title">
        <span>🖼️</span> My Frames
        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">(Click to equip)</span>
    </div>
    <div class="badges-grid" id="framesList"></div>
    
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
// Profile Manager Class
class ProfileManager {
    constructor() {
        this.avatarEmojis = {
            'default': '🧑', 'wizard': '🧙', 'knight': '⚔️', 'ninja': '🥷',
            'pirate': '🏴‍☠️', 'robot': '🤖', 'dragon': '🐉', 'phoenix': '🔥',
            'unicorn': '🦄', 'viking': '⚡'
        };
        
        this.frameStyles = {
            'default': '', 
            'gold': 'box-shadow: 0 0 0 8px #ffd700, 0 0 0 15px #fbbf24;',
            'silver': 'box-shadow: 0 0 0 8px #c0c0c0, 0 0 0 15px #a0a0a0;',
            'diamond': 'box-shadow: 0 0 0 8px #b9f2ff, 0 0 0 15px #7fffd4;',
            'ruby': 'box-shadow: 0 0 0 8px #ff4444, 0 0 0 15px #cc3333;'
        };
        
        this.badges = {
            'Math Wizard': { name: '🧙 Math Wizard', icon: '🧙', desc: 'Master of all mathematical arts!', howTo: 'Complete all algebra levels' },
            'Speed King': { name: '⚡ Speed King', icon: '⚡', desc: 'Lightning fast calculations!', howTo: 'Answer 50 questions under 2 seconds' },
            'Perfect Master': { name: '⭐ Perfect Master', icon: '⭐', desc: 'Perfection in every level!', howTo: 'Get 3 stars on 50 levels' },
            'Coin Hoarder': { name: '🪙 Coin Hoarder', icon: '🪙', desc: 'Rich beyond measure!', howTo: 'Earn 10,000 total coins' },
            'Question Slayer': { name: '🗡️ Question Slayer', icon: '🗡️', desc: 'Defeated thousands of questions!', howTo: 'Answer 500 questions correctly' }
        };
        
        this.themes = {
            'ocean': { name: '🌊 Ocean Blue', icon: '🌊', desc: 'Calm blue ocean theme', howTo: 'Get 3 stars on 20 levels' },
            'sunset': { name: '🌅 Sunset Red', icon: '🌅', desc: 'Beautiful sunset colors', howTo: 'Complete 10 levels with no mistakes' },
            'forest': { name: '🌲 Forest Green', icon: '🌲', desc: 'Peaceful forest theme', howTo: 'Answer 100 questions correctly' }
        };
        
        this.init();
    }
    
    loadData() {
        return {
            coins: parseInt(localStorage.getItem('mathQuest_coins') || '0', 10),
            currentAvatar: localStorage.getItem('mathQuest_avatar') || 'default',
            currentTheme: localStorage.getItem('mathQuest_theme') || 'default',
            currentBadge: localStorage.getItem('mathQuest_badge') || null,
            currentFrame: localStorage.getItem('mathQuest_frame') || null,
            unlockedAvatars: JSON.parse(localStorage.getItem('mathQuest_unlockedAvatars') || '["default"]'),
            unlockedFrames: JSON.parse(localStorage.getItem('mathQuest_unlockedFrames') || '["default"]'),
            unlockedBadges: JSON.parse(localStorage.getItem('mathQuest_unlockedBadges') || '[]'),
            unlockedThemes: JSON.parse(localStorage.getItem('mathQuest_unlockedThemes') || '["default"]')
        };
    }
    
    calculateStats() {
        let totalLevels = 0;
        let perfectLevels = 0;
        
        const skills = ['beginner', 'advance', 'expert', 'grand-master'];
        const modes = {
            'beginner': ['add', 'subtract', 'multiply', 'div'],
            'advance': ['decimal', 'fractions', 'perimeter', 'rounding'],
            'expert': ['algebra', 'area', 'factorization', 'percentage', 'simplifying-expressions'],
            'grand-master': ['logarithms', 'pythagorean-theorem', 'trigonometry']
        };
        
        skills.forEach(skill => {
            if (modes[skill]) {
                modes[skill].forEach(mode => {
                    for (let level = 1; level <= 45; level++) {
                        const stars = localStorage.getItem(`mathQuest_${skill}_${mode}_level_${level}_stars`);
                        if (stars && parseInt(stars) > 0) totalLevels++;
                        if (stars && parseInt(stars) === 3) perfectLevels++;
                    }
                });
            }
        });
        
        return { totalLevels, perfectLevels };
    }
    
    applyTheme(themeId) {
        const themes = {
            'default': { bg: 'linear-gradient(135deg, #060f3a, #0a2a6e)' },
            'ocean': { bg: 'linear-gradient(135deg, #001f3f, #005f8c)' },
            'sunset': { bg: 'linear-gradient(135deg, #7c2d12, #b91c1c)' },
            'forest': { bg: 'linear-gradient(135deg, #14532d, #166534)' }
        };
        document.body.style.background = themes[themeId]?.bg || themes.default.bg;
    }
    
    equipItem(type, id) {
        const data = this.loadData();
        const unlockedKey = `unlocked${type.charAt(0).toUpperCase() + type.slice(1)}`;
        
        if (!data[unlockedKey].includes(id)) {
            this.showMessage('🔒 Not unlocked yet!', true);
            return false;
        }
        
        if (type === 'avatars') data.currentAvatar = id;
        if (type === 'frames') data.currentFrame = id;
        if (type === 'badges') data.currentBadge = id;
        if (type === 'themes') data.currentTheme = id;
        
        localStorage.setItem('mathQuest_avatar', data.currentAvatar);
        localStorage.setItem('mathQuest_frame', data.currentFrame);
        localStorage.setItem('mathQuest_badge', data.currentBadge);
        localStorage.setItem('mathQuest_theme', data.currentTheme);
        
        this.showMessage(`✨ Equipped!`, false);
        this.render();
        if (type === 'themes') this.applyTheme(data.currentTheme);
        return true;
    }
    
    showMessage(text, isError = false) {
        const existingMsg = document.querySelector('.message');
        if (existingMsg) existingMsg.remove();
        
        const msgDiv = document.createElement('div');
        msgDiv.textContent = text;
        msgDiv.style.cssText = `position:fixed;top:20px;right:20px;padding:12px 20px;border-radius:10px;z-index:1000;background:${isError ? '#ef4444' : '#48bb78'};color:white;font-weight:bold;animation:slideInRight 0.3s ease;`;
        document.body.appendChild(msgDiv);
        setTimeout(() => msgDiv.remove(), 3000);
    }
    
    render() {
        const data = this.loadData();
        const stats = this.calculateStats();
        
        document.getElementById('profileLevels').textContent = stats.totalLevels;
        document.getElementById('profilePerfect').textContent = stats.perfectLevels;
        document.getElementById('profileCoins').textContent = data.coins.toLocaleString();
        
        const avatarDiv = document.getElementById('profileAvatar');
        avatarDiv.innerHTML = this.avatarEmojis[data.currentAvatar] || '🧑';
        
        const frameDiv = document.getElementById('profileFrame');
        frameDiv.style.cssText = this.frameStyles[data.currentFrame] || '';
        
        this.applyTheme(data.currentTheme);
    }
    
    init() {
        this.render();
        setInterval(() => {
            const stats = this.calculateStats();
            document.getElementById('profileLevels').textContent = stats.totalLevels;
            document.getElementById('profilePerfect').textContent = stats.perfectLevels;
        }, 5000);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.profile = new ProfileManager();
});
</script>

<?php include 'background-music.php'; ?>

<!-- Coin Sync Script -->
<script src="/js/coin_sync.js"></script>
</body>
</html>