// js/core/AchievementSystem.js
// Complete Achievement System for Math Quest

class AchievementSystem {
    constructor() {
        this.achievements = this.loadAchievements();
        this.init();
    }

    // Load achievements from localStorage
    loadAchievements() {
        const saved = localStorage.getItem('mathQuest_achievements');
        if (saved) {
            return JSON.parse(saved);
        }
        
        // Initialize all achievements
        return {
            // ========== TOPIC BADGES ==========
            'addition_ace': {
                id: 'addition_ace',
                name: '➕ Addition Ace',
                description: 'Complete all 45 addition levels',
                category: 'topic',
                mode: 'add',
                skill: 'beginner',
                requirement: { type: 'complete_all', count: 45 },
                reward: { coins: 1000, avatar: 'addition_ace' },
                unlocked: false,
                progress: 0,
                icon: '➕'
            },
            'subtraction_ace': {
                id: 'subtraction_ace',
                name: '➖ Subtraction Ace',
                description: 'Complete all 45 subtraction levels',
                category: 'topic',
                mode: 'subtract',
                skill: 'beginner',
                requirement: { type: 'complete_all', count: 45 },
                reward: { coins: 1000, avatar: 'subtraction_ace' },
                unlocked: false,
                progress: 0,
                icon: '➖'
            },
            'multiplication_ace': {
                id: 'multiplication_ace',
                name: '✖️ Multiplication Ace',
                description: 'Complete all 45 multiplication levels',
                category: 'topic',
                mode: 'multiply',
                skill: 'beginner',
                requirement: { type: 'complete_all', count: 45 },
                reward: { coins: 1000, avatar: 'multiplication_ace' },
                unlocked: false,
                progress: 0,
                icon: '✖️'
            },
            'division_master': {
                id: 'division_master',
                name: '➗ Division Master',
                description: 'Get 3 stars on all 45 division levels',
                category: 'topic',
                mode: 'div',
                skill: 'beginner',
                requirement: { type: 'perfect_all', count: 45 },
                reward: { coins: 1500, avatar: 'division_master' },
                unlocked: false,
                progress: 0,
                icon: '➗'
            },
            'algebra_expert': {
                id: 'algebra_expert',
                name: 'x Algebra Expert',
                description: 'Complete all 45 algebra levels',
                category: 'topic',
                mode: 'algebra',
                skill: 'expert',
                requirement: { type: 'complete_all', count: 45 },
                reward: { coins: 1500, avatar: 'algebra_expert' },
                unlocked: false,
                progress: 0,
                icon: 'x'
            },
            'geometry_master': {
                id: 'geometry_master',
                name: '📐 Geometry Master',
                description: 'Complete all area and perimeter levels',
                category: 'topic',
                mode: 'area',
                skill: 'expert',
                requirement: { type: 'complete_all', count: 90 },
                reward: { coins: 1500, avatar: 'geometry_master' },
                unlocked: false,
                progress: 0,
                icon: '📐'
            },
            'fraction_master': {
                id: 'fraction_master',
                name: '🥧 Fraction Master',
                description: 'Get 3 stars on all fraction levels',
                category: 'topic',
                mode: 'fractions',
                skill: 'advance',
                requirement: { type: 'perfect_all', count: 45 },
                reward: { coins: 1200, avatar: 'fraction_master' },
                unlocked: false,
                progress: 0,
                icon: '🥧'
            },
            'decimal_master': {
                id: 'decimal_master',
                name: '. Decimal Master',
                description: 'Get 3 stars on all decimal levels',
                category: 'topic',
                mode: 'decimal',
                skill: 'advance',
                requirement: { type: 'perfect_all', count: 45 },
                reward: { coins: 1200, avatar: 'decimal_master' },
                unlocked: false,
                progress: 0,
                icon: '.'
            },
            'percentage_pro': {
                id: 'percentage_pro',
                name: '% Percentage Pro',
                description: 'Complete all percentage levels',
                category: 'topic',
                mode: 'percentage',
                skill: 'expert',
                requirement: { type: 'complete_all', count: 45 },
                reward: { coins: 1200, avatar: 'percentage_pro' },
                unlocked: false,
                progress: 0,
                icon: '%'
            },
            'trigonometry_guru': {
                id: 'trigonometry_guru',
                name: '📐 Trigonometry Guru',
                description: 'Complete all trigonometry levels',
                category: 'topic',
                mode: 'trigonometry',
                skill: 'grand-master',
                requirement: { type: 'complete_all', count: 45 },
                reward: { coins: 2000, avatar: 'trig_guru' },
                unlocked: false,
                progress: 0,
                icon: '📐'
            },
            'pythagoras_legend': {
                id: 'pythagoras_legend',
                name: '🔺 Pythagoras Legend',
                description: 'Get 3 stars on all Pythagorean theorem levels',
                category: 'topic',
                mode: 'pythagorean-theorem',
                skill: 'grand-master',
                requirement: { type: 'perfect_all', count: 45 },
                reward: { coins: 2000, avatar: 'pythagoras_legend' },
                unlocked: false,
                progress: 0,
                icon: '🔺'
            },
            'logarithm_wizard': {
                id: 'logarithm_wizard',
                name: '📈 Logarithm Wizard',
                description: 'Complete all logarithm levels',
                category: 'topic',
                mode: 'logarithms',
                skill: 'grand-master',
                requirement: { type: 'complete_all', count: 45 },
                reward: { coins: 2000, avatar: 'log_wizard' },
                unlocked: false,
                progress: 0,
                icon: '📈'
            },
            
            // ========== ACCURACY AWARDS ==========
            'perfect_score': {
                id: 'perfect_score',
                name: '💯 Perfect Score!',
                description: 'Get a perfect score on any level (all answers correct)',
                category: 'accuracy',
                requirement: { type: 'perfect_level', count: 1 },
                reward: { coins: 100 },
                unlocked: false,
                progress: 0,
                icon: '💯'
            },
            'no_mistakes': {
                id: 'no_mistakes',
                name: '✨ No Mistakes',
                description: 'Complete a level with 0 wrong answers',
                category: 'accuracy',
                requirement: { type: 'no_mistakes', count: 1 },
                reward: { coins: 150 },
                unlocked: false,
                progress: 0,
                icon: '✨'
            },
            'flawless_run_10': {
                id: 'flawless_run_10',
                name: '🏃 Flawless Run',
                description: 'Complete 10 levels with no wrong answers',
                category: 'accuracy',
                requirement: { type: 'no_mistakes_streak', count: 10 },
                reward: { coins: 500, avatar: 'flawless_runner' },
                unlocked: false,
                progress: 0,
                icon: '🏃'
            },
            'flawless_run_25': {
                id: 'flawless_run_25',
                name: '⚡ Flawless Champion',
                description: 'Complete 25 levels with no wrong answers',
                category: 'accuracy',
                requirement: { type: 'no_mistakes_streak', count: 25 },
                reward: { coins: 1000, avatar: 'flawless_champion' },
                unlocked: false,
                progress: 0,
                icon: '⚡'
            },
            'flawless_run_50': {
                id: 'flawless_run_50',
                name: '👑 Flawless Legend',
                description: 'Complete 50 levels with no wrong answers',
                category: 'accuracy',
                requirement: { type: 'no_mistakes_streak', count: 50 },
                reward: { coins: 2500, avatar: 'flawless_legend' },
                unlocked: false,
                progress: 0,
                icon: '👑'
            },
            
            // ========== SPEED ACHIEVEMENTS ==========
            'lightning_fast': {
                id: 'lightning_fast',
                name: '⚡ Lightning Fast',
                description: 'Answer a question in under 2 seconds',
                category: 'speed',
                requirement: { type: 'fast_answer', time: 2 },
                reward: { coins: 200 },
                unlocked: false,
                progress: 0,
                icon: '⚡'
            },
            'quick_thinker': {
                id: 'quick_thinker',
                name: '🧠 Quick Thinker',
                description: 'Complete a level in half the time limit',
                category: 'speed',
                requirement: { type: 'half_time_level', count: 1 },
                reward: { coins: 300 },
                unlocked: false,
                progress: 0,
                icon: '🧠'
            },
            'speed_demon_5': {
                id: 'speed_demon_5',
                name: '🏎️ Speed Demon',
                description: 'Complete 5 levels in under half the time',
                category: 'speed',
                requirement: { type: 'half_time_levels', count: 5 },
                reward: { coins: 800, avatar: 'speed_demon' },
                unlocked: false,
                progress: 0,
                icon: '🏎️'
            },
            'speed_demon_20': {
                id: 'speed_demon_20',
                name: '🚀 Speed Master',
                description: 'Complete 20 levels in under half the time',
                category: 'speed',
                requirement: { type: 'half_time_levels', count: 20 },
                reward: { coins: 1500, avatar: 'speed_master' },
                unlocked: false,
                progress: 0,
                icon: '🚀'
            },
            'speed_demon_50': {
                id: 'speed_demon_50',
                name: '⚡ Speed Legend',
                description: 'Complete 50 levels in under half the time',
                category: 'speed',
                requirement: { type: 'half_time_levels', count: 50 },
                reward: { coins: 3000, avatar: 'speed_legend' },
                unlocked: false,
                progress: 0,
                icon: '⚡'
            },
            
            // ========== MILESTONE ACHIEVEMENTS ==========
            'level_50': {
                id: 'level_50',
                name: '🌟 50 Levels',
                description: 'Complete 50 levels across all modes',
                category: 'milestone',
                requirement: { type: 'total_levels', count: 50 },
                reward: { coins: 500 },
                unlocked: false,
                progress: 0,
                icon: '🌟'
            },
            'level_100': {
                id: 'level_100',
                name: '🎯 100 Levels',
                description: 'Complete 100 levels across all modes',
                category: 'milestone',
                requirement: { type: 'total_levels', count: 100 },
                reward: { coins: 1000 },
                unlocked: false,
                progress: 0,
                icon: '🎯'
            },
            'level_250': {
                id: 'level_250',
                name: '🏆 250 Levels',
                description: 'Complete 250 levels across all modes',
                category: 'milestone',
                requirement: { type: 'total_levels', count: 250 },
                reward: { coins: 2500, avatar: 'legend' },
                unlocked: false,
                progress: 0,
                icon: '🏆'
            },
            'level_500': {
                id: 'level_500',
                name: '👑 500 Levels',
                description: 'Complete 500 levels across all modes',
                category: 'milestone',
                requirement: { type: 'total_levels', count: 500 },
                reward: { coins: 5000, avatar: 'grand_master' },
                unlocked: false,
                progress: 0,
                icon: '👑'
            },
            'perfect_10': {
                id: 'perfect_10',
                name: '⭐ 10 Perfect Levels',
                description: 'Get 3 stars on 10 levels',
                category: 'milestone',
                requirement: { type: 'total_perfect', count: 10 },
                reward: { coins: 300 },
                unlocked: false,
                progress: 0,
                icon: '⭐'
            },
            'perfect_50': {
                id: 'perfect_50',
                name: '💎 50 Perfect Levels',
                description: 'Get 3 stars on 50 levels',
                category: 'milestone',
                requirement: { type: 'total_perfect', count: 50 },
                reward: { coins: 1500, avatar: 'perfect_master' },
                unlocked: false,
                progress: 0,
                icon: '💎'
            },
            'perfect_100': {
                id: 'perfect_100',
                name: '👑 100 Perfect Levels',
                description: 'Get 3 stars on 100 levels',
                category: 'milestone',
                requirement: { type: 'total_perfect', count: 100 },
                reward: { coins: 3000, avatar: 'perfect_legend' },
                unlocked: false,
                progress: 0,
                icon: '👑'
            },
            
            // ========== COIN ACHIEVEMENTS ==========
            'coin_1000': {
                id: 'coin_1000',
                name: '🪙 Coin Collector',
                description: 'Earn 1,000 total coins',
                category: 'coins',
                requirement: { type: 'total_coins', count: 1000 },
                reward: { coins: 200 },
                unlocked: false,
                progress: 0,
                icon: '🪙'
            },
            'coin_10000': {
                id: 'coin_10000',
                name: '💰 Coin Hoarder',
                description: 'Earn 10,000 total coins',
                category: 'coins',
                requirement: { type: 'total_coins', count: 10000 },
                reward: { coins: 1000, avatar: 'coin_master' },
                unlocked: false,
                progress: 0,
                icon: '💰'
            },
            'coin_50000': {
                id: 'coin_50000',
                name: '💎 Coin Tycoon',
                description: 'Earn 50,000 total coins',
                category: 'coins',
                requirement: { type: 'total_coins', count: 50000 },
                reward: { coins: 5000, avatar: 'coin_tycoon' },
                unlocked: false,
                progress: 0,
                icon: '💎'
            }
        };
    }

    saveAchievements() {
        localStorage.setItem('mathQuest_achievements', JSON.stringify(this.achievements));
    }

    // Check and unlock achievements
    checkAchievement(type, data) {
        let unlockedAny = false;
        
        for (const [id, achievement] of Object.entries(this.achievements)) {
            if (achievement.unlocked) continue;
            
            let shouldUnlock = false;
            
            switch (achievement.requirement.type) {
                case 'complete_all':
                    if (data.mode === achievement.mode && data.skill === achievement.skill) {
                        const completed = this.getLevelsCompleted(achievement.mode, achievement.skill);
                        achievement.progress = completed;
                        if (completed >= achievement.requirement.count) {
                            shouldUnlock = true;
                        }
                    }
                    break;
                    
                case 'perfect_all':
                    if (data.mode === achievement.mode && data.skill === achievement.skill) {
                        const perfect = this.getPerfectLevelsCount(achievement.mode, achievement.skill);
                        achievement.progress = perfect;
                        if (perfect >= achievement.requirement.count) {
                            shouldUnlock = true;
                        }
                    }
                    break;
                    
                case 'perfect_level':
                    if (data.perfectLevel) {
                        shouldUnlock = true;
                    }
                    break;
                    
                case 'no_mistakes':
                    if (data.noMistakes) {
                        shouldUnlock = true;
                    }
                    break;
                    
                case 'no_mistakes_streak':
                    const streak = this.getNoMistakesStreak();
                    achievement.progress = streak;
                    if (streak >= achievement.requirement.count) {
                        shouldUnlock = true;
                    }
                    break;
                    
                case 'fast_answer':
                    if (data.fastAnswer && data.fastAnswer <= achievement.requirement.time) {
                        shouldUnlock = true;
                    }
                    break;
                    
                case 'half_time_level':
                    if (data.halfTimeLevel) {
                        shouldUnlock = true;
                    }
                    break;
                    
                case 'half_time_levels':
                    const halfTimeCount = this.getHalfTimeLevelsCount();
                    achievement.progress = halfTimeCount;
                    if (halfTimeCount >= achievement.requirement.count) {
                        shouldUnlock = true;
                    }
                    break;
                    
                case 'total_levels':
                    const totalLevels = this.getTotalLevelsCompleted();
                    achievement.progress = totalLevels;
                    if (totalLevels >= achievement.requirement.count) {
                        shouldUnlock = true;
                    }
                    break;
                    
                case 'total_perfect':
                    const totalPerfect = this.getTotalPerfectLevels();
                    achievement.progress = totalPerfect;
                    if (totalPerfect >= achievement.requirement.count) {
                        shouldUnlock = true;
                    }
                    break;
                    
                case 'total_coins':
                    const totalCoins = this.getTotalCoinsEarned();
                    achievement.progress = totalCoins;
                    if (totalCoins >= achievement.requirement.count) {
                        shouldUnlock = true;
                    }
                    break;
            }
            
            if (shouldUnlock) {
                this.unlockAchievement(achievement);
                unlockedAny = true;
            }
        }
        
        return unlockedAny;
    }
    
    unlockAchievement(achievement) {
        achievement.unlocked = true;
        achievement.unlockedAt = new Date().toISOString();
        this.saveAchievements();
        
        // Give reward
        if (achievement.reward.coins) {
            let currentCoins = parseInt(localStorage.getItem('mathQuest_coins') || '0', 10);
            currentCoins += achievement.reward.coins;
            localStorage.setItem('mathQuest_coins', currentCoins);
            
            // Show coin animation
            this.showCoinAnimation(achievement.reward.coins);
        }
        
        if (achievement.reward.avatar) {
            // Unlock special avatar
            let unlockedAvatars = JSON.parse(localStorage.getItem('mathQuest_unlockedAvatars') || '["default"]');
            if (!unlockedAvatars.includes(achievement.reward.avatar)) {
                unlockedAvatars.push(achievement.reward.avatar);
                localStorage.setItem('mathQuest_unlockedAvatars', JSON.stringify(unlockedAvatars));
            }
        }
        
        // Show notification
        this.showAchievementNotification(achievement);
        
        // Play achievement sound
        this.playAchievementSound();
    }
    
    showAchievementNotification(achievement) {
        const notification = document.createElement('div');
        notification.className = 'achievement-notification';
        notification.innerHTML = `
            <div class="achievement-icon">${achievement.icon}</div>
            <div class="achievement-content">
                <div class="achievement-title">🏆 ACHIEVEMENT UNLOCKED!</div>
                <div class="achievement-name">${achievement.name}</div>
                <div class="achievement-reward">+${achievement.reward.coins} 🪙</div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }
    
    showCoinAnimation(amount) {
        const coinAnim = document.createElement('div');
        coinAnim.className = 'coin-float';
        coinAnim.innerHTML = `+${amount} 🪙`;
        coinAnim.style.left = '50%';
        coinAnim.style.top = '50%';
        coinAnim.style.position = 'fixed';
        document.body.appendChild(coinAnim);
        setTimeout(() => coinAnim.remove(), 1000);
    }
    
    playAchievementSound() {
        try {
            const audio = new Audio('/math-game/sounds/achievement.mp3');
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Sound not loaded'));
        } catch(e) {}
    }
    
    // Helper methods to track progress
    getLevelsCompleted(mode, skill) {
        let completed = 0;
        for (let level = 1; level <= 45; level++) {
            const stars = localStorage.getItem(`mathQuest_${skill}_${mode}_level_${level}_stars`);
            if (stars && parseInt(stars) > 0) {
                completed++;
            }
        }
        return completed;
    }
    
    getPerfectLevelsCount(mode, skill) {
        let perfect = 0;
        for (let level = 1; level <= 45; level++) {
            const stars = localStorage.getItem(`mathQuest_${skill}_${mode}_level_${level}_stars`);
            if (stars && parseInt(stars) === 3) {
                perfect++;
            }
        }
        return perfect;
    }
    
    getTotalLevelsCompleted() {
        let total = 0;
        const skills = ['beginner', 'advance', 'expert', 'grand-master'];
        const modes = {
            'beginner': ['add', 'subtract', 'multiply', 'div'],
            'advance': ['decimal', 'fractions', 'perimeter', 'rounding'],
            'expert': ['algebra', 'area', 'factorization', 'percentage', 'simplifying-expressions'],
            'grand-master': ['logarithms', 'pythagorean-theorem', 'trigonometry']
        };
        
        skills.forEach(skill => {
            modes[skill].forEach(mode => {
                for (let level = 1; level <= 45; level++) {
                    const stars = localStorage.getItem(`mathQuest_${skill}_${mode}_level_${level}_stars`);
                    if (stars && parseInt(stars) > 0) {
                        total++;
                    }
                }
            });
        });
        
        return total;
    }
    
    getTotalPerfectLevels() {
        let total = 0;
        const skills = ['beginner', 'advance', 'expert', 'grand-master'];
        const modes = {
            'beginner': ['add', 'subtract', 'multiply', 'div'],
            'advance': ['decimal', 'fractions', 'perimeter', 'rounding'],
            'expert': ['algebra', 'area', 'factorization', 'percentage', 'simplifying-expressions'],
            'grand-master': ['logarithms', 'pythagorean-theorem', 'trigonometry']
        };
        
        skills.forEach(skill => {
            modes[skill].forEach(mode => {
                for (let level = 1; level <= 45; level++) {
                    const stars = localStorage.getItem(`mathQuest_${skill}_${mode}_level_${level}_stars`);
                    if (stars && parseInt(stars) === 3) {
                        total++;
                    }
                }
            });
        });
        
        return total;
    }
    
    getTotalCoinsEarned() {
        return parseInt(localStorage.getItem('mathQuest_coins') || '0', 10);
    }
    
    getNoMistakesStreak() {
        return parseInt(localStorage.getItem('mathQuest_noMistakesStreak') || '0', 10);
    }
    
    getHalfTimeLevelsCount() {
        return parseInt(localStorage.getItem('mathQuest_halfTimeLevels') || '0', 10);
    }
    
    updateNoMistakesStreak(noMistakes) {
        let streak = parseInt(localStorage.getItem('mathQuest_noMistakesStreak') || '0', 10);
        if (noMistakes) {
            streak++;
            localStorage.setItem('mathQuest_noMistakesStreak', streak);
        } else {
            streak = 0;
            localStorage.setItem('mathQuest_noMistakesStreak', 0);
        }
        return streak;
    }
    
    updateHalfTimeLevels(halfTime) {
        let count = parseInt(localStorage.getItem('mathQuest_halfTimeLevels') || '0', 10);
        if (halfTime) {
            count++;
            localStorage.setItem('mathQuest_halfTimeLevels', count);
        }
        return count;
    }
    
    getUnlockedAchievements() {
        return Object.values(this.achievements).filter(a => a.unlocked);
    }
    
    getAchievementsByCategory(category) {
        return Object.values(this.achievements).filter(a => a.category === category);
    }
    
    renderAchievementsPage() {
        const categories = ['topic', 'accuracy', 'speed', 'milestone', 'coins'];
        let html = '';
        
        categories.forEach(category => {
            const achievements = this.getAchievementsByCategory(category);
            if (achievements.length === 0) return;
            
            const categoryNames = {
                'topic': '📚 Topic Badges',
                'accuracy': '🎯 Accuracy Awards',
                'speed': '⚡ Speed Achievements',
                'milestone': '🏆 Milestone Achievements',
                'coins': '🪙 Coin Achievements'
            };
            
            html += `<h3 style="color: #ffd700; margin-top: 20px;">${categoryNames[category]}</h3>`;
            html += `<div class="achievements-grid">`;
            
            achievements.forEach(ach => {
                const progressPercent = (ach.progress / ach.requirement.count) * 100;
                html += `
                    <div class="achievement-card ${ach.unlocked ? 'unlocked' : 'locked'}">
                        <div class="achievement-header">
                            <div class="achievement-badge">${ach.icon}</div>
                            <div class="achievement-info">
                                <div class="achievement-name">${ach.name}</div>
                                <div class="achievement-category">${categoryNames[category]}</div>
                            </div>
                        </div>
                        <div class="achievement-desc">${ach.description}</div>
                        ${!ach.unlocked ? `
                            <div class="achievement-progress">
                                <div class="progress-bar-achievement">
                                    <div class="progress-fill" style="width: ${progressPercent}%"></div>
                                </div>
                                <div class="progress-text">${ach.progress}/${ach.requirement.count}</div>
                            </div>
                        ` : ''}
                        <div class="achievement-reward-badge">+${ach.reward.coins} 🪙</div>
                    </div>
                `;
            });
            
            html += `</div>`;
        });
        
        return html;
    }
    
    init() {
        console.log('Achievement System Loaded');
    }
}

// Initialize achievement system
window.achievementSystem = new AchievementSystem();