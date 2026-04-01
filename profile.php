<?php
// profile.php - User Profile with Unlockable Badges & Themes
require_once 'config.php';
requireLogin();  // This forces login if not logged in

// Get user data from database if needed
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
    
    <!-- Badges Section (Unlockable) -->
    <div class="section-title">
        <span>🏷️</span> My Badges
        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">(Earned through achievements)</span>
    </div>
    <div class="badges-grid" id="badgesList"></div>
    
    <!-- Themes Section (Unlockable) -->
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
// Profile Manager with Unlockable Badges & Themes
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
        
        // Badges that are earned through achievements (NOT purchasable)
        this.badges = {
            'Math Wizard': { name: '🧙 Math Wizard', icon: '🧙', desc: 'Master of all mathematical arts!', howTo: 'Complete all algebra levels' },
            'Speed King': { name: '⚡ Speed King', icon: '⚡', desc: 'Lightning fast calculations!', howTo: 'Answer 50 questions under 2 seconds' },
            'Perfect Master': { name: '⭐ Perfect Master', icon: '⭐', desc: 'Perfection in every level!', howTo: 'Get 3 stars on 50 levels' },
            'Coin Hoarder': { name: '🪙 Coin Hoarder', icon: '🪙', desc: 'Rich beyond measure!', howTo: 'Earn 10,000 total coins' },
            'Question Slayer': { name: '🗡️ Question Slayer', icon: '🗡️', desc: 'Defeated thousands of questions!', howTo: 'Answer 500 questions correctly' },
            'Addition Ace': { name: '➕ Addition Ace', icon: '➕', desc: 'Master of addition!', howTo: 'Complete all 45 addition levels' },
            'Subtraction Ace': { name: '➖ Subtraction Ace', icon: '➖', desc: 'Master of subtraction!', howTo: 'Complete all 45 subtraction levels' },
            'Multiplication Ace': { name: '✖️ Multiplication Ace', icon: '✖️', desc: 'Master of multiplication!', howTo: 'Complete all 45 multiplication levels' },
            'Division Master': { name: '➗ Division Master', icon: '➗', desc: 'Master of division!', howTo: 'Get 3 stars on all 45 division levels' },
            'Algebra Expert': { name: 'x Algebra Expert', icon: 'x', desc: 'Algebra genius!', howTo: 'Complete all 45 algebra levels' }
        };
        
        // Themes that are earned through achievements or spin wheel (NOT purchasable)
        this.themes = {
            'ocean': { name: '🌊 Ocean Blue', icon: '🌊', desc: 'Calm blue ocean theme for relaxed gaming', howTo: 'Get 3 stars on 20 levels' },
            'sunset': { name: '🌅 Sunset Red', icon: '🌅', desc: 'Beautiful sunset colors for evening play', howTo: 'Complete 10 levels with no mistakes' },
            'forest': { name: '🌲 Forest Green', icon: '🌲', desc: 'Peaceful forest theme for focus', howTo: 'Answer 100 questions correctly' }
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
            unlockedBadges: this.getUnlockedBadges(),
            unlockedThemes: this.getUnlockedThemes()
        };
    }
    
    getUnlockedBadges() {
        const unlocked = JSON.parse(localStorage.getItem('mathQuest_unlockedBadges') || '[]');
        const achievements = JSON.parse(localStorage.getItem('mathQuest_achievements') || '{}');
        
        // Check each badge against achievements
        if (achievements['algebra_expert']?.unlocked && !unlocked.includes('Math Wizard')) {
            unlocked.push('Math Wizard');
        }
        if (achievements['speed_demon_20']?.unlocked && !unlocked.includes('Speed King')) {
            unlocked.push('Speed King');
        }
        if (achievements['total_perfect_50']?.unlocked && !unlocked.includes('Perfect Master')) {
            unlocked.push('Perfect Master');
        }
        if (achievements['coin_10000']?.unlocked && !unlocked.includes('Coin Hoarder')) {
            unlocked.push('Coin Hoarder');
        }
        if (achievements['total_levels_100']?.unlocked && !unlocked.includes('Question Slayer')) {
            unlocked.push('Question Slayer');
        }
        if (achievements['addition_ace']?.unlocked && !unlocked.includes('Addition Ace')) {
            unlocked.push('Addition Ace');
        }
        if (achievements['subtraction_ace']?.unlocked && !unlocked.includes('Subtraction Ace')) {
            unlocked.push('Subtraction Ace');
        }
        if (achievements['multiplication_ace']?.unlocked && !unlocked.includes('Multiplication Ace')) {
            unlocked.push('Multiplication Ace');
        }
        if (achievements['division_master']?.unlocked && !unlocked.includes('Division Master')) {
            unlocked.push('Division Master');
        }
        if (achievements['algebra_expert']?.unlocked && !unlocked.includes('Algebra Expert')) {
            unlocked.push('Algebra Expert');
        }
        
        // Save updated unlocked badges
        localStorage.setItem('mathQuest_unlockedBadges', JSON.stringify(unlocked));
        return unlocked;
    }
    
    getUnlockedThemes() {
        const unlocked = JSON.parse(localStorage.getItem('mathQuest_unlockedThemes') || '["default"]');
        const achievements = JSON.parse(localStorage.getItem('mathQuest_achievements') || '{}');
        const spinRewards = JSON.parse(localStorage.getItem('mathQuest_spinRewards') || '[]');
        
        // Check achievements for theme unlocks
        if (achievements['total_perfect_20']?.unlocked && !unlocked.includes('ocean')) {
            unlocked.push('ocean');
        }
        if (achievements['no_mistakes_streak_10']?.unlocked && !unlocked.includes('sunset')) {
            unlocked.push('sunset');
        }
        if (achievements['total_levels_100']?.unlocked && !unlocked.includes('forest')) {
            unlocked.push('forest');
        }
        
        // Check spin wheel rewards for themes
        if (spinRewards.includes('ocean') && !unlocked.includes('ocean')) {
            unlocked.push('ocean');
        }
        if (spinRewards.includes('sunset') && !unlocked.includes('sunset')) {
            unlocked.push('sunset');
        }
        if (spinRewards.includes('forest') && !unlocked.includes('forest')) {
            unlocked.push('forest');
        }
        
        // Save updated unlocked themes
        localStorage.setItem('mathQuest_unlockedThemes', JSON.stringify(unlocked));
        return unlocked;
    }
    
    saveData(data) {
        localStorage.setItem('mathQuest_coins', data.coins);
        localStorage.setItem('mathQuest_avatar', data.currentAvatar);
        localStorage.setItem('mathQuest_theme', data.currentTheme);
        localStorage.setItem('mathQuest_badge', data.currentBadge);
        localStorage.setItem('mathQuest_frame', data.currentFrame);
        localStorage.setItem('mathQuest_unlockedAvatars', JSON.stringify(data.unlockedAvatars));
        localStorage.setItem('mathQuest_unlockedFrames', JSON.stringify(data.unlockedFrames));
        localStorage.setItem('mathQuest_unlockedThemes', JSON.stringify(data.unlockedThemes));
        localStorage.setItem('mathQuest_unlockedBadges', JSON.stringify(data.unlockedBadges));
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
                        if (stars && parseInt(stars) > 0) {
                            totalLevels++;
                        }
                        if (stars && parseInt(stars) === 3) {
                            perfectLevels++;
                        }
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
        
        const theme = themes[themeId] || themes.default;
        document.body.style.background = theme.bg;
    }
    
    equipItem(type, id) {
        const data = this.loadData();
        
        if (type === 'avatars') {
            if (!data.unlockedAvatars.includes(id)) {
                this.showMessage('🔒 You haven\'t unlocked this avatar yet! Visit the shop to unlock it.', true);
                return false;
            }
            data.currentAvatar = id;
        }
        
        if (type === 'badges') {
            if (!data.unlockedBadges.includes(id)) {
                this.showMessage('🔒 You haven\'t earned this badge yet! Complete achievements to unlock it.', true);
                return false;
            }
            data.currentBadge = id;
        }
        
        if (type === 'themes') {
            if (!data.unlockedThemes.includes(id)) {
                this.showMessage('🔒 You haven\'t unlocked this theme yet! Complete achievements or win it in the spin wheel.', true);
                return false;
            }
            data.currentTheme = id;
        }
        
        if (type === 'frames') {
            if (!data.unlockedFrames.includes(id)) {
                this.showMessage('🔒 You haven\'t unlocked this frame yet! Visit the shop to unlock it.', true);
                return false;
            }
            data.currentFrame = id;
        }
        
        this.saveData(data);
        this.showMessage(`✨ Equipped ${id}!`, false);
        this.render();
        
        if (type === 'themes') this.applyTheme(data.currentTheme);
        return true;
    }
    
    showMessage(text, isError = false) {
        // Remove any existing messages
        const existingMsg = document.querySelector('.message');
        if (existingMsg) existingMsg.remove();
        
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${isError ? 'error' : 'success'}`;
        msgDiv.textContent = text;
        msgDiv.style.position = 'fixed';
        msgDiv.style.top = '20px';
        msgDiv.style.right = '20px';
        msgDiv.style.zIndex = '1000';
        msgDiv.style.padding = '15px 25px';
        msgDiv.style.borderRadius = '12px';
        msgDiv.style.fontWeight = 'bold';
        msgDiv.style.animation = 'slideInRight 0.3s ease';
        msgDiv.style.backgroundColor = isError ? '#ef4444' : '#48bb78';
        msgDiv.style.color = 'white';
        document.body.appendChild(msgDiv);
        
        setTimeout(() => {
            msgDiv.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => msgDiv.remove(), 300);
        }, 3000);
    }
    
    renderAvatar(data) {
        const avatarDiv = document.getElementById('profileAvatar');
        avatarDiv.innerHTML = this.avatarEmojis[data.currentAvatar] || '🧑';
        
        const frameDiv = document.getElementById('profileFrame');
        frameDiv.style.cssText = this.frameStyles[data.currentFrame] || '';
        
        const badgeDiv = document.getElementById('profileBadge');
        if (data.currentBadge && this.badges[data.currentBadge]) {
            badgeDiv.innerHTML = this.badges[data.currentBadge].name;
            badgeDiv.style.display = 'inline-block';
        } else {
            badgeDiv.style.display = 'none';
        }
        
        const themeNames = {
            'default': 'Default Theme',
            'ocean': '🌊 Ocean Blue Theme',
            'sunset': '🌅 Sunset Red Theme',
            'forest': '🌲 Forest Green Theme'
        };
        document.getElementById('profileTheme').innerHTML = themeNames[data.currentTheme] || 'Default Theme';
    }
    
    renderAvatars(data) {
        const container = document.getElementById('avatarsList');
        container.innerHTML = '';
        
        const allAvatars = {
            'default': { name: 'Default', icon: '🧑', desc: 'Classic look' },
            'wizard': { name: 'Wizard', icon: '🧙', desc: 'Magical wizard' },
            'knight': { name: 'Knight', icon: '⚔️', desc: 'Brave knight' },
            'ninja': { name: 'Ninja', icon: '🥷', desc: 'Swift ninja' },
            'pirate': { name: 'Pirate', icon: '🏴‍☠️', desc: 'Adventurous pirate' },
            'robot': { name: 'Robot', icon: '🤖', desc: 'High-tech robot' },
            'dragon': { name: 'Dragon', icon: '🐉', desc: 'Legendary dragon' },
            'phoenix': { name: 'Phoenix', icon: '🔥', desc: 'Mythical phoenix' },
            'unicorn': { name: 'Unicorn', icon: '🦄', desc: 'Magical unicorn' },
            'viking': { name: 'Viking', icon: '⚡', desc: 'Fearsome viking' }
        };
        
        Object.entries(allAvatars).forEach(([id, avatar]) => {
            const isUnlocked = data.unlockedAvatars.includes(id);
            const isEquipped = data.currentAvatar === id;
            
            const card = document.createElement('div');
            card.className = `badge-card ${isEquipped ? 'equipped' : ''} ${!isUnlocked ? 'locked' : ''}`;
            
            if (isUnlocked && !isEquipped) {
                card.style.cursor = 'pointer';
                card.onclick = () => this.equipItem('avatars', id);
            } else if (!isUnlocked) {
                card.style.cursor = 'not-allowed';
            }
            
            card.innerHTML = `
                <div class="badge-icon">${avatar.icon}</div>
                <div class="badge-name">${avatar.name}</div>
                <div class="badge-desc">${avatar.desc}</div>
                <div class="badge-howto">${isUnlocked ? '✓ Available' : '🔒 Purchase in Shop'}</div>
                ${isUnlocked && !isEquipped ? '<button class="equip-btn">Equip</button>' : ''}
                ${isEquipped ? '<div class="status-equipped">✓ Currently Equipped</div>' : ''}
            `;
            container.appendChild(card);
        });
    }
    
    renderBadges(data) {
        const container = document.getElementById('badgesList');
        container.innerHTML = '';
        
        Object.entries(this.badges).forEach(([id, badge]) => {
            const isUnlocked = data.unlockedBadges.includes(id);
            const isEquipped = data.currentBadge === id;
            
            const card = document.createElement('div');
            card.className = `badge-card ${isEquipped ? 'equipped' : ''} ${!isUnlocked ? 'locked' : ''}`;
            
            if (isUnlocked && !isEquipped) {
                card.style.cursor = 'pointer';
                card.onclick = () => this.equipItem('badges', id);
            } else if (!isUnlocked) {
                card.style.cursor = 'not-allowed';
            }
            
            card.innerHTML = `
                <div class="badge-icon">${badge.icon}</div>
                <div class="badge-name">${badge.name}</div>
                <div class="badge-desc">${badge.desc}</div>
                <div class="badge-howto">${isUnlocked ? '✓ Unlocked!' : `🔒 ${badge.howTo}`}</div>
                ${isUnlocked && !isEquipped ? '<button class="equip-btn">Equip</button>' : ''}
                ${isEquipped ? '<div class="status-equipped">✓ Currently Equipped</div>' : ''}
            `;
            container.appendChild(card);
        });
    }
    
    renderThemes(data) {
        const container = document.getElementById('themesList');
        container.innerHTML = '';
        
        Object.entries(this.themes).forEach(([id, theme]) => {
            const isUnlocked = data.unlockedThemes.includes(id);
            const isEquipped = data.currentTheme === id;
            
            const card = document.createElement('div');
            card.className = `theme-card ${isEquipped ? 'equipped' : ''} ${!isUnlocked ? 'locked' : ''}`;
            
            if (isUnlocked && !isEquipped) {
                card.style.cursor = 'pointer';
                card.onclick = () => this.equipItem('themes', id);
            } else if (!isUnlocked) {
                card.style.cursor = 'not-allowed';
            }
            
            card.innerHTML = `
                <div class="theme-icon">${theme.icon}</div>
                <div class="theme-name">${theme.name}</div>
                <div class="theme-desc">${theme.desc}</div>
                <div class="theme-howto">${isUnlocked ? '✓ Unlocked!' : `🔒 ${theme.howTo}`}</div>
                ${isUnlocked && !isEquipped ? '<button class="equip-btn">Equip</button>' : ''}
                ${isEquipped ? '<div class="status-equipped">✓ Currently Equipped</div>' : ''}
            `;
            container.appendChild(card);
        });
    }
    
    renderFrames(data) {
        const container = document.getElementById('framesList');
        container.innerHTML = '';
        
        const allFrames = [
            { id: 'gold', name: 'Gold Frame', icon: '👑', desc: 'Royal golden border' },
            { id: 'silver', name: 'Silver Frame', icon: '🥈', desc: 'Shiny silver border' },
            { id: 'diamond', name: 'Diamond Frame', icon: '💎', desc: 'Sparkling diamond border' },
            { id: 'ruby', name: 'Ruby Frame', icon: '🔴', desc: 'Precious ruby border' }
        ];
        
        allFrames.forEach(frame => {
            const isUnlocked = data.unlockedFrames.includes(frame.id);
            const isEquipped = data.currentFrame === frame.id;
            
            const card = document.createElement('div');
            card.className = `badge-card ${isEquipped ? 'equipped' : ''} ${!isUnlocked ? 'locked' : ''}`;
            
            if (isUnlocked && !isEquipped) {
                card.style.cursor = 'pointer';
                card.onclick = () => this.equipItem('frames', frame.id);
            } else if (!isUnlocked) {
                card.style.cursor = 'not-allowed';
            }
            
            card.innerHTML = `
                <div class="badge-icon">${frame.icon}</div>
                <div class="badge-name">${frame.name}</div>
                <div class="badge-desc">${frame.desc}</div>
                <div class="badge-howto">${isUnlocked ? '✓ Available' : '🔒 Purchase in Shop'}</div>
                ${isUnlocked && !isEquipped ? '<button class="equip-btn">Equip</button>' : ''}
                ${isEquipped ? '<div class="status-equipped">✓ Currently Equipped</div>' : ''}
            `;
            container.appendChild(card);
        });
    }
    
    render() {
        const data = this.loadData();
        const stats = this.calculateStats();
        
        document.getElementById('profileLevels').textContent = stats.totalLevels;
        document.getElementById('profilePerfect').textContent = stats.perfectLevels;
        
        this.renderAvatar(data);
        this.renderAvatars(data);
        this.renderBadges(data);
        this.renderThemes(data);
        this.renderFrames(data);
        
        this.applyTheme(data.currentTheme);
    }
    
    init() {
        this.render();
        // Update stats every 5 seconds
        setInterval(() => {
            const stats = this.calculateStats();
            document.getElementById('profileLevels').textContent = stats.totalLevels;
            document.getElementById('profilePerfect').textContent = stats.perfectLevels;
        }, 5000);
    }
}

// Initialize profile manager when page loads
document.addEventListener('DOMContentLoaded', () => {
    const profile = new ProfileManager();
});

// Add animation keyframes if not present
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
    
    .message {
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        z-index: 10000;
    }
    
    .badge-card, .theme-card {
        transition: all 0.3s ease;
    }
    
    .badge-card:hover:not(.locked), .theme-card:hover:not(.locked) {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    
    .equip-btn {
        background: linear-gradient(135deg, #48bb78, #2f855a);
        color: white;
        border: none;
        padding: 6px 15px;
        border-radius: 25px;
        cursor: pointer;
        font-size: 0.75rem;
        font-weight: bold;
        margin-top: 10px;
        transition: all 0.2s;
    }
    
    .equip-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(72,187,120,0.4);
    }
    
    .status-equipped {
        color: #48bb78;
        font-size: 0.75rem;
        font-weight: bold;
        margin-top: 8px;
    }
`;
document.head.appendChild(style);
</script>

<?php include 'background-music.php'; ?>

</body>
</html>