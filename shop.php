<?php
// shop.php - Math Quest Shop Page
require_once 'config.php';
requireLogin();  // This MUST be at the very top
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest - Shop</title>
    <link rel="stylesheet" href="style.css?v=2">
    <style>
        .shop-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .coin-wallet {
            background: linear-gradient(135deg, #ffd700, #c9920a);
            display: inline-block;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: bold;
            color: #1a1a2e;
            margin-top: 15px;
        }
        .info-message {
            background: rgba(88, 196, 245, 0.2);
            border: 1px solid #58c4f5;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            margin-bottom: 25px;
            color: #58c4f5;
        }
        .shop-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .shop-tab {
            padding: 10px 25px;
            background: rgba(255,255,255,0.1);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        }
        .shop-tab.active {
            background: linear-gradient(135deg, #ffd700, #c9920a);
            color: #1a1a2e;
        }
        .shop-tab:hover {
            transform: translateY(-2px);
        }
        .shop-section {
            display: none;
        }
        .shop-section.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .shop-item {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
            border: 1px solid rgba(255,215,0,0.2);
        }
        .shop-item:hover {
            transform: translateY(-5px);
            border-color: #ffd700;
        }
        .shop-item.equipped {
            border: 2px solid #ffd700;
            background: rgba(255,215,0,0.1);
        }
        .item-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .item-name {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: #ffd700;
        }
        .item-desc {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
            margin-bottom: 10px;
        }
        .item-price {
            font-weight: bold;
            margin-bottom: 15px;
            color: #ffd700;
        }
        .btn-shop {
            padding: 8px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.2s;
        }
        .btn-buy {
            background: linear-gradient(135deg, #ffd700, #c9920a);
            color: #1a1a2e;
        }
        .btn-equip {
            background: linear-gradient(135deg, #58c4f5, #1e90ff);
            color: white;
        }
        .btn-owned {
            background: rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.6);
            cursor: not-allowed;
        }
        .message {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 10px;
            display: none;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        .message.success {
            background: rgba(0,255,0,0.2);
            border: 1px solid #0f0;
            color: #0f0;
        }
        .message.error {
            background: rgba(255,0,0,0.2);
            border: 1px solid #f00;
            color: #f00;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { transform: translateX(100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="card" style="max-width: 1200px; margin: 20px auto;">
    <div class="shop-header">
        <h1>🛒 Math Quest Shop</h1>
        <p>Spend your coins on awesome items!</p>
        <div class="coin-wallet">
            💰 Your Coins: <span id="shopCoinTotal">0</span> 🪙
        </div>
    </div>
    
    <!-- Info Message -->
    <div class="info-message">
        🎖️ <strong>Badges & Themes are earned through achievements!</strong> 🎨<br>
        Complete special challenges, get perfect scores, or win them in the Lucky Spin Wheel!
    </div>
    
    <!-- Shop Tabs (Only Avatars, Frames, Power-ups) -->
    <div class="shop-tabs">
        <div class="shop-tab active" data-tab="avatars">👤 Avatars</div>
        <div class="shop-tab" data-tab="frames">🖼️ Frames</div>
        <div class="shop-tab" data-tab="powerups">⚡ Power-ups</div>
    </div>
    
    <!-- Avatars Section -->
    <div id="avatarsSection" class="shop-section active">
        <div class="items-grid" id="avatarsGrid"></div>
    </div>
    
    <!-- Frames Section -->
    <div id="framesSection" class="shop-section">
        <div class="items-grid" id="framesGrid"></div>
    </div>
    
    <!-- Power-ups Section -->
    <div id="powerupsSection" class="shop-section">
        <div class="items-grid" id="powerupsGrid"></div>
    </div>
</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<div id="message" class="message"></div>

<script>
// Shop System (Only Avatars, Frames, Power-ups)
class MathQuestShop {
    constructor() {
        this.items = {
            avatars: {
                'wizard': { name: '🧙 Wizard', price: 3000, icon: '🧙', desc: 'Become a magical math wizard!' },
                'knight': { name: '⚔️ Knight', price: 2500, icon: '⚔️', desc: 'Brave and strong math warrior!' },
                'ninja': { name: '🥷 Ninja', price: 2800, icon: '🥷', desc: 'Swift and silent math master!' },
                'pirate': { name: '🏴‍☠️ Pirate', price: 2600, icon: '🏴‍☠️', desc: 'Adventurous treasure hunter!' },
                'robot': { name: '🤖 Robot', price: 3200, icon: '🤖', desc: 'High-tech math machine!' },
                'dragon': { name: '🐉 Dragon', price: 5000, icon: '🐉', desc: 'Legendary fire breather!' },
                'phoenix': { name: '🔥 Phoenix', price: 4500, icon: '🔥', desc: 'Rise from the ashes!' },
                'unicorn': { name: '🦄 Unicorn', price: 4000, icon: '🦄', desc: 'Magical and pure!' },
                'viking': { name: '⚡ Viking', price: 3500, icon: '⚡', desc: 'Fearsome Norse warrior!' }
            },
            frames: {
                'gold': { name: '👑 Gold Frame', price: 1500, icon: '👑', desc: 'Royal golden border' },
                'silver': { name: '🥈 Silver Frame', price: 1000, icon: '🥈', desc: 'Shiny silver border' },
                'diamond': { name: '💎 Diamond Frame', price: 2500, icon: '💎', desc: 'Sparkling diamond border' },
                'ruby': { name: '🔴 Ruby Frame', price: 2000, icon: '🔴', desc: 'Precious ruby border' }
            },
            powerups: {
                'shield': { name: '🛡️ Shield', price: 500, icon: '🛡️', desc: 'Protects from one wrong answer', type: 'consumable' },
                'freeze': { name: '⏸️ Freeze Timer', price: 200, icon: '⏸️', desc: 'Pauses timer for 10 seconds', type: 'consumable' },
                'skip': { name: '⏭️ Skip Question', price: 150, icon: '⏭️', desc: 'Skip a question without penalty', type: 'consumable' },
                'retry_stars': { name: '🔄 Retry Keep Stars', price: 800, icon: '🔄', desc: 'Retry level without losing stars', type: 'consumable' }
            }
        };
        
        this.init();
    }
    
    loadData() {
        return {
            coins: parseInt(localStorage.getItem('mathQuest_coins') || '0', 10),
            currentAvatar: localStorage.getItem('mathQuest_avatar') || 'default',
            currentFrame: localStorage.getItem('mathQuest_frame') || null,
            unlockedAvatars: JSON.parse(localStorage.getItem('mathQuest_unlockedAvatars') || '["default"]'),
            unlockedFrames: JSON.parse(localStorage.getItem('mathQuest_unlockedFrames') || '["default"]')
        };
    }
    
    saveData(data) {
        localStorage.setItem('mathQuest_coins', data.coins);
        localStorage.setItem('mathQuest_avatar', data.currentAvatar);
        localStorage.setItem('mathQuest_frame', data.currentFrame);
        localStorage.setItem('mathQuest_unlockedAvatars', JSON.stringify(data.unlockedAvatars));
        localStorage.setItem('mathQuest_unlockedFrames', JSON.stringify(data.unlockedFrames));
        
        const navCoin = document.getElementById('coinCountNav');
        if (navCoin) navCoin.textContent = data.coins.toLocaleString();
    }
    
    showMessage(text, isError = false) {
        const msgDiv = document.getElementById('message');
        msgDiv.textContent = text;
        msgDiv.className = `message ${isError ? 'error' : 'success'}`;
        msgDiv.style.display = 'block';
        setTimeout(() => {
            msgDiv.style.display = 'none';
        }, 3000);
    }
    
    purchase(type, id, price) {
        const data = this.loadData();
        const unlockedKey = `unlocked${type.charAt(0).toUpperCase() + type.slice(1)}`;
        
        if (data[unlockedKey].includes(id)) {
            this.showMessage(`✨ You already own ${this.items[type][id].name}!`, false);
            return false;
        }
        
        if (data.coins < price) {
            this.showMessage(`💰 Not enough coins! Need ${price.toLocaleString()} coins.`, true);
            return false;
        }
        
        data.coins -= price;
        data[unlockedKey].push(id);
        
        if (type === 'avatars') data.currentAvatar = id;
        if (type === 'frames') data.currentFrame = id;
        
        this.saveData(data);
        this.showMessage(`🎉 Purchased ${this.items[type][id].name}!`, false);
        this.render();
        return true;
    }
    
    equip(type, id) {
        const data = this.loadData();
        const unlockedKey = `unlocked${type.charAt(0).toUpperCase() + type.slice(1)}`;
        
        if (!data[unlockedKey].includes(id)) {
            this.showMessage(`🔒 You haven't unlocked this item yet!`, true);
            return false;
        }
        
        if (type === 'avatars') data.currentAvatar = id;
        if (type === 'frames') data.currentFrame = id;
        
        this.saveData(data);
        this.showMessage(`✨ Equipped ${this.items[type][id].name}!`, false);
        this.render();
        return true;
    }
    
    purchasePowerup(id, price) {
        const data = this.loadData();
        
        if (data.coins < price) {
            this.showMessage(`💰 Not enough coins! Need ${price.toLocaleString()} coins.`, true);
            return false;
        }
        
        data.coins -= price;
        let inventory = JSON.parse(localStorage.getItem('mathQuest_powerups') || '{}');
        inventory[id] = (inventory[id] || 0) + 1;
        localStorage.setItem('mathQuest_powerups', JSON.stringify(inventory));
        
        this.saveData(data);
        this.showMessage(`🎉 Purchased ${this.items.powerups[id].name}! Use it in-game.`, false);
        this.render();
        return true;
    }
    
    renderCategory(type, data) {
        const grid = document.getElementById(`${type}Grid`);
        if (!grid) return;
        
        grid.innerHTML = '';
        const unlockedKey = `unlocked${type.charAt(0).toUpperCase() + type.slice(1)}`;
        const currentKey = `current${type.charAt(0).toUpperCase() + type.slice(1)}`;
        
        Object.entries(this.items[type]).forEach(([id, item]) => {
            const isUnlocked = data[unlockedKey].includes(id);
            const isEquipped = data[currentKey] === id;
            
            const div = document.createElement('div');
            div.className = `shop-item ${isEquipped ? 'equipped' : ''}`;
            
            div.innerHTML = `
                <div class="item-icon">${item.icon}</div>
                <div class="item-name">${item.name}</div>
                <div class="item-desc">${item.desc}</div>
                <div class="item-price">🪙 ${item.price.toLocaleString()}</div>
                <div class="item-actions">
                    ${!isUnlocked ? 
                        `<button class="btn-shop btn-buy" onclick="shop.purchase('${type}', '${id}', ${item.price})">Buy</button>` :
                        (!isEquipped ? 
                            `<button class="btn-shop btn-equip" onclick="shop.equip('${type}', '${id}')">Equip</button>` :
                            `<button class="btn-shop btn-owned" disabled>Owned</button>`
                        )
                    }
                </div>
            `;
            grid.appendChild(div);
        });
    }
    
    renderPowerups(data) {
        const grid = document.getElementById('powerupsGrid');
        if (!grid) return;
        
        grid.innerHTML = '';
        Object.entries(this.items.powerups).forEach(([id, item]) => {
            const div = document.createElement('div');
            div.className = 'shop-item';
            div.innerHTML = `
                <div class="item-icon">${item.icon}</div>
                <div class="item-name">${item.name}</div>
                <div class="item-desc">${item.desc}</div>
                <div class="item-price">🪙 ${item.price.toLocaleString()}</div>
                <div class="item-actions">
                    <button class="btn-shop btn-buy" onclick="shop.purchasePowerup('${id}', ${item.price})">Buy</button>
                </div>
            `;
            grid.appendChild(div);
        });
    }
    
    render() {
        const data = this.loadData();
        document.getElementById('shopCoinTotal').textContent = data.coins.toLocaleString();
        this.renderCategory('avatars', data);
        this.renderCategory('frames', data);
        this.renderPowerups(data);
    }
    
    setupTabs() {
        document.querySelectorAll('.shop-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.shop-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.shop-section').forEach(s => s.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(`${tab.dataset.tab}Section`).classList.add('active');
            });
        });
    }
    
    init() {
        this.render();
        this.setupTabs();
        setInterval(() => {
            const data = this.loadData();
            document.getElementById('shopCoinTotal').textContent = data.coins.toLocaleString();
        }, 500);
    }
}

const shop = new MathQuestShop();
</script>

<?php include 'background-music.php'; ?>

</body>
</html>