<!-- nav.php - Navigation Bar with Dynamic Avatar Preview -->
<nav class="navbar">
    <a href="index.php" class="logo-link">
        <div class="logo">🎮 Math Quest</div>
    </a>
    <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap; justify-content:flex-end;">
        <!-- Profile Avatar Button - Shows Current Avatar -->
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
        
        <!-- Coin Display -->
        <span id="navCoinBadge" style="
            font-family:'Fredoka One',cursive;
            font-size:0.95rem;
            color:#ffd700;
            background:rgba(255,215,0,0.12);
            border:1.5px solid rgba(255,215,0,0.35);
            border-radius:20px;
            padding:3px 14px;
            letter-spacing:0.3px;
            cursor:default;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        ">
            🪙 <span id="coinCountNav">0</span>
        </span>
        
        <ul class="nav-links">
            <li><a href="index.php">🏠 Home</a></li>
            <li><a href="shop.php">🛒 Shop</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="play.php">🎮 Play Game</a></li>
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
    
    // Load coins from server and sync with localStorage
    function loadCoinsFromServer() {
        fetch('/math-game/coins.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const serverCoins = data.coins;
                    const localCoins = parseInt(localStorage.getItem('mathQuest_coins') || '0', 10);
                    
                    // Update localStorage to match server
                    if (localCoins !== serverCoins) {
                        localStorage.setItem('mathQuest_coins', serverCoins);
                    }
                    
                    // Update display
                    updateNavCoinsDisplay(serverCoins);
                } else {
                    // Fallback to localStorage
                    updateNavCoinsFromLocal();
                }
            })
            .catch(err => {
                console.error('Error loading coins from server:', err);
                updateNavCoinsFromLocal();
            });
    }
    
    // Update coin display from localStorage
    function updateNavCoinsFromLocal() {
        let coins = localStorage.getItem('mathQuest_coins');
        if (!coins || coins === 'null' || coins === 'undefined') {
            coins = '0';
            localStorage.setItem('mathQuest_coins', '0');
        }
        coins = parseInt(coins, 10);
        updateNavCoinsDisplay(coins);
    }
    
    // Update the coin display element
    function updateNavCoinsDisplay(coins) {
        const coinElement = document.getElementById('coinCountNav');
        if (coinElement && coinElement.textContent !== coins.toLocaleString()) {
            coinElement.textContent = coins.toLocaleString();
            const badge = document.getElementById('navCoinBadge');
            if (badge) {
                badge.style.transform = 'scale(1.05)';
                setTimeout(() => badge.style.transform = 'scale(1)', 200);
            }
        }
    }
    
    // Update profile avatar preview in navigation
    function updateNavAvatar() {
        // Get current avatar from localStorage
        let currentAvatar = localStorage.getItem('mathQuest_avatar');
        let currentFrame = localStorage.getItem('mathQuest_frame');
        
        // Handle null/undefined values
        if (!currentAvatar || currentAvatar === 'null' || currentAvatar === 'undefined') {
            currentAvatar = 'default';
            localStorage.setItem('mathQuest_avatar', 'default');
        }
        
        if (!currentFrame || currentFrame === 'null' || currentFrame === 'undefined') {
            currentFrame = null;
        }
        
        // Get the emoji for the current avatar
        const emoji = avatarEmojis[currentAvatar] || '🧑';
        
        // Update the avatar preview
        const navPreview = document.getElementById('navAvatarPreview');
        if (navPreview) {
            navPreview.innerHTML = emoji;
            
            // Apply frame effect if equipped
            if (currentFrame && frameStyles[currentFrame]) {
                navPreview.style.boxShadow = frameStyles[currentFrame];
            } else {
                navPreview.style.boxShadow = 'none';
            }
            
            // Add a little animation to show update
            navPreview.style.transform = 'scale(1.1)';
            setTimeout(() => {
                navPreview.style.transform = 'scale(1)';
            }, 200);
        }
    }
    
    // Initialize everything
    function initNav() {
        updateNavAvatar();
        loadCoinsFromServer(); // Load from server first
    }
    
    // Run immediately
    initNav();
    
    // Update every 5 seconds to keep coins in sync
    setInterval(() => {
        loadCoinsFromServer();
    }, 5000);
    
    // Also update when page becomes visible (user returns from another tab)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            updateNavAvatar();
            loadCoinsFromServer();
        }
    });
    
    // Listen for storage events (when localStorage changes in another tab)
    window.addEventListener('storage', (e) => {
        if (e.key === 'mathQuest_avatar' || e.key === 'mathQuest_frame') {
            updateNavAvatar();
        }
        if (e.key === 'mathQuest_coins') {
            const newCoins = parseInt(e.newValue || '0', 10);
            updateNavCoinsDisplay(newCoins);
        }
    });
})();
</script>