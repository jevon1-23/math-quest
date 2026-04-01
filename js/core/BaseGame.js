// js/core/BaseGame.js
// Shared base class for ALL Math Quest game modes.
// Loaded by game-handler.php before any game-specific file.
// Used by: advance/, beginner/, expert/, grand-master/ game files.

class BaseGame {
    constructor(config) {
        this.modeKey         = config.modeKey;
        this.gameName        = config.gameName;
        this.skillLevel      = config.skillLevel  || 'expert';
        this.timePerQuestion = config.timePerQuestion || 30;
        this.bonusTime       = config.bonusTime   || this.timePerQuestion;
        this.baseScore       = config.baseScore   || 10;
        this.achievementName = config.achievementName || null;
        this.difficultyRanges = config.difficultyRanges || {};

        // ── Game state ──────────────────────────────────────────────────────
        this.score               = 0;
        this.questionCount       = 0;
        this.questionsPerLevel   = 5;
        this.currentLevel        = 1;
        this.maxLevel            = 10;
        this.correctAnswerPosition = 0;
        this.currentDifficulty   = window.currentDifficulty || 'easy';
        this.levelCompleted      = false;
        this.unlockedLevels      = 1;
        this.starsEarned         = 0;
        this.correctCount        = 0;
        this.perfectLevels       = 0;
        this.totalScore          = 0;
        this.levelStartTime      = null;
        this.questionStartTime   = null;
        this.fastestTime         = Infinity;
        this.levelFastestTime    = Infinity;
        this.secondsLeft         = this.timePerQuestion;
        this.timerInterval       = null;
        this.soundEnabled        = true;
        this.userId              = 'guest';

        this.achievements = {
            firstPerfect : false,
            speedDemon   : false,
            levelMaster  : false,
            scoreKing    : false,
            quickLearner : false,
            special      : false
        };

        // ── Coin system ──────────────────────────────────────────────────
        this.coins = 0;
        this.hintUsed = false;

        // ── Achievement & Animation Systems ────────────────────────────────
        this.achievementSystem = window.achievementSystem || null;
        this.animations = window.animations || null;

        // Lazy sound cache
        this._soundCache = {};

        // Star data cache
        this._starsCache = {};

        // DOM element references
        this.elements = {};

        this.gameCard = document.getElementById('gameCard');
    }

    // ── Sound ──────────────────────────────────────────────────────────────

    _getSound(path) {
        if (!this._soundCache[path]) {
            try { this._soundCache[path] = new Audio(path); }
            catch (e) { return null; }
        }
        return this._soundCache[path];
    }

    _playSound(name) {
        if (!this.soundEnabled) return;
        const map = {
            correct : '/math-game/sounds/correct.mp3',
            wrong   : '/math-game/sounds/wrong.mp3',
            victory : '/math-game/sounds/victory.mp3',
            fail    : '/math-game/sounds/fail.mp3'
        };
        const sound = this._getSound(map[name] || name);
        if (sound) { try { sound.currentTime = 0; sound.play(); } catch (e) {} }
    }

    // ── Difficulty derived from level number ─────────────────────────────

    _difficultyForLevel(level) {
        if (level <= 10) return 'easy';
        if (level <= 25) return 'medium';
        return 'hard';
    }

    // ── localStorage helpers ───────────────────────────────────────────────

    _storageKey(suffix) {
        const userId = this.userId || 'guest';
        const base = `mathQuest_${userId}_${this.skillLevel}_${this.modeKey}`;
        return suffix ? `${base}_${suffix}` : base;
    }

    _loadSavedProgress() {
        const val = parseInt(localStorage.getItem(this._storageKey('')) || '1', 10);
        if (!isNaN(val)) this.unlockedLevels = val;
    }

    _saveProgress() {
        localStorage.setItem(this._storageKey(''), this.unlockedLevels);
    }

    _saveStars(level, stars) {
        localStorage.setItem(this._storageKey(`level_${level}_stars`), stars);
    }

    _loadAllStars() {
        const max = Math.max(this.maxLevel, 45);
        for (let i = 1; i <= max; i++) {
            this._starsCache[i] = parseInt(
                localStorage.getItem(this._storageKey(`level_${i}_stars`)) || '0', 10
            );
        }
    }

    // ── Server coin methods ────────────────────────────────────────────────

    _saveCoinsToServer() {
        fetch('/math-game/coins.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                coins: this.coins
            })
        }).catch(err => console.error('Error saving coins to server:', err));
    }

    _loadCoinsFromServer() {
        return fetch('/math-game/coins.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.coins > 0) {
                    this.coins = data.coins;
                    localStorage.setItem(`mathQuest_coins_${this.userId}`, String(this.coins));
                } else {
                    const savedCoins = localStorage.getItem(`mathQuest_coins_${this.userId}`);
                    this.coins = (savedCoins !== null && !isNaN(parseInt(savedCoins, 10)))
                        ? parseInt(savedCoins, 10) : 0;
                }
                this._lastCoinCount = this.coins;
                return true;
            })
            .catch(err => {
                console.error('Error loading coins from server:', err);
                const savedCoins = localStorage.getItem(`mathQuest_coins_${this.userId}`);
                this.coins = (savedCoins !== null && !isNaN(parseInt(savedCoins, 10)))
                    ? parseInt(savedCoins, 10) : 0;
                this._lastCoinCount = this.coins;
                return false;
            });
    }

    _saveCoins() {
        const val = isNaN(this.coins) ? 0 : Math.max(0, Math.floor(this.coins));
        this.coins = val;
        localStorage.setItem(`mathQuest_coins_${this.userId}`, String(val));
        this._saveCoinsToServer();
    }

    _updateCoinDisplay() {
        ['coinCount', 'coinCountMap', 'coinCountNav'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = this.coins.toLocaleString();
                el.style.transition = 'color 0.2s';
                el.style.color = '#fff';
                setTimeout(() => { el.style.color = ''; }, 300);
            }
        });
        
        if (this.animations && this._lastCoinCount < this.coins) {
            const amount = this.coins - this._lastCoinCount;
            this.animations.showCoinCollect(window.innerWidth / 2, window.innerHeight / 2, amount);
        }
        this._lastCoinCount = this.coins;
    }

    // ── Level selection screen ──────────────────────────────────────────────

    showLevelSelection() {
        if (this.timerInterval) clearInterval(this.timerInterval);
        this.maxLevel = 45;
        this._loadSavedProgress();
        this._loadAllStars();

        const lbPanel = document.getElementById('lbPanel');
        if (lbPanel) {
            lbPanel.style.display = window.innerWidth <= 899 ? 'none' : '';
        }
        const calcPanel = document.getElementById('calcPanel');
        if (calcPanel) calcPanel.style.display = 'none';

        const COL_LR = [40, 100, 160, 220, 280];
        const COL_RL = [280, 220, 160, 100, 40];
        const ROW_H  = 90;
        const TOP_Y  = 45;

        const positions = [];
        for (let row = 0; row < 9; row++) {
            const y    = TOP_Y + row * ROW_H;
            const cols = row % 2 === 0 ? COL_LR : COL_RL;
            cols.forEach(x => positions.push({ x, y }));
        }

        let pathD = '';
        positions.forEach((p, i) => {
            pathD += i === 0 ? `M${p.x},${p.y}` : ` L${p.x},${p.y}`;
        });

        function zone(level) {
            if (level === 11 || level === 25 || level === 45)
                return { outer: '#6b21a8', inner: '#a855f7', text: '#fff', starColor: '#f6e05e', boss: true };
            if (level <= 10) return { outer: '#2f855a', inner: '#48bb78', text: '#fff', starColor: '#f6e05e', boss: false };
            if (level <= 25) return { outer: '#b7791f', inner: '#f6ad55', text: '#fff', starColor: '#f6e05e', boss: false };
            return              { outer: '#c53030', inner: '#fc8181', text: '#fff', starColor: '#f6e05e', boss: false };
        }

        let nodes = '';
        for (let i = 1; i <= this.maxLevel; i++) {
            const pos    = positions[i - 1];
            const cx     = pos.x;
            const cy     = pos.y;
            const stars  = this._starsCache[i] || 0;
            const locked = i > this.unlockedLevels;
            const z      = zone(i);

            const outerColor = locked ? '#90a4ae' : z.outer;
            const innerColor = locked ? '#cfd8dc' : (stars === 3 ? '#fefcbf' : z.inner);
            const numColor   = locked ? '#78909c' : (stars === 3 ? '#744210' : z.text);

            const isBossNode = (i === 11 || i === 25 || i === 45);
            let centerContent;
            if (locked) {
                centerContent = `<text x="${cx}" y="${cy + 5}" text-anchor="middle" font-size="12" font-family="sans-serif">🔒</text>`;
            } else if (isBossNode) {
                centerContent = `<text x="${cx}" y="${cy - 2}" text-anchor="middle" font-size="12" font-family="sans-serif">👹</text>
                    <text x="${cx}" y="${cy + 11}" text-anchor="middle" font-size="7" font-weight="bold" fill="#fff" font-family="sans-serif">BOSS</text>`;
            } else {
                centerContent = `<text x="${cx}" y="${cy + 5}" text-anchor="middle" font-size="11" font-weight="bold" fill="${numColor}" font-family="sans-serif">${i}</text>`;
            }

            let starDots = '';
            if (!locked) {
                const starY  = cy + 34;
                const gap    = 13;
                const startX = cx - gap;
                for (let s = 0; s < 3; s++) {
                    const earned = s < stars;
                    starDots += `<text x="${startX + s * gap}" y="${starY}" text-anchor="middle" font-size="11" font-family="sans-serif" opacity="${earned ? '1' : '0.25'}">⭐</text>`;
                }
            }

            const clickAttr = locked
                ? `onclick="game.unlockLevel(${i})"`
                : `onclick="game.startLevel(${i})"`;
            const cursor = 'pointer';

            nodes += `
                <g ${clickAttr} style="cursor:${cursor}" pointer-events="all">
                    <circle cx="${cx}" cy="${cy}" r="24" fill="${outerColor}"/>
                    <circle cx="${cx}" cy="${cy}" r="20" fill="${innerColor}"/>
                    ${centerContent}
                    ${starDots}
                </g>`;
        }

        this.gameCard.innerHTML = `
            <div id="levelMap">
                <div class="map-top-row">
                    <h3>${this.gameName}</h3>
                    <span class="map-coin-display">🪙 <span id="coinCountMap">${this.coins.toLocaleString()}</span></span>
                    <a href="/math-game/game-selector.php?skill=${this.skillLevel}" class="map-back-btn">🔙 Back</a>
                </div>
                <div id="mapScroll">
                    <svg viewBox="0 0 320 900" width="100%">
                        <path d="${pathD}" fill="none" stroke="#f6e05e" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" opacity="0.3" pointer-events="none"/>
                        <path d="${pathD}" fill="none" stroke="#f59e0b" stroke-width="9" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="14,10" pointer-events="none"/>
                        <path d="${pathD}" fill="none" stroke="#fffde7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="14,10" stroke-dashoffset="12" opacity="0.6" pointer-events="none"/>
                        ${nodes}
                    </svg>
                </div>
            </div>
        `;
    }

    _isBossLevel(level) {
        return level === 11 || level === 25 || level === 45;
    }

    startLevel(level) {
        this.currentLevel      = level;
        this.currentDifficulty = this._difficultyForLevel(level);
        this.isBoss            = this._isBossLevel(level);
        this.questionsPerLevel = this.isBoss ? 23 : 13;
        this.questionCount     = 0;
        this.levelCompleted    = false;
        this.starsEarned       = 0;
        this.correctCount      = 0;
        this.levelStartTime    = Date.now();
        this.levelFastestTime  = Infinity;
        this.secondsLeft       = this.timePerQuestion;
        this._usedQuestions    = new Set();
        this._lastCoinCount = this.coins;

        this._rebuildGameCard();
        this._startTimer();
        this._updateProgress();
        this.generateQuestion();
    }

    _rebuildGameCard() {
        this.gameCard.innerHTML = `
            <div class="game-header">
                <span id="levelInfo" class="level-badge">Level ${this.currentLevel}</span>
                <span id="progressInfo" class="progress-badge">Question 1/${this.questionsPerLevel}</span>
                <span id="timer" class="timer-badge">${this.timePerQuestion}</span>
            </div>
            <div class="coin-bar">
                <span class="coin-display">🪙 <span id="coinCount">${this.coins.toLocaleString()}</span></span>
                <button class="coin-btn" onclick="game.useHint()">💡 Hint <span class="coin-cost">350</span></button>
                <button class="coin-btn" onclick="game.buyExtraTime()">⏱️ +15s <span class="coin-cost">30</span></button>
            </div>
            <div class="skill-indicator">
                <span class="skill-tag ${this.skillLevel}">${this._skillLabel()}</span>
                <span class="difficulty-tag ${this.currentDifficulty}">
                    ${this.currentDifficulty.charAt(0).toUpperCase() + this.currentDifficulty.slice(1)}
                </span>
            </div>
            <div class="stars-container" id="stars">☆☆☆</div>
            <div class="fastest-time">⚡ Fastest: <span id="fastestTime">-</span></div>
            <h2 id="modeTitle">${this.gameName} - ${this.isBoss ? "👹 BOSS " : ""}Level ${this.currentLevel}</h2>
            <h3 id="question"></h3>
            <div class="progress-bar-container">
                <div id="progressBar" class="progress-bar"></div>
            </div>
            <div class="button-group">
                <button onclick="game.checkAnswer(0)" id="btn0"></button>
                <button onclick="game.checkAnswer(1)" id="btn1"></button>
                <button onclick="game.checkAnswer(2)" id="btn2"></button>
            </div>
            <div class="score" id="score">Score: ${this.score}</div>
            <div class="game-controls">
                <button onclick="game.showLevelSelection()" class="control-btn">📋 Levels</button>
                <button onclick="game.toggleSound()" id="soundToggle" class="control-btn">🔊 Sound</button>
                <a href="/math-game/play.php" class="control-btn">🏠 Menu</a>
            </div>
            <div id="achievements" class="achievement-popup" style="display:none;"></div>
        `;

        this.elements = {
            levelInfo   : document.getElementById('levelInfo'),
            progressInfo: document.getElementById('progressInfo'),
            timer       : document.getElementById('timer'),
            stars       : document.getElementById('stars'),
            fastestTime : document.getElementById('fastestTime'),
            question    : document.getElementById('question'),
            progressBar : document.getElementById('progressBar'),
            score       : document.getElementById('score'),
            achievements: document.getElementById('achievements'),
            soundToggle : document.getElementById('soundToggle'),
            buttons     : [
                document.getElementById('btn0'),
                document.getElementById('btn1'),
                document.getElementById('btn2')
            ]
        };

        this._updateCoinDisplay();

        const lbPanel = document.getElementById('lbPanel');
        if (lbPanel) lbPanel.style.display = 'none';

        const calcPanel = document.getElementById('calcPanel');
        if (calcPanel) {
            calcPanel.style.display = this.skillLevel === 'beginner' ? 'none' : 'flex';
        }
    }

    _skillLabel() {
        return {
            beginner      : '🌱 Beginner',
            advance       : '🚀 Advance',
            expert        : '⚡ Expert',
            'grand-master': '👑 Grand Master'
        }[this.skillLevel] || this.skillLevel;
    }

    _startTimer() {
        if (this.timerInterval) clearInterval(this.timerInterval);

        this.timerInterval = setInterval(() => {
            this.secondsLeft--;
            const el = this.elements.timer;
            if (el) {
                el.innerText = this.secondsLeft;
                if (this.secondsLeft <= 5) {
                    el.style.color     = '#f56565';
                    el.style.animation = 'pulse 0.5s infinite';
                } else if (this.secondsLeft <= 15) {
                    el.style.color     = '#ed8936';
                    el.style.animation = '';
                } else {
                    el.style.color     = 'white';
                    el.style.animation = '';
                }
            }
            if (this.secondsLeft <= 0) {
                clearInterval(this.timerInterval);
                this.levelCompleted = true;
                this._calculateStars();
                this._showLevelComplete();
            }
        }, 1000);
    }

    _updateProgress() {
        const { levelInfo, progressInfo, progressBar } = this.elements;
        if (progressInfo) progressInfo.innerText = `Question ${this.questionCount + 1}/${this.questionsPerLevel}`;
        if (levelInfo)    levelInfo.innerText    = `Level ${this.currentLevel}`;
        if (progressBar)  progressBar.style.width = `${(this.questionCount / this.questionsPerLevel) * 100}%`;
    }

    checkAnswer(choice) {
        if (this.levelCompleted) return;

        const timeTaken = (Date.now() - this.questionStartTime) / 1000;
        const { buttons, fastestTime: ftEl, score: scoreEl } = this.elements;

        if (this.animations && buttons[choice]) {
            this.animations.animateButton(buttons[choice]);
        }

        if (timeTaken < this.fastestTime) {
            this.fastestTime = timeTaken;
            if (ftEl) ftEl.innerText = timeTaken.toFixed(1) + 's';
            if (timeTaken < 2 && !this.achievements.quickLearner) {
                this.achievements.quickLearner = true;
                this._showAchievement('⚡ Quick Learner! — Under 2 seconds!');
                if (this.achievementSystem) {
                    this.achievementSystem.checkAchievement('lightning_fast', { fastAnswer: timeTaken });
                }
            }
        }
        if (timeTaken < this.levelFastestTime) this.levelFastestTime = timeTaken;

        if (choice === this.correctAnswerPosition) {
            this.correctCount++;
            buttons[choice].classList.add('correct');
            this._playSound('correct');

            if (this.animations) {
                const btnRect = buttons[choice].getBoundingClientRect();
                this.animations.showStarEarn(btnRect.left + btnRect.width / 2, btnRect.top);
            }

            const levelMultiplier = 1 + (this.currentLevel - 1) * 0.15;
            const bonus = Math.max(0, Math.floor(this.bonusTime - timeTaken));
            const earned = Math.round((this.baseScore + bonus) * levelMultiplier);
            this.score      += earned;
            this.totalScore += earned;

            if (timeTaken < 3 && !this.achievements.speedDemon) {
                this.achievements.speedDemon = true;
                this._showAchievement('🏃 Speed Demon! — Under 3 seconds!');
                const halfTime = this.timePerQuestion / 2;
                if (timeTaken < halfTime && this.achievementSystem) {
                    this.achievementSystem.checkAchievement('half_time_level', { halfTimeLevel: true });
                    this.achievementSystem.updateHalfTimeLevels(true);
                }
            }
            if (this.totalScore > 500 && !this.achievements.scoreKing) {
                this.achievements.scoreKing = true;
                this._showAchievement('👑 Score King! — 500+ points!');
            }

            const coinReward = this.isBoss ? 15 :
                               this.currentLevel <= 10 ? 5 :
                               this.currentLevel <= 25 ? 10 : 12;
            this.coins = (isNaN(this.coins) ? 0 : this.coins) + coinReward;
            this._saveCoins();
            this._updateCoinDisplay();
        } else {
            buttons[choice].classList.add('wrong');
            buttons[this.correctAnswerPosition].classList.add('correct');
            this._playSound('wrong');
            
            if (this.animations) {
                this.animations.shakeElement(buttons[choice]);
            }
        }

        if (scoreEl) scoreEl.innerText = 'Score: ' + this.score;
        this._updateStarsPreview();

        setTimeout(() => {
            buttons.forEach(b => b.classList.remove('correct', 'wrong'));
            this.questionCount++;
            this._updateProgress();

            if (this.questionCount < this.questionsPerLevel) {
                this.secondsLeft = this.timePerQuestion;
                this.generateQuestion();
            } else {
                clearInterval(this.timerInterval);
                this.levelCompleted = true;
                this._calculateStars();
                this._playStarSound();
                this._showLevelComplete();
            }
        }, 1000);
    }

    _updateStarsPreview() {
        const el = this.elements.stars;
        if (!el) return;
        if (this.questionCount === 0) { el.innerHTML = '☆☆☆'; return; }
        const c = this.correctCount;
        let stars;
        if (this.isBoss) {
            if (c >= 20) stars = 3; else if (c >= 9) stars = 2; else if (c >= 6) stars = 1; else stars = 0;
        } else {
            if (c >= 11) stars = 3; else if (c >= 8) stars = 2; else if (c >= 4) stars = 1; else stars = 0;
        }
        
        if (stars > 0 && this.animations) {
            const starEl = this.elements.stars;
            this.animations.showStarsEarned(stars);
        }
        
        el.innerHTML = stars === 3 ? '⭐⭐⭐' : stars === 2 ? '⭐⭐' : stars === 1 ? '⭐' : '☆☆☆';
    }

    _calculateStars() {
        const c = this.correctCount;

        if (this.isBoss) {
            if (c >= 20)     this.starsEarned = 3;
            else if (c >= 9) this.starsEarned = 2;
            else if (c >= 6) this.starsEarned = 1;
            else             this.starsEarned = 0;
        } else {
            if (c >= 11)     this.starsEarned = 3;
            else if (c >= 8) this.starsEarned = 2;
            else if (c >= 4) this.starsEarned = 1;
            else             this.starsEarned = 0;
        }

        if (this.starsEarned === 3) {
            this.perfectLevels++;
            if (!this.achievements.firstPerfect) {
                this.achievements.firstPerfect = true;
                this._showAchievement('💯 Perfect Score!');
                if (this.achievementSystem) {
                    this.achievementSystem.checkAchievement('perfect_score', { perfectLevel: true });
                    this.achievementSystem.checkAchievement('no_mistakes', { noMistakes: true });
                }
            }
            if (this.achievementName && !this.achievements.special) {
                this.achievements.special = true;
                this._showAchievement(this.achievementName);
            }
        }

        this._saveStars(this.currentLevel, this.starsEarned);
        this._starsCache[this.currentLevel] = this.starsEarned;

        const noMistakes = (c === this.questionsPerLevel);
        if (this.achievementSystem) {
            this.achievementSystem.updateNoMistakesStreak(noMistakes);
            this.achievementSystem.checkAchievement('no_mistakes_streak', { noMistakesStreak: true });
            
            const halfTimeLevel = this.levelFastestTime < (this.timePerQuestion / 2);
            if (halfTimeLevel) {
                this.achievementSystem.checkAchievement('half_time_levels', { halfTimeLevels: true });
            }
        }

        if (this.starsEarned >= 2 && this.currentLevel === this.unlockedLevels && this.currentLevel < this.maxLevel) {
            this.unlockedLevels = this.currentLevel + 1;
            this._saveProgress();
        }

        if (this.perfectLevels >= 3 && !this.achievements.levelMaster) {
            this.achievements.levelMaster = true;
            this._showAchievement('👑 Level Master! — 3 perfect levels!');
        }
        
        if (this.achievementSystem) {
            this.achievementSystem.checkAchievement('complete_all', {
                mode: this.modeKey,
                skill: this.skillLevel
            });
            this.achievementSystem.checkAchievement('perfect_all', {
                mode: this.modeKey,
                skill: this.skillLevel
            });
        }
    }

    _playStarSound() {
        if      (this.starsEarned === 3) this._playSound('victory');
        else if (this.starsEarned === 0) this._playSound('fail');
        else                             this._playSound('correct');
    }

    _showAchievement(message) {
        const el = this.elements.achievements;
        if (!el) return;
        el.innerHTML = '🏆 ' + message;
        el.style.display = 'block';
        setTimeout(() => { el.style.display = 'none'; }, 3000);
    }

    _showLevelComplete() {
        if (typeof lbSubmitScore === 'function') {
            lbSubmitScore(this.score);
        }

        fetch('/math-game/save-score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                score: this.score,
                skill: this.skillLevel,
                mode: this.modeKey,
                level: this.currentLevel,
                stars: this.starsEarned,
                correct_count: this.correctCount,
                total_questions: this.questionsPerLevel,
                fastest_time: this.levelFastestTime,
                total_time: ((Date.now() - this.levelStartTime) / 1000)
            })
        }).catch(err => console.error('Error saving score:', err));

        const bonusCoins = this.isBoss
            ? [0, 100, 200, 500][this.starsEarned]
            : [0, 20, 40, 75][this.starsEarned];
        if (bonusCoins > 0) {
            this.coins += bonusCoins;
            this._saveCoins();
            this._updateCoinDisplay();
        }

        if (this.starsEarned === 3 && this.animations) {
            this.animations.celebrateLevelComplete();
        }

        const levelTime  = ((Date.now() - this.levelStartTime) / 1000).toFixed(1);
        const fastest    = isFinite(this.levelFastestTime) ? this.levelFastestTime.toFixed(1) + 's' : '-';
        const nextLevel  = this.currentLevel + 1;
        const finalScore = this.score;
        const lvl        = this.currentLevel;

        const messages = {
            0: '😞 Try again! Need 1 star to unlock the next level.',
            1: '👍 Good! You got 1 star!',
            2: '🌟 Great! You got 2 stars!',
            3: '🏅 PERFECT! 3 stars!'
        };

        const coinBannerStyle = `font-family:'Fredoka One',cursive;font-size:1.1rem;color:#ffd700;background:rgba(255,215,0,0.15);border:2px solid rgba(255,215,0,0.4);border-radius:14px;padding:8px 16px;margin:0.6rem 0;display:block;text-align:center;`;
        const coinDisplay = bonusCoins > 0
            ? `<div style="${coinBannerStyle}">🪙 +${bonusCoins} coins earned! &nbsp;<span style="opacity:0.7;font-size:0.85em;">Total: ${this.coins.toLocaleString()}</span></div>`
            : `<div style="${coinBannerStyle} background:rgba(255,255,255,0.04); color:rgba(255,255,255,0.35); border-color:rgba(255,255,255,0.1);">Get stars to earn bonus coins 🪙</div>`;

        this.gameCard.innerHTML = `
            <h2>${this.isBoss ? '👹 BOSS ' : '🎉 '}Level ${lvl} Complete!</h2>
            <div class="stars-display">${'⭐'.repeat(this.starsEarned)}</div>
            <p style="font-size:2rem;">${finalScore}</p>
            <p>⏱️ Time: ${levelTime}s | ⚡ Fastest: ${fastest}</p>
            <p>🏆 Perfect Levels: ${this.perfectLevels}</p>
            ${coinDisplay}
            <div class="star-message star-${this.starsEarned}">${messages[this.starsEarned]}</div>
            <div class="button-group">
                ${this.currentLevel < this.maxLevel && this.starsEarned >= 1
                    ? `<button onclick="game.continueGame(${nextLevel}, ${finalScore})" class="continue-btn">➡️ Level ${nextLevel}</button>`
                    : ''}
                <button onclick="game.redoLevel(${lvl})" class="redo-btn">🔄 Redo</button>
                <button onclick="game.showLevelSelection()" class="choose-btn">📋 Levels</button>
                <a href="/math-game/play.php" class="menu-btn">🏠 Menu</a>
            </div>
        `;
        
        if (this.achievementSystem) {
            this.achievementSystem.checkAchievement('total_levels', { totalLevels: true });
            this.achievementSystem.checkAchievement('total_perfect', { totalPerfect: true });
            this.achievementSystem.checkAchievement('total_coins', { totalCoins: true });
        }
    }

    redoLevel(level) {
        this.score = 0;
        this.startLevel(level);
    }

    continueGame(nextLevel, currentScore) {
        this.score = currentScore;
        this.startLevel(nextLevel);
    }

    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
        const el = this.elements.soundToggle;
        if (el) el.innerText = this.soundEnabled ? '🔊 Sound' : '🔇 Sound';
    }

    useHint() {
        if (this.hintUsed) { this._showAchievement('⚠️ Already used hint this question!'); return; }
        if (!this._spendCoins(350)) { this._showAchievement('🪙 Not enough coins! Need 350.'); return; }
        this.hintUsed = true;
        const { buttons } = this.elements;
        for (let i = 0; i < 3; i++) {
            if (i !== this.correctAnswerPosition) {
                buttons[i].style.opacity    = '0.25';
                buttons[i].style.pointerEvents = 'none';
                buttons[i].style.transform  = 'scale(0.9)';
                break;
            }
        }
        this._updateCoinDisplay();
    }

    buyExtraTime() {
        if (!this._spendCoins(30)) { this._showAchievement('🪙 Not enough coins! Need 30.'); return; }
        this.secondsLeft += 15;
        const el = this.elements.timer;
        if (el) el.innerText = this.secondsLeft;
        this._showAchievement('⏱️ +15 seconds added!');
        this._updateCoinDisplay();
    }

    unlockLevel(level) {
        if (level <= this.unlockedLevels) { this._showAchievement('✅ Already unlocked!'); return; }
        if (!this._spendCoins(1000)) { this._showAchievement('🪙 Not enough coins! Need 1,000.'); return; }
        if (level > this.unlockedLevels) {
            this.unlockedLevels = level;
            this._saveProgress();
            this._loadAllStars();
        }
        this._showAchievement(`🔓 Level ${level} unlocked!`);
        this.showLevelSelection();
    }

    _earnCoins(amount, reason) {
        this.coins += amount;
        this._saveCoins();
        this._updateCoinDisplay();
        this._showAchievement(`🪙 +${amount} coins — ${reason}`);
    }

    _spendCoins(amount) {
        if (this.coins < amount) return false;
        this.coins -= amount;
        this._saveCoins();
        this._updateCoinDisplay();
        return true;
    }

    init() {
        this.userId = window.currentUserId || localStorage.getItem('mathQuest_userId') || 'guest';
        
        this.soundEnabled = localStorage.getItem('mq_sound') !== 'false';
        
        // Load coins from server first
        this._loadCoinsFromServer().then(() => {
            this._loadSavedProgress();
            this._loadAllStars();
            
            if (window.achievementSystem && !this.achievementSystem) {
                this.achievementSystem = window.achievementSystem;
            }
            
            if (window.animations && !this.animations) {
                this.animations = window.animations;
            }
            
            setTimeout(() => {
                this.showLevelSelection();
                this._updateCoinDisplay();
            }, 100);
        });
    }

    _isUsed(key) {
        return this._usedQuestions && this._usedQuestions.has(String(key));
    }

    _markUsed(key) {
        if (this._usedQuestions) this._usedQuestions.add(String(key));
    }

    generateQuestion() {
        throw new Error(`${this.constructor.name} must implement generateQuestion()`);
    }

    _beginQuestion() {
        this.questionStartTime = Date.now();
        this.hintUsed = false;
        if (this.elements.buttons) {
            this.elements.buttons.forEach(b => {
                b.style.opacity = '';
                b.style.pointerEvents = '';
                b.style.transform = '';
            });
        }
        this._updateCoinDisplay();
        const mapEl = document.getElementById('coinCountMap');
        if (mapEl) mapEl.textContent = this.coins.toLocaleString();
    }

    _setChoices(correctAnswer, wrong1, wrong2, retries = 0) {
        const questionKey = this.elements.question ? this.elements.question.innerText : '';

        if (retries < 10 && this._isUsed(questionKey)) {
            this.generateQuestion();
            return;
        }

        this._markUsed(questionKey);

        const choices = [correctAnswer, wrong1, wrong2];
        for (let i = choices.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [choices[i], choices[j]] = [choices[j], choices[i]];
        }
        this.correctAnswerPosition = choices.indexOf(correctAnswer);
        const { buttons } = this.elements;
        buttons[0].innerText = choices[0];
        buttons[1].innerText = choices[1];
        buttons[2].innerText = choices[2];
    }
}