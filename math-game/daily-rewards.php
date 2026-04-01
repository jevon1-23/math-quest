<?php
// daily-rewards.php - Daily Rewards & Spin Wheel Page
require_once 'config.php';
requireLogin();  // This forces login if not logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest - Daily Rewards</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="card" style="max-width: 900px; margin: 20px auto;">
    <div class="rewards-header">
        <h1>🎁 Daily Rewards</h1>
        <p>Login every day to earn amazing rewards!</p>
    </div>
    
    <!-- Streak Card -->
    <div class="streak-card">
        <p>🔥 Current Streak</p>
        <div class="streak-number" id="streakCount">0</div>
        <p>days in a row!</p>
    </div>
    
    <!-- 7-Day Rewards Grid -->
    <div class="rewards-grid" id="rewardsGrid"></div>
    
    <!-- Claim Button -->
    <div style="text-align: center;">
        <button class="claim-btn" id="claimDailyBtn" onclick="claimDailyReward()">🎁 Claim Today's Reward</button>
    </div>
    
    <!-- Spin Wheel Section -->
    <div class="wheel-container">
        <h2>🎡 Lucky Spin Wheel</h2>
        <div style="position: relative; width: 300px; height: 300px; margin: 20px auto;">
            <canvas id="wheelCanvas" width="300" height="300" style="border-radius: 50%; box-shadow: 0 8px 25px rgba(0,0,0,0.3);"></canvas>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px; background: #ffd700; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 2px 8px rgba(0,0,0,0.3); z-index: 10;">🎲</div>
            <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 15px solid transparent; border-right: 15px solid transparent; border-top: 30px solid #ff6b6b; z-index: 5;"></div>
        </div>
        
        <!-- Spin Info -->
        <div style="display: flex; justify-content: center; gap: 20px; margin: 15px 0;">
            <div style="background: rgba(0,0,0,0.5); padding: 8px 15px; border-radius: 20px;">
                <span style="color: #ffd700;">🎡 Free Spin: </span>
                <span id="freeSpinStatus" style="color: white; font-weight: bold;">Available</span>
            </div>
            <div style="background: rgba(0,0,0,0.5); padding: 8px 15px; border-radius: 20px;">
                <span style="color: #ffd700;">🪙 Extra Spin: </span>
                <span style="color: white; font-weight: bold;">500 Coins</span>
            </div>
        </div>
        
        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <button class="spin-btn" onclick="spinWheel(true)" id="freeSpinBtn">🎲 FREE SPIN</button>
            <button class="spin-btn" onclick="spinWheel(false)" id="paidSpinBtn" style="background: linear-gradient(135deg, #7b2fff, #4a0080);">🪙 BUY SPIN (500)</button>
        </div>
        
        <div id="spinResult" class="spin-result"></div>
        <p style="color: rgba(255,255,255,0.7); margin-top: 15px;">
            🎰 Win 500, 750, or 2,000 coins! 🎰<br>
            2,000 coins is the GRAND PRIZE! | Free spin daily | Extra spins: 500 coins
        </p>
    </div>
    
    <!-- Daily Challenges -->
    <div class="daily-challenges">
        <h2>📋 Daily Challenges</h2>
        <div id="dailyChallenges"></div>
    </div>
</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<script>
// Daily Rewards System
class DailyRewardsSystem {
    constructor() {
        this.rewards = [
            { day: 1, coins: 100, icon: '🌟', name: 'Starter Bonus' },
            { day: 2, coins: 150, icon: '⭐', name: 'Day 2 Reward' },
            { day: 3, coins: 200, icon: '🎁', name: 'Day 3 Gift' },
            { day: 4, coins: 250, icon: '💎', name: 'Day 4 Treasure' },
            { day: 5, coins: 300, icon: '🏆', name: 'Day 5 Prize' },
            { day: 6, coins: 400, icon: '💰', name: 'Day 6 Jackpot' },
            { day: 7, coins: 500, icon: '👑', name: 'Day 7 GRAND PRIZE!' }
        ];
        
        // Wheel rewards - only 500, 750, and 2000 coins
        this.wheelRewards = [
            { name: '500 Coins', coins: 500, icon: '🪙', color: '#ffd700', probability: 45 },
            { name: '750 Coins', coins: 750, icon: '🪙', color: '#ffaa00', probability: 35 },
            { name: '2,000 Coins', coins: 2000, icon: '👑', color: '#e74c3c', probability: 10 },
            { name: 'Ocean Theme', coins: 0, icon: '🌊', color: '#3498db', probability: 4, theme: 'ocean' },
            { name: 'Sunset Theme', coins: 0, icon: '🌅', color: '#e67e22', probability: 3, theme: 'sunset' },
            { name: 'Forest Theme', coins: 0, icon: '🌲', color: '#2ecc71', probability: 2, theme: 'forest' },
            { name: 'Shield', coins: 0, icon: '🛡️', color: '#8e44ad', probability: 1, powerup: 'shield' }
        ];
        
        this.loadData();
        this.render();
        this.drawWheel();
        this.updateSpinButtons();
    }
    
    loadData() {
        const saved = localStorage.getItem('mathQuest_dailyRewards');
        if (saved) {
            const data = JSON.parse(saved);
            this.lastClaimDate = data.lastClaimDate;
            this.streak = data.streak;
            this.claimedDays = data.claimedDays || [];
            this.lastFreeSpinDate = data.lastFreeSpinDate;
            this.dailyChallenges = data.dailyChallenges || this.generateDailyChallenges();
        } else {
            this.lastClaimDate = null;
            this.streak = 0;
            this.claimedDays = [];
            this.lastFreeSpinDate = null;
            this.dailyChallenges = this.generateDailyChallenges();
        }
    }
    
    saveData() {
        localStorage.setItem('mathQuest_dailyRewards', JSON.stringify({
            lastClaimDate: this.lastClaimDate,
            streak: this.streak,
            claimedDays: this.claimedDays,
            lastFreeSpinDate: this.lastFreeSpinDate,
            dailyChallenges: this.dailyChallenges
        }));
    }
    
    canFreeSpin() {
        if (!this.lastFreeSpinDate) return true;
        const today = new Date().toDateString();
        const last = new Date(this.lastFreeSpinDate).toDateString();
        return today !== last;
    }
    
    updateSpinButtons() {
        const canSpinFree = this.canFreeSpin();
        const freeBtn = document.getElementById('freeSpinBtn');
        const freeStatus = document.getElementById('freeSpinStatus');
        
        if (freeBtn) {
            if (canSpinFree) {
                freeBtn.style.opacity = '1';
                freeBtn.style.cursor = 'pointer';
                if (freeStatus) freeStatus.innerHTML = '✓ Available';
            } else {
                freeBtn.style.opacity = '0.5';
                freeBtn.style.cursor = 'not-allowed';
                if (freeStatus) freeStatus.innerHTML = 'Used Today';
            }
        }
    }
    
    generateDailyChallenges() {
        return [
            { id: 'complete_3_levels', name: '🎯 Complete 3 Levels', desc: 'Finish any 3 levels', target: 3, current: 0, reward: 150, completed: false },
            { id: 'perfect_1_level', name: '⭐ Perfect Score', desc: 'Get 3 stars on a level', target: 1, current: 0, reward: 200, completed: false },
            { id: 'answer_20_correct', name: '✓ 20 Correct Answers', desc: 'Answer 20 questions correctly', target: 20, current: 0, reward: 100, completed: false }
        ];
    }
    
    drawWheel() {
        const canvas = document.getElementById('wheelCanvas');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        const centerX = width / 2;
        const centerY = height / 2;
        const radius = width / 2;
        
        ctx.clearRect(0, 0, width, height);
        
        const total = this.wheelRewards.reduce((sum, r) => sum + r.probability, 0);
        let startAngle = 0;
        
        this.wheelRewards.forEach((reward) => {
            const angle = (reward.probability / total) * Math.PI * 2;
            const endAngle = startAngle + angle;
            
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, startAngle, endAngle);
            ctx.closePath();
            
            ctx.fillStyle = reward.color;
            ctx.fill();
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.stroke();
            
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate(startAngle + angle / 2);
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = 'bold 12px "Nunito", sans-serif';
            ctx.fillStyle = '#ffffff';
            ctx.shadowBlur = 0;
            
            const textRadius = radius * 0.65;
            let text = reward.name;
            if (reward.coins === 2000) {
                text = '2k';
            } else if (reward.coins === 750) {
                text = '750';
            } else if (reward.coins === 500) {
                text = '500';
            } else if (reward.theme) {
                text = reward.icon;
            } else if (reward.powerup) {
                text = reward.icon;
            }
            ctx.fillText(text, textRadius, 0);
            ctx.restore();
            
            startAngle = endAngle;
        });
    }
    
    canClaimDaily() {
        if (!this.lastClaimDate) return true;
        const today = new Date().toDateString();
        const last = new Date(this.lastClaimDate).toDateString();
        if (today !== last) {
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            if (last === yesterday.toDateString()) {
                this.streak = Math.min(this.streak + 1, 7);
            } else {
                this.streak = 1;
            }
            return true;
        }
        return false;
    }
    
    claimDailyReward() {
        if (!this.canClaimDaily()) {
            alert('🎁 You already claimed today\'s reward! Come back tomorrow!');
            return;
        }
        
        const today = new Date();
        this.lastClaimDate = today.toISOString();
        const dayIndex = Math.min(this.streak - 1, 6);
        const reward = this.rewards[dayIndex];
        
        let currentCoins = parseInt(localStorage.getItem('mathQuest_coins') || '0', 10);
        currentCoins += reward.coins;
        localStorage.setItem('mathQuest_coins', currentCoins);
        
        this.claimedDays.push(reward.day);
        this.saveData();
        
        this.showPopup(reward.coins, reward.icon, reward.name);
        
        this.render();
        this.updateNavCoins();
    }
    
    showPopup(amount, icon, name, message = null) {
        // Remove any existing popup
        const existingPopup = document.querySelector('.popup-overlay');
        if (existingPopup) existingPopup.remove();
        
        const popup = document.createElement('div');
        popup.className = 'popup-overlay';
        
        let title = '';
        let messageText = '';
        let amountText = '';
        
        if (amount > 0) {
            title = '🎉 YOU WON! 🎉';
            amountText = `+${amount.toLocaleString()} 🪙`;
            if (amount === 2000) {
                messageText = 'JACKPOT! Amazing luck! 🎰✨';
            } else {
                messageText = `You earned ${amount} coins! Keep spinning for more rewards!`;
            }
        } else if (name === 'Ocean Theme' || name === 'Sunset Theme' || name === 'Forest Theme') {
            title = '🎨 THEME UNLOCKED! 🎨';
            amountText = name;
            messageText = `You unlocked the ${name}! Go to your profile to equip it!`;
        } else if (name === 'Shield') {
            title = '🛡️ POWER-UP UNLOCKED! 🛡️';
            amountText = name;
            messageText = 'You got a Shield Power-up! Use it in-game to protect from wrong answers!';
        } else if (message) {
            title = '⚠️ Notice';
            amountText = '';
            messageText = message;
        } else {
            title = '🎁 Reward';
            amountText = name;
            messageText = 'Thanks for playing!';
        }
        
        popup.innerHTML = `
            <div class="popup-card">
                <div class="popup-icon">${icon}</div>
                <div class="popup-title">${title}</div>
                <div class="popup-amount">${amountText}</div>
                <div class="popup-message">${messageText}</div>
                <button class="popup-close-btn" onclick="this.closest('.popup-overlay').remove()">Awesome! 🎉</button>
            </div>
        `;
        
        document.body.appendChild(popup);
        
        // Auto close after 4 seconds
        setTimeout(() => {
            if (popup && popup.parentNode) popup.remove();
        }, 4000);
    }
    
    spinWheel(isFreeSpin) {
        if (isFreeSpin) {
            if (!this.canFreeSpin()) {
                this.showPopup(0, '🎡', 'No Free Spin', 'You already used your free spin today! Buy an extra spin for 500 coins!');
                return;
            }
        } else {
            // Paid spin - check coins
            let currentCoins = parseInt(localStorage.getItem('mathQuest_coins') || '0', 10);
            if (currentCoins < 500) {
                this.showPopup(0, '💰', 'Not Enough Coins', 'You need 500 coins for an extra spin. Complete levels to earn more coins!');
                return;
            }
            currentCoins -= 500;
            localStorage.setItem('mathQuest_coins', currentCoins);
            this.updateNavCoins();
        }
        
        const totalProb = this.wheelRewards.reduce((sum, r) => sum + r.probability, 0);
        let random = Math.random() * totalProb;
        let selected = null;
        let cumulative = 0;
        
        for (let i = 0; i < this.wheelRewards.length; i++) {
            cumulative += this.wheelRewards[i].probability;
            if (random < cumulative) {
                selected = this.wheelRewards[i];
                break;
            }
        }
        
        // Animate wheel spin
        const canvas = document.getElementById('wheelCanvas');
        let startTime = null;
        const spinDuration = 2000;
        let targetRotation = (Math.PI * 2 * 5) + (Math.random() * Math.PI * 2);
        
        const drawWheelWithRotation = (rotation) => {
            const ctx = canvas.getContext('2d');
            const width = canvas.width;
            const height = canvas.height;
            const centerX = width / 2;
            const centerY = height / 2;
            const radius = width / 2;
            
            ctx.clearRect(0, 0, width, height);
            
            const total = this.wheelRewards.reduce((sum, r) => sum + r.probability, 0);
            let startAngle = rotation;
            
            this.wheelRewards.forEach((reward) => {
                const angle = (reward.probability / total) * Math.PI * 2;
                const endAngle = startAngle + angle;
                
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.closePath();
                
                ctx.fillStyle = reward.color;
                ctx.fill();
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 2;
                ctx.stroke();
                
                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(startAngle + angle / 2);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = 'bold 12px "Nunito", sans-serif';
                ctx.fillStyle = '#ffffff';
                
                const textRadius = radius * 0.65;
                let text = reward.name;
                if (reward.coins === 2000) {
                    text = '2k';
                } else if (reward.coins === 750) {
                    text = '750';
                } else if (reward.coins === 500) {
                    text = '500';
                } else if (reward.theme) {
                    text = reward.icon;
                } else if (reward.powerup) {
                    text = reward.icon;
                }
                ctx.fillText(text, textRadius, 0);
                ctx.restore();
                
                startAngle = endAngle;
            });
        };
        
        const animateSpin = (timestamp) => {
            if (!startTime) startTime = timestamp;
            const elapsed = timestamp - startTime;
            const progress = Math.min(elapsed / spinDuration, 1);
            const easeOut = 1 - Math.pow(1 - progress, 3);
            
            const currentRotation = targetRotation * easeOut;
            drawWheelWithRotation(currentRotation);
            
            if (progress < 1) {
                requestAnimationFrame(animateSpin);
            } else {
                // Award prize
                let prizeAmount = 0;
                
                if (selected.coins > 0) {
                    let currentCoins = parseInt(localStorage.getItem('mathQuest_coins') || '0', 10);
                    currentCoins += selected.coins;
                    localStorage.setItem('mathQuest_coins', currentCoins);
                    prizeAmount = selected.coins;
                } else if (selected.theme) {
                    let unlockedThemes = JSON.parse(localStorage.getItem('mathQuest_unlockedThemes') || '["default"]');
                    if (!unlockedThemes.includes(selected.theme)) {
                        unlockedThemes.push(selected.theme);
                        localStorage.setItem('mathQuest_unlockedThemes', JSON.stringify(unlockedThemes));
                    } else {
                        // Already have theme, give coins instead
                        let currentCoins = parseInt(localStorage.getItem('mathQuest_coins') || '0', 10);
                        currentCoins += 250;
                        localStorage.setItem('mathQuest_coins', currentCoins);
                        prizeAmount = 250;
                        selected = { name: selected.name + ' (Already had - 250 coins)', coins: 250, icon: '🪙' };
                    }
                } else if (selected.powerup) {
                    let inventory = JSON.parse(localStorage.getItem('mathQuest_powerups') || '{}');
                    inventory[selected.powerup] = (inventory[selected.powerup] || 0) + 1;
                    localStorage.setItem('mathQuest_powerups', JSON.stringify(inventory));
                }
                
                if (isFreeSpin) {
                    this.lastFreeSpinDate = new Date().toISOString();
                }
                this.saveData();
                
                // Show popup with win
                this.showPopup(selected.coins || prizeAmount, selected.icon, selected.name);
                
                this.updateNavCoins();
                this.render();
                this.updateSpinButtons();
            }
        };
        
        requestAnimationFrame(animateSpin.bind(this));
    }
    
    updateNavCoins() {
        const coins = localStorage.getItem('mathQuest_coins') || '0';
        const navCoin = document.getElementById('coinCountNav');
        if (navCoin) navCoin.textContent = parseInt(coins).toLocaleString();
        const coinElements = document.querySelectorAll('.nav-coin-display span:last-child, .coin-display');
        coinElements.forEach(el => {
            if (el.textContent !== coins) {
                el.textContent = parseInt(coins).toLocaleString();
            }
        });
    }
    
    renderRewardsGrid() {
        const container = document.getElementById('rewardsGrid');
        if (!container) return;
        
        const canClaim = this.canClaimDaily();
        
        let html = '';
        for (let i = 0; i < 7; i++) {
            const reward = this.rewards[i];
            const isClaimed = this.claimedDays.includes(reward.day);
            const isAvailable = canClaim && !isClaimed && i < this.streak;
            const isFuture = i >= this.streak;
            
            let statusClass = '';
            if (isClaimed) statusClass = 'claimed';
            else if (isAvailable) statusClass = 'available';
            else if (isFuture) statusClass = 'future';
            
            html += `
                <div class="reward-day ${statusClass}">
                    <div class="reward-day-number">Day ${reward.day}</div>
                    <div class="reward-icon">${reward.icon}</div>
                    <div class="reward-amount">${reward.coins} 🪙</div>
                    <div style="font-size:0.7rem; color:white;">${reward.name}</div>
                </div>
            `;
        }
        container.innerHTML = html;
        const streakEl = document.getElementById('streakCount');
        if (streakEl) streakEl.innerHTML = this.streak;
    }
    
    renderChallenges() {
        const container = document.getElementById('dailyChallenges');
        if (!container) return;
        
        let html = '';
        
        this.dailyChallenges.forEach(challenge => {
            const progressPercent = (challenge.current / challenge.target) * 100;
            const isCompleted = challenge.completed;
            
            html += `
                <div class="challenge-item ${isCompleted ? 'completed' : ''}">
                    <div class="challenge-info">
                        <div class="challenge-name">${challenge.name}</div>
                        <div class="challenge-desc">${challenge.desc}</div>
                        <div class="challenge-progress">
                            <div class="challenge-progress-fill" style="width: ${progressPercent}%"></div>
                        </div>
                    </div>
                    <div class="challenge-reward">+${challenge.reward} 🪙</div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    render() {
        this.renderRewardsGrid();
        this.renderChallenges();
        this.drawWheel();
    }
}

// Initialize daily rewards system when page loads
let dailyRewards = null;

document.addEventListener('DOMContentLoaded', () => {
    dailyRewards = new DailyRewardsSystem();
});

function claimDailyReward() {
    if (dailyRewards) {
        dailyRewards.claimDailyReward();
    }
}

function spinWheel(isFree) {
    if (dailyRewards) {
        dailyRewards.spinWheel(isFree);
    }
}
</script>

<?php include 'background-music.php'; ?>

</body>
</html>