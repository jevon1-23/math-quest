<!-- nav.php - Navigation Bar with Avatar Preview + Coins Display -->
<nav class="navbar">
    <a href="index.php" class="logo-link">
        <div class="logo">🎮 Math Quest</div>
    </a>
    <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap; justify-content:flex-end;">
        
        <!-- Coin Display -->
        <div id="navCoinDisplay" style="
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,215,0,0.15);
            border: 2px solid rgba(255,215,0,0.4);
            border-radius: 50px;
            padding: 6px 16px;
            transition: all 0.3s;
        ">
            <span style="font-size:1.2rem;">🪙</span>
            <span id="coinCountNav" style="color:#ffd700; font-weight:700; font-size:0.95rem;">0</span>
        </div>

        <!-- Profile Avatar Button -->
        <div id="profileButton" style="
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,215,0,0.15);
            border: 2px solid rgba(255,215,0,0.4);
            border-radius: 50px;
            padding: 4px 12px 4px 4px;
            cursor: pointer;
            transition: all 0.3s;
        " onclick="window.location.href='profile.php'">
            <div id="navAvatarPreview" style="
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: linear-gradient(135deg, #ffd700, #ffed4e);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                transition: all 0.3s;
            ">🧑</div>
            <span style="color: white; font-weight: 600; font-size: 0.85rem;">Profile</span>
        </div>

        <ul class="nav-links">
            <li><a href="index.php">🏠 Home</a></li>
            <li><a href="shop.php">🛒 Shop</a></li>
            <li><a href="profile.php">👤 Profile</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="play.php">🎮 Play</a></li>
        </ul>
        
    </div>
</nav>

<script>
(function() {
    // Avatar emoji mapping
    const avatarEmojis = {
        'default': '🧑',
        'wizard': '🧙',
        'knight': '⚔️',
        'ninja': '🥷',
        'pirate': '🏴‍☠️',
        'robot': '🤖',
        'dragon': '🐉',
        'phoenix': '🔥',
        'unicorn': '🦄',
        'viking': '⚡'
    };
    
    // Frame styles mapping
    const frameStyles = {
        'gold': 'box-shadow: 0 0 0 2px #ffd700, 0 0 0 4px #fbbf24;',
        'silver': 'box-shadow: 0 0 0 2px #c0c0c0, 0 0 0 4px #a0a0a0;',
        'diamond': 'box-shadow: 0 0 0 2px #b9f2ff, 0 0 0 4px #7fffd4;',
        'ruby': 'box-shadow: 0 0 0 2px #ff4444, 0 0 0 4px #cc3333;'
    };
    
    // Update profile avatar preview in navigation
    function updateNavAvatar() {
        let currentAvatar = localStorage.getItem('mathQuest_avatar') || 'default';
        let currentFrame = localStorage.getItem('mathQuest_frame');
        
        const emoji = avatarEmojis[currentAvatar] || '🧑';
        const navPreview = document.getElementById('navAvatarPreview');
        if (navPreview) {
            navPreview.innerHTML = emoji;
            if (currentFrame && frameStyles[currentFrame]) {
                navPreview.style.boxShadow = frameStyles[currentFrame];
            } else {
                navPreview.style.boxShadow = 'none';
            }
        }
    }
    
    // Update coin display
    function updateNavCoins() {
        let coins = localStorage.getItem('mathQuest_coins') || '0';
        const navCoin = document.getElementById('coinCountNav');
        if (navCoin) navCoin.textContent = parseInt(coins).toLocaleString();
    }
    
    // Initialize both
    function initNav() {
        updateNavAvatar();
        updateNavCoins();
    }
    
    initNav();
    
    // Keep avatar in sync when page becomes visible
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            updateNavAvatar();
            updateNavCoins();
        }
    });
    
    // Keep both in sync across tabs
    window.addEventListener('storage', (e) => {
        if (e.key === 'mathQuest_avatar' || e.key === 'mathQuest_frame') {
            updateNavAvatar();
        }
        if (e.key === 'mathQuest_coins') {
            updateNavCoins();
        }
    });
    
    // Periodic refresh for coins (same-tab updates)
    setInterval(updateNavCoins, 2000);
})();
</script>