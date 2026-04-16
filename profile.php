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
            <div class="stat-value" id="profileCoins">0</div>
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
    
    <!-- Power-ups Section -->
    <div class="section-title">
        <span>⚡</span> My Power-ups
        <span style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">(Buy in Shop or win from Spin Wheel)</span>
    </div>
    <div class="badges-grid" id="powerupsList"></div>

    <!-- Wheel Info -->
    <div class="wheel-info">
        🎡 <strong>Want more items?</strong> 🎡<br>
        Visit the <a href="shop.php">Shop</a> to buy items or spin the <a href="daily-rewards.php">Lucky Wheel</a> to win exclusive themes &amp; power-ups!
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

        // ALL shop items mirrored here
        this.allAvatars = {
            'default': { name: '🧑 Default', icon: '🧑', desc: 'Your starter avatar', free: true },
            'wizard': { name: '🧙 Wizard', icon: '🧙', desc: 'Become a magical math wizard!', price: 3000 },
            'knight': { name: '⚔️ Knight', icon: '⚔️', desc: 'Brave and strong math warrior!', price: 2500 },
            'ninja': { name: '🥷 Ninja', icon: '🥷', desc: 'Swift and silent math master!', price: 2800 },
            'pirate': { name: '🏴‍☠️ Pirate', icon: '🏴‍☠️', desc: 'Adventurous treasure hunter!', price: 2600 },
            'robot': { name: '🤖 Robot', icon: '🤖', desc: 'High-tech math machine!', price: 3200 },
            'dragon': { name: '🐉 Dragon', icon: '🐉', desc: 'Legendary fire breather!', price: 5000 },
            'phoenix': { name: '🔥 Phoenix', icon: '🔥', desc: 'Rise from the ashes!', price: 4500 },
            'unicorn': { name: '🦄 Unicorn', icon: '🦄', desc: 'Magical and pure!', price: 4000 },
            'viking': { name: '⚡ Viking', icon: '⚡', desc: 'Fearsome Norse warrior!', price: 3500 }
        };

        this.allFrames = {
            'default': { name: '⬜ No Frame', icon: '⬜', desc: 'Plain border', free: true },
            'gold': { name: '👑 Gold Frame', icon: '👑', desc: 'Royal golden border', price: 1500 },
            'silver': { name: '🥈 Silver Frame', icon: '🥈', desc: 'Shiny silver border', price: 1000 },
            'diamond': { name: '💎 Diamond Frame', icon: '💎', desc: 'Sparkling diamond border', price: 2500 },
            'ruby': { name: '🔴 Ruby Frame', icon: '🔴', desc: 'Precious ruby border', price: 2000 }
        };

        this.allThemes = {
            'default': { name: '🌌 Default', icon: '🌌', desc: 'Classic dark blue', free: true },
            'ocean': { name: '🌊 Ocean Blue', icon: '🌊', desc: 'Calm blue ocean theme', howTo: 'Win from Lucky Spin or get 3★ on 20 levels' },
            'sunset': { name: '🌅 Sunset Red', icon: '🌅', desc: 'Beautiful sunset colors', howTo: 'Win from Lucky Spin or complete 10 levels perfectly' },
            'forest': { name: '🌲 Forest Green', icon: '🌲', desc: 'Peaceful forest theme', howTo: 'Win from Lucky Spin or answer 100 questions correctly' }
        };

        this.allPowerups = {
            'shield': { name: '🛡️ Shield', icon: '🛡️', desc: 'Protects from one wrong answer', price: 500 },
            'freeze': { name: '⏸️ Freeze Timer', icon: '⏸️', desc: 'Pauses timer for 10 seconds', price: 200 },
            'skip': { name: '⏭️ Skip Question', icon: '⏭️', desc: 'Skip a question without penalty', price: 150 },
            'retry_stars': { name: '🔄 Retry Keep Stars', icon: '🔄', desc: 'Retry level without losing stars', price: 800 }
        };

        this.badges = {
            'Math Wizard': { name: '🧙 Math Wizard', icon: '🧙', desc: 'Master of all mathematical arts!', howTo: 'Complete all algebra levels' },
            'Speed King': { name: '⚡ Speed King', icon: '⚡', desc: 'Lightning fast calculations!', howTo: 'Answer 50 questions under 2 seconds' },
            'Perfect Master': { name: '⭐ Perfect Master', icon: '⭐', desc: 'Perfection in every level!', howTo: 'Get 3 stars on 50 levels' },
            'Coin Hoarder': { name: '🪙 Coin Hoarder', icon: '🪙', desc: 'Rich beyond measure!', howTo: 'Earn 10,000 total coins' }
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
            unlockedThemes: JSON.parse(localStorage.getItem('mathQuest_unlockedThemes') || '["default"]'),
            powerups: JSON.parse(localStorage.getItem('mathQuest_powerups') || '{}')
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
        const unlockedKey = type === 'avatars' ? 'unlockedAvatars'
                          : type === 'frames'  ? 'unlockedFrames'
                          : type === 'themes'  ? 'unlockedThemes'
                          : 'unlockedBadges';

        if (!data[unlockedKey].includes(id)) {
            this.showMessage('🔒 Not unlocked yet! Buy it in the Shop or win it on the Spin Wheel.', true);
            return false;
        }

        if (type === 'avatars') { data.currentAvatar = id; localStorage.setItem('mathQuest_avatar', id); }
        if (type === 'frames')  { data.currentFrame = id;  localStorage.setItem('mathQuest_frame', id); }
        if (type === 'badges')  { data.currentBadge = id;  localStorage.setItem('mathQuest_badge', id); }
        if (type === 'themes')  { data.currentTheme = id;  localStorage.setItem('mathQuest_theme', id); this.applyTheme(id); }

        this.showMessage('✨ Equipped!', false);
        this.render();
        return true;
    }

    showMessage(text, isError = false) {
        const existingMsg = document.querySelector('.profile-message');
        if (existingMsg) existingMsg.remove();
        const msgDiv = document.createElement('div');
        msgDiv.className = 'profile-message';
        msgDiv.textContent = text;
        msgDiv.style.cssText = `position:fixed;top:20px;right:20px;padding:12px 20px;border-radius:10px;z-index:1000;background:${isError ? '#ef4444' : '#48bb78'};color:white;font-weight:bold;`;
        document.body.appendChild(msgDiv);
        setTimeout(() => msgDiv.remove(), 3000);
    }

    renderItemGrid(containerId, items, unlockedList, currentId, type) {
        const container = document.getElementById(containerId);
        if (!container) return;

        let html = '';
        Object.entries(items).forEach(([id, item]) => {
            const isUnlocked = item.free || unlockedList.includes(id);
            const isEquipped = currentId === id;

            const lockOverlay = !isUnlocked ? `
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.55);border-radius:15px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;">
                    <span style="font-size:1.6rem;">🔒</span>
                    <span style="color:rgba(255,255,255,0.8);font-size:0.7rem;font-weight:bold;text-align:center;padding:0 8px;">${item.price ? '🪙 ' + item.price.toLocaleString() : (item.howTo || 'Win on Spin Wheel')}</span>
                </div>` : '';

            const btnHtml = isUnlocked
                ? (isEquipped
                    ? `<button style="padding:6px 16px;border:none;border-radius:20px;background:rgba(255,255,255,0.2);color:rgba(255,255,255,0.6);cursor:default;font-weight:bold;" disabled>✓ Equipped</button>`
                    : `<button style="padding:6px 16px;border:none;border-radius:20px;background:linear-gradient(135deg,#58c4f5,#1e90ff);color:white;cursor:pointer;font-weight:bold;" onclick="window.profile.equipItem('${type}','${id}')">Equip</button>`)
                : `<a href="shop.php" style="padding:6px 16px;border:none;border-radius:20px;background:linear-gradient(135deg,#ffd700,#c9920a);color:#1a1a2e;cursor:pointer;font-weight:bold;text-decoration:none;display:inline-block;">Go to Shop</a>`;

            html += `
                <div style="position:relative;background:rgba(255,255,255,0.08);border-radius:15px;padding:18px 14px;text-align:center;border:2px solid ${isEquipped ? '#ffd700' : (isUnlocked ? 'rgba(255,215,0,0.2)' : 'rgba(255,255,255,0.1)')};transition:transform 0.3s;">
                    <div style="font-size:2.4rem;margin-bottom:8px;">${item.icon}</div>
                    <div style="font-weight:bold;font-size:0.95rem;color:#ffd700;margin-bottom:4px;">${item.name}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.6);margin-bottom:10px;">${item.desc}</div>
                    ${btnHtml}
                    ${lockOverlay}
                </div>`;
        });

        container.innerHTML = html;
    }

    renderPowerupsGrid(containerId, allPowerups, inventory) {
        const container = document.getElementById(containerId);
        if (!container) return;
        let html = '';
        Object.entries(allPowerups).forEach(([id, item]) => {
            const count = inventory[id] || 0;
            const hasAny = count > 0;
            const lockOverlay = !hasAny ? `
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.55);border-radius:15px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;">
                    <span style="font-size:1.6rem;">🔒</span>
                    <span style="color:rgba(255,255,255,0.8);font-size:0.7rem;font-weight:bold;">🪙 ${item.price}</span>
                </div>` : '';
            html += `
                <div style="position:relative;background:rgba(255,255,255,0.08);border-radius:15px;padding:18px 14px;text-align:center;border:2px solid ${hasAny ? 'rgba(255,215,0,0.4)' : 'rgba(255,255,255,0.1)'};transition:transform 0.3s;">
                    <div style="font-size:2.4rem;margin-bottom:8px;">${item.icon}</div>
                    <div style="font-weight:bold;font-size:0.95rem;color:#ffd700;margin-bottom:4px;">${item.name}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.6);margin-bottom:6px;">${item.desc}</div>
                    ${hasAny ? `<div style="color:#ffd700;font-weight:bold;font-size:0.9rem;">x${count} owned</div>` : `<a href="shop.php" style="padding:6px 16px;border:none;border-radius:20px;background:linear-gradient(135deg,#ffd700,#c9920a);color:#1a1a2e;cursor:pointer;font-weight:bold;text-decoration:none;display:inline-block;font-size:0.85rem;">Buy in Shop</a>`}
                    ${lockOverlay}
                </div>`;
        });
        container.innerHTML = html;
    }

    renderBadgesGrid(containerId, badges, unlockedBadges) {
        const container = document.getElementById(containerId);
        if (!container) return;
        let html = '';
        Object.entries(badges).forEach(([id, badge]) => {
            const isUnlocked = unlockedBadges.includes(id);
            const lockOverlay = !isUnlocked ? `
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.55);border-radius:15px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;">
                    <span style="font-size:1.6rem;">🔒</span>
                    <span style="color:rgba(255,255,255,0.8);font-size:0.7rem;font-weight:bold;text-align:center;padding:0 8px;">${badge.howTo}</span>
                </div>` : '';
            html += `
                <div style="position:relative;background:rgba(255,255,255,0.08);border-radius:15px;padding:18px 14px;text-align:center;border:2px solid ${isUnlocked ? 'rgba(255,215,0,0.4)' : 'rgba(255,255,255,0.1)'};">
                    <div style="font-size:2.4rem;margin-bottom:8px;">${badge.icon}</div>
                    <div style="font-weight:bold;font-size:0.95rem;color:#ffd700;margin-bottom:4px;">${badge.name}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.6);">${badge.desc}</div>
                    ${lockOverlay}
                </div>`;
        });
        container.innerHTML = html;
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

        // Render all sections with locked/unlocked state
        this.renderItemGrid('avatarsList', this.allAvatars, data.unlockedAvatars, data.currentAvatar, 'avatars');
        this.renderItemGrid('framesList', this.allFrames, data.unlockedFrames, data.currentFrame || 'default', 'frames');
        this.renderItemGrid('themesList', this.allThemes, data.unlockedThemes, data.currentTheme, 'themes');
        this.renderBadgesGrid('badgesList', this.badges, data.unlockedBadges);
        this.renderPowerupsGrid('powerupsList', this.allPowerups, data.powerups);
    }

    init() {
        this.render();
        setInterval(() => { this.render(); }, 5000);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.profile = new ProfileManager();
});
</script>
</body>
</html>