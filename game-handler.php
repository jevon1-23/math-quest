<?php
require_once 'config.php';
requireLogin();  // This forces login if not logged in

$allowed_skills = ['beginner', 'advance', 'expert', 'grand-master'];
$allowed_modes  = [
    'beginner'     => ['add', 'subtract', 'multiply', 'div'],
    'advance'      => ['decimal', 'fractions', 'perimeter', 'rounding'],
    'expert'       => ['algebra', 'area', 'factorization', 'percentage', 'simplifying-expressions'],
    'grand-master' => ['logarithms', 'pythagorean-theorem', 'trigonometry'],
];
$allowed_diffs = ['easy', 'medium', 'hard'];

$skill = in_array($_GET['skill'] ?? '', $allowed_skills)        ? $_GET['skill'] : 'beginner';
$mode  = in_array($_GET['mode']  ?? '', $allowed_modes[$skill]) ? $_GET['mode']  : $allowed_modes[$skill][0];
$diff  = in_array($_GET['diff']  ?? '', $allowed_diffs)         ? $_GET['diff']  : 'easy';

$modeNames = [
    'add'=>'Addition','subtract'=>'Subtraction','multiply'=>'Multiplication','div'=>'Division',
    'decimal'=>'Decimals','fractions'=>'Fractions','perimeter'=>'Perimeter','rounding'=>'Rounding',
    'algebra'=>'Algebra','area'=>'Area','factorization'=>'Factorization',
    'percentage'=>'Percentage','simplifying-expressions'=>'Simplifying',
    'logarithms'=>'Logarithms','pythagorean-theorem'=>'Pythagorean','trigonometry'=>'Trigonometry',
];
$modeName = $modeNames[$mode] ?? ucfirst($mode);
$isBeginner = $skill === 'beginner';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest — <?php echo htmlspecialchars($modeName); ?></title>
    <link rel="stylesheet" href="style.css?v=2">
    <style>
        /* Power-up bar styles */
        .powerup-bar {
            display: flex;
            gap: 10px;
            margin: 10px 0;
            justify-content: center;
            flex-wrap: wrap;
            padding: 10px;
            background: rgba(0,0,0,0.2);
            border-radius: 15px;
        }
        .pu-btn {
            background: linear-gradient(135deg, #4a5568, #2d3748);
            border: none;
            border-radius: 25px;
            padding: 8px 16px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
        }
        .pu-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #5a6578, #3d4758);
        }
        .pu-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .pu-count {
            background: rgba(0,0,0,0.5);
            border-radius: 12px;
            padding: 2px 6px;
            margin-left: 6px;
            font-size: 0.7rem;
        }
        .pu-active {
            background: #48bb78;
            color: white;
            margin-left: 6px;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 0.7rem;
        }

        /* Colorful Calculator Styles - No Flash */
        #calcPanel {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border-radius: 20px;
            padding: 15px;
            margin-top: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            transition: none !important;
            will-change: auto;
        }

        #calcPanel.calc-hidden {
            display: none !important;
        }

        #calcPanel.calc-visible {
            display: block !important;
        }

        #calcPanel .calc-title {
            text-align: center;
            color: #ffd700;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        #calcPanel .calc-display {
            background: #0f0f1a;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid rgba(255,215,0,0.3);
        }

        #calcPanel .calc-expr {
            color: #888;
            font-size: 0.85rem;
            min-height: 25px;
            text-align: right;
            font-family: monospace;
        }

        #calcPanel .calc-val {
            color: #ffd700;
            font-size: 1.8rem;
            font-weight: bold;
            text-align: right;
            font-family: monospace;
        }

        #calcPanel .calc-row {
            display: grid;
            gap: 8px;
            margin-bottom: 8px;
        }

        #calcPanel .calc-btn {
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        #calcPanel .calc-btn:active {
            transform: scale(0.95);
        }

        #calcPanel .cb-fn {
            background: linear-gradient(135deg, #8e44ad, #6c3483);
            color: white;
        }

        #calcPanel .cb-clr {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        #calcPanel .cb-del {
            background: linear-gradient(135deg, #f1c40f, #d4ac0d);
            color: #1a1a2e;
        }

        #calcPanel .cb-op {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }

        #calcPanel .cb-num {
            background: linear-gradient(135deg, #2d3561, #1a1f3a);
            color: white;
        }

        #calcPanel .cb-eq {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
        }

        #calcPanel #angleToggle {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }

        /* Smooth game card transitions */
        #gameCard {
            transition: opacity 0.15s ease;
        }
    </style>
    <script>
        window.currentUserId = '<?php echo $_SESSION['user_id'] ?? 'guest'; ?>';
        window.currentUsername = '<?php echo addslashes($_SESSION['user_name'] ?? ''); ?>';
        localStorage.setItem('mathQuest_userId', window.currentUserId);
        if (window.currentUsername) {
            localStorage.setItem('mathQuest_playerName_' + window.currentUserId, window.currentUsername);
        }
    </script>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="game-page-wrapper">

    <div class="lb-panel" id="lbPanel" style="display:none;">
        <div class="lb-header">
            <h3>🏆 Leaderboard</h3>
            <div class="lb-subtitle"><?php echo htmlspecialchars($modeName); ?></div>
        </div>
        <div class="lb-name-section" style="display:none;">
            <input type="text" id="lbNameInput" placeholder="Your name…" maxlength="16"/>
            <button onclick="lbSetName()">Save</button>
        </div>
        <div class="lb-body" id="lbBody">
            <div class="lb-empty-msg">No scores yet.<br>Complete a level to appear!</div>
        </div>
        <div class="lb-your-pos" id="lbYourPos"></div>
    </div>

    <div class="card" id="gameCard">
        <div class="loading">Loading game…</div>
    </div>

    <div id="calcPanel" class="<?php echo $isBeginner ? 'calc-hidden' : 'calc-visible'; ?>">
        <div class="calc-title">🧮 Scientific Calculator</div>

        <div class="calc-display" id="calcDisplay">
            <div class="calc-expr" id="calcExpr"></div>
            <div class="calc-val" id="calcVal">0</div>
        </div>

        <div class="calc-row" style="grid-template-columns:repeat(4,1fr)">
            <button class="calc-btn cb-fn" onclick="calcFn('sin')">sin</button>
            <button class="calc-btn cb-fn" onclick="calcFn('cos')">cos</button>
            <button class="calc-btn cb-fn" onclick="calcFn('tan')">tan</button>
            <button class="calc-btn cb-fn" onclick="calcFn('log')">log</button>
        </div>
        <div class="calc-row" style="grid-template-columns:repeat(4,1fr)">
            <button class="calc-btn cb-fn" onclick="calcFn('ln')">ln</button>
            <button class="calc-btn cb-fn" onclick="calcFn('√')">√</button>
            <button class="calc-btn cb-fn" onclick="calcInput('π')">π</button>
            <button class="calc-btn cb-fn" onclick="calcInput('^')">xʸ</button>
        </div>

        <div class="calc-row" style="grid-template-columns:repeat(4,1fr)">
            <button class="calc-btn cb-clr" onclick="calcClear()">C</button>
            <button class="calc-btn cb-del" onclick="calcDel()">⌫</button>
            <button class="calc-btn cb-op"  onclick="calcInput('(')">(</button>
            <button class="calc-btn cb-op"  onclick="calcInput(')')">)</button>
        </div>
        <div class="calc-row" style="grid-template-columns:repeat(4,1fr)">
            <button class="calc-btn cb-num" onclick="calcInput('7')">7</button>
            <button class="calc-btn cb-num" onclick="calcInput('8')">8</button>
            <button class="calc-btn cb-num" onclick="calcInput('9')">9</button>
            <button class="calc-btn cb-op"  onclick="calcInput('÷')">÷</button>
        </div>
        <div class="calc-row" style="grid-template-columns:repeat(4,1fr)">
            <button class="calc-btn cb-num" onclick="calcInput('4')">4</button>
            <button class="calc-btn cb-num" onclick="calcInput('5')">5</button>
            <button class="calc-btn cb-num" onclick="calcInput('6')">6</button>
            <button class="calc-btn cb-op"  onclick="calcInput('×')">×</button>
        </div>
        <div class="calc-row" style="grid-template-columns:repeat(4,1fr)">
            <button class="calc-btn cb-num" onclick="calcInput('1')">1</button>
            <button class="calc-btn cb-num" onclick="calcInput('2')">2</button>
            <button class="calc-btn cb-num" onclick="calcInput('3')">3</button>
            <button class="calc-btn cb-op"  onclick="calcInput('-')">−</button>
        </div>
        <div class="calc-row" style="grid-template-columns:repeat(4,1fr)">
            <button class="calc-btn cb-num" onclick="calcInput('0')">0</button>
            <button class="calc-btn cb-num" onclick="calcInput('.')">.</button>
            <button class="calc-btn cb-eq"  onclick="calcEquals()">=</button>
            <button class="calc-btn cb-op"  onclick="calcInput('+')">+</button>
        </div>

        <div style="text-align:center; margin-top:4px;">
            <button class="calc-btn" id="angleToggle" onclick="calcToggleAngle()" style="width:100%;font-size:0.8rem;">
                📐 Mode: DEG
            </button>
        </div>
    </div>

</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<?php
$jsFile = $skill . '/' . $mode . '.js';
if (!preg_match('/^[a-z\-]+\/[a-z\-]+\.js$/', $jsFile)) {
    die('<script>console.error("Invalid game path");</script>');
}
?>

<script>
    var currentSkill      = <?php echo json_encode($skill); ?>;
    var currentDifficulty = <?php echo json_encode($diff);  ?>;
    var currentMode       = <?php echo json_encode($mode);  ?>;
</script>

<script src="js/core/BaseGame.js"></script>
<script src="js/core/GameInitializer.js"></script>
<script src="<?php echo htmlspecialchars($jsFile); ?>"></script>

<script>
// ============================================
// POWER-UP SYSTEM
// ============================================

class PowerupSystem {
    constructor(gameInstance) {
        this.game = gameInstance;
        this.powerups = {
            shield: { count: 0, active: false },
            freeze: { count: 0, active: false },
            skip: { count: 0, active: false },
            doublePoints: { count: 0, active: false }
        };
        this.freezeTimer = null;
        this.doublePointsActive = false;
        this.originalBaseScore = 10;
        this._loadInventory();
    }

    _loadInventory() {
        const saved = localStorage.getItem('mathQuest_powerups');
        if (saved) {
            try {
                const data = JSON.parse(saved);
                Object.keys(this.powerups).forEach(key => {
                    if (data[key] !== undefined) {
                        this.powerups[key].count = data[key];
                    }
                });
            } catch(e) {}
        }
        
        const shieldCount = localStorage.getItem('powerup_shield');
        const freezeCount = localStorage.getItem('powerup_freeze');
        const skipCount = localStorage.getItem('powerup_skip');
        const doublePointsCount = localStorage.getItem('powerup_doublePoints');
        
        if (shieldCount) this.powerups.shield.count += parseInt(shieldCount) || 0;
        if (freezeCount) this.powerups.freeze.count += parseInt(freezeCount) || 0;
        if (skipCount) this.powerups.skip.count += parseInt(skipCount) || 0;
        if (doublePointsCount) this.powerups.doublePoints.count += parseInt(doublePointsCount) || 0;
        
        localStorage.removeItem('powerup_shield');
        localStorage.removeItem('powerup_freeze');
        localStorage.removeItem('powerup_skip');
        localStorage.removeItem('powerup_doublePoints');
        
        this._saveInventory();
        console.log('Power-ups loaded:', this.powerups);
    }

    _saveInventory() {
        const saveData = {};
        Object.keys(this.powerups).forEach(key => {
            saveData[key] = this.powerups[key].count;
        });
        localStorage.setItem('mathQuest_powerups', JSON.stringify(saveData));
    }

    addPowerup(type, amount = 1) {
        if (this.powerups[type]) {
            this.powerups[type].count += amount;
            this._saveInventory();
            this._renderPowerupBar();
            if (this.game) this.game._showAchievement(`🎁 +${amount} ${type} power-up!`);
            return true;
        }
        return false;
    }

    getCount(type) {
        return this.powerups[type]?.count || 0;
    }

    usePowerup(type) {
        if (!this.powerups[type] || this.powerups[type].count <= 0) {
            if (this.game) this.game._showAchievement(`❌ No ${type} power-ups left! Buy in Shop.`);
            return false;
        }

        switch(type) {
            case 'shield':
                this._activateShield();
                break;
            case 'freeze':
                this._activateFreeze();
                break;
            case 'skip':
                this._activateSkip();
                break;
            case 'doublePoints':
                this._activateDoublePoints();
                break;
            default:
                return false;
        }

        this.powerups[type].count--;
        this._saveInventory();
        this._renderPowerupBar();
        return true;
    }

    _activateShield() {
        if (this.powerups.shield.active) {
            if (this.game) this.game._showAchievement('🛡️ Shield already active!');
            return;
        }
        this.powerups.shield.active = true;
        if (this.game) this.game._showAchievement('🛡️ Shield activated! Next wrong answer blocked.');
        this._renderPowerupBar();
        
        setTimeout(() => {
            if (this.powerups.shield.active) {
                this.powerups.shield.active = false;
                this._renderPowerupBar();
                if (this.game) this.game._showAchievement('🛡️ Shield expired!');
            }
        }, 30000);
    }

    _activateFreeze() {
        if (this.powerups.freeze.active) {
            if (this.game) this.game._showAchievement('⏸️ Timer already frozen!');
            return;
        }
        
        if (this.game && this.game.timerInterval) {
            clearInterval(this.game.timerInterval);
            this.powerups.freeze.active = true;
            if (this.game) this.game._showAchievement('⏸️ Timer frozen for 10 seconds!');
            this._renderPowerupBar();
            
            this.freezeTimer = setTimeout(() => {
                this.powerups.freeze.active = false;
                if (this.game && !this.game.levelCompleted) {
                    this.game._startTimer();
                    if (this.game) this.game._showAchievement('▶️ Timer resumed!');
                }
                this._renderPowerupBar();
            }, 10000);
        }
    }

    _activateSkip() {
        if (this.game && this.game.skipUsedThis) {
            if (this.game) this.game._showAchievement('⏭️ Already skipped this question!');
            return;
        }
        
        if (this.game) {
            this.game.skipUsedThis = true;
            this.game._showAchievement('⏭️ Question skipped — no penalty!');
            
            this.game.questionCount++;
            this.game._updateProgress();
            
            if (this.game.questionCount < this.game.questionsPerLevel) {
                this.game.secondsLeft = this.game.timePerQuestion;
                this.game.skipUsedThis = false;
                this.game.hintUsed = false;
                this.game.generateQuestion();
            } else {
                clearInterval(this.game.timerInterval);
                this.game.levelCompleted = true;
                this.game._calculateStars();
                this.game._playStarSound();
                this.game._showLevelComplete();
            }
        }
    }

    _activateDoublePoints() {
        if (this.doublePointsActive) {
            if (this.game) this.game._showAchievement('✨ Double points already active!');
            return;
        }
        
        this.doublePointsActive = true;
        if (this.game) {
            this.originalBaseScore = this.game.baseScore;
            this.game.baseScore = this.originalBaseScore * 2;
            this.game._showAchievement('✨ Double points activated for 30 seconds!');
        }
        this._renderPowerupBar();
        
        setTimeout(() => {
            this.doublePointsActive = false;
            if (this.game) this.game.baseScore = this.originalBaseScore;
            if (this.game) this.game._showAchievement('✨ Double points expired!');
            this._renderPowerupBar();
        }, 30000);
    }

    shouldBlockWrong() {
        if (this.powerups.shield.active) {
            this.powerups.shield.active = false;
            this._renderPowerupBar();
            if (this.game) this.game._showAchievement('🛡️ Shield blocked a wrong answer!');
            return true;
        }
        return false;
    }

    isFrozen() {
        return this.powerups.freeze.active;
    }

    _renderPowerupBar() {
        let powerupBar = document.getElementById('powerupBar');
        if (!powerupBar) {
            const gameCard = document.getElementById('gameCard');
            if (gameCard) {
                const existingBar = gameCard.querySelector('.powerup-bar');
                if (existingBar) return;
                
                powerupBar = document.createElement('div');
                powerupBar.id = 'powerupBar';
                powerupBar.className = 'powerup-bar';
                
                const coinBar = gameCard.querySelector('.coin-bar');
                if (coinBar) {
                    coinBar.insertAdjacentElement('afterend', powerupBar);
                } else {
                    gameCard.insertBefore(powerupBar, gameCard.firstChild);
                }
            } else {
                return;
            }
        }
        
        powerupBar.innerHTML = `
            <button class="pu-btn" onclick="window.powerupSystem.usePowerup('shield')" ${this.getCount('shield') === 0 ? 'disabled' : ''}>
                🛡️ Shield <span class="pu-count">×${this.getCount('shield')}</span>
                ${this.powerups.shield.active ? '<span class="pu-active">ACTIVE</span>' : ''}
            </button>
            <button class="pu-btn" onclick="window.powerupSystem.usePowerup('freeze')" ${this.getCount('freeze') === 0 ? 'disabled' : ''}>
                ⏸️ Freeze <span class="pu-count">×${this.getCount('freeze')}</span>
                ${this.powerups.freeze.active ? '<span class="pu-active">ACTIVE</span>' : ''}
            </button>
            <button class="pu-btn" onclick="window.powerupSystem.usePowerup('skip')" ${this.getCount('skip') === 0 ? 'disabled' : ''}>
                ⏭️ Skip <span class="pu-count">×${this.getCount('skip')}</span>
            </button>
            <button class="pu-btn" onclick="window.powerupSystem.usePowerup('doublePoints')" ${this.getCount('doublePoints') === 0 ? 'disabled' : ''}>
                ✨ 2x Points <span class="pu-count">×${this.getCount('doublePoints')}</span>
                ${this.doublePointsActive ? '<span class="pu-active">ACTIVE</span>' : ''}
            </button>
        `;
    }

    showPowerupBar() {
        this._renderPowerupBar();
    }

    cleanup() {
        if (this.freezeTimer) {
            clearTimeout(this.freezeTimer);
            this.freezeTimer = null;
        }
        this.powerups.shield.active = false;
        this.powerups.freeze.active = false;
        this.doublePointsActive = false;
        if (this.game) {
            this.game.baseScore = this.originalBaseScore;
        }
    }
}

// ============================================
// CALCULATOR FUNCTIONS
// ============================================

let calcExpression = '';
let calcAngleDeg   = true;

function calcUpdateDisplay() {
    const exprEl = document.getElementById('calcExpr');
    const valEl = document.getElementById('calcVal');
    if (exprEl) exprEl.textContent = calcExpression;
    if (valEl) valEl.textContent = calcExpression || '0';
}

function calcInput(val) {
    calcExpression += val;
    calcUpdateDisplay();
}

function calcClear() {
    calcExpression = '';
    calcUpdateDisplay();
}

function calcDel() {
    calcExpression = calcExpression.slice(0, -1);
    calcUpdateDisplay();
}

function calcFn(fn) {
    calcExpression += fn + '(';
    calcUpdateDisplay();
}

function calcToggleAngle() {
    calcAngleDeg = !calcAngleDeg;
    const toggle = document.getElementById('angleToggle');
    if (toggle) toggle.textContent = '📐 Mode: ' + (calcAngleDeg ? 'DEG' : 'RAD');
}

function safeMathParse(raw) {
    let src = raw
        .replace(/×/g, '*').replace(/÷/g, '/')
        .replace(/π/g, '3.14159265358979')
        .replace(/\^/g, '**')
        .trim();

    const toRad = v => calcAngleDeg ? v * Math.PI / 180 : v;
    let pos = 0;

    function peek() { return src[pos]; }
    function consume(ch) { if (src[pos] !== ch) throw new Error('Expected ' + ch); pos++; }

    function parseExpr() { return parseAddSub(); }

    function parseAddSub() {
        let v = parseMulDiv();
        while (pos < src.length && (peek() === '+' || peek() === '-')) {
            const op = src[pos++];
            const r = parseMulDiv();
            v = op === '+' ? v + r : v - r;
        }
        return v;
    }

    function parseMulDiv() {
        let v = parsePow();
        while (pos < src.length && (peek() === '*' || peek() === '/')) {
            const op = src[pos++];
            if (op === '*' && peek() === '*') { pos++; v = Math.pow(v, parsePow()); continue; }
            const r = parsePow();
            v = op === '*' ? v * r : v / r;
        }
        return v;
    }

    function parsePow() { return parseUnary(); }

    function parseUnary() {
        if (peek() === '-') { pos++; return -parsePrimary(); }
        if (peek() === '+') { pos++; return parsePrimary(); }
        return parsePrimary();
    }

    function parsePrimary() {
        if (/[\d.]/.test(peek() || '')) {
            let num = '';
            while (pos < src.length && /[\d.]/.test(src[pos])) num += src[pos++];
            return parseFloat(num);
        }
        if (peek() === '(') {
            consume('(');
            const v = parseExpr();
            consume(')');
            return v;
        }
        const rest = src.slice(pos);
        const fnMatch = rest.match(/^(sin|cos|tan|log|ln|sqrt|√|abs)/);
        if (fnMatch) {
            const fn = fnMatch[1]; pos += fn.length;
            consume('(');
            const arg = parseExpr();
            consume(')');
            switch (fn) {
                case 'sin': return Math.sin(toRad(arg));
                case 'cos': return Math.cos(toRad(arg));
                case 'tan': return Math.tan(toRad(arg));
                case 'log': return Math.log10(arg);
                case 'ln': return Math.log(arg);
                case 'sqrt': case '√': return Math.sqrt(arg);
                case 'abs': return Math.abs(arg);
            }
        }
        throw new Error('Unexpected character: ' + peek());
    }

    const result = parseExpr();
    if (pos !== src.length) throw new Error('Unexpected token at pos ' + pos);
    return result;
}

function calcEquals() {
    try {
        const result = safeMathParse(calcExpression);
        const rounded = Math.round(result * 1e10) / 1e10;
        const exprEl = document.getElementById('calcExpr');
        const valEl = document.getElementById('calcVal');
        if (exprEl) exprEl.textContent = calcExpression + ' =';
        if (valEl) valEl.textContent = isNaN(rounded) ? 'Error' : rounded;
        calcExpression = isNaN(rounded) ? '' : String(rounded);
    } catch(e) {
        const valEl = document.getElementById('calcVal');
        if (valEl) valEl.textContent = 'Error';
        calcExpression = '';
    }
}

// ============================================
// LEADERBOARD FUNCTIONS
// ============================================

const LB_KEY = 'mathQuest_lb_' + (window.currentUserId || 'guest') + '_' + currentMode;
const LB_MAX = 20;
const TIERS = [
    { label:'Champion', icon:'🏆', cls:'champion', range:[1,1], avatarBg:'#d97706' },
    { label:'Diamond', icon:'💎', cls:'diamond', range:[2,4], avatarBg:'#7c3aed' },
    { label:'Gold', icon:'🥇', cls:'gold', range:[5,8], avatarBg:'#ca8a04' },
    { label:'Silver', icon:'🥈', cls:'silver', range:[9,13], avatarBg:'#64748b' },
    { label:'Bronze', icon:'🥉', cls:'bronze', range:[14,20], avatarBg:'#b45309' },
];

function lbLoad() { try { return JSON.parse(localStorage.getItem(LB_KEY)) || []; } catch(e) { return []; } }
function lbSave(e) { localStorage.setItem(LB_KEY, JSON.stringify(e)); }
function lbGetName() { return localStorage.getItem('mathQuest_playerName_' + (window.currentUserId || 'guest')) || window.currentUsername || ''; }

function lbSetName() {
    const val = document.getElementById('lbNameInput')?.value.trim();
    if (!val) return;
    localStorage.setItem('mathQuest_playerName_' + (window.currentUserId || 'guest'), val);
    lbRender();
}

function lbSubmitScore(score) {
    const name = lbGetName();
    if (!name) return;
    const entries = lbLoad();
    const idx = entries.findIndex(e => e.name === name);
    if (idx !== -1) { if (score > entries[idx].score) entries[idx].score = score; }
    else entries.push({ name, score });
    entries.sort((a,b) => b.score - a.score);
    lbSave(entries.slice(0, LB_MAX));
    lbRender();
}

function lbColor(name) {
    const colors = ['#3b82f6','#8b5cf6','#ec4899','#10b981','#f59e0b','#ef4444','#06b6d4'];
    let h = 0;
    for (let i = 0; i < name.length; i++) h = name.charCodeAt(i) + ((h<<5)-h);
    return colors[Math.abs(h) % colors.length];
}

function lbRender() {
    const entries = lbLoad();
    const myName = lbGetName();
    const body = document.getElementById('lbBody');
    const yourPos = document.getElementById('lbYourPos');

    if (!body) return;

    if (!entries.length) {
        body.innerHTML = '<div class="lb-empty-msg">No scores yet.<br>Complete a level to appear!</div>';
        if (yourPos) yourPos.style.display = 'none';
        return;
    }

    let html = '';
    TIERS.forEach(tier => {
        const [from, to] = tier.range;
        const slice = entries.slice(from-1, to);
        if (!slice.length) return;
        html += `<div class="lb-tier"><div class="lb-tier-header ${tier.cls}"><span class="lb-tier-icon">${tier.icon}</span><span>${tier.label}</span></div><div class="lb-tier-entries">`;
        slice.forEach((e) => {
            const isMe = e.name === myName;
            const init = e.name.substring(0,2).toUpperCase();
            html += `<div class="lb-entry ${isMe?'is-me':''}">
                <div class="lb-avatar" style="background:${lbColor(e.name)}">${init}</div>
                <span class="lb-entry-name ${isMe?'is-me':''}">${e.name}${isMe?' (Me)':''}</span>
                <span class="lb-entry-score">${e.score.toLocaleString()}</span>
            </div>`;
        });
        html += '</div></div>';
    });
    body.innerHTML = html;

    const myPos = entries.findIndex(e => e.name === myName);
    if (myPos !== -1 && myName && yourPos) {
        const tier = TIERS.find(t => myPos+1 >= t.range[0] && myPos+1 <= t.range[1]);
        yourPos.style.display = 'block';
        yourPos.innerHTML = `You are <strong>#${myPos+1}</strong>${tier?' · '+tier.icon+' '+tier.label:''} · <strong>${entries[myPos].score.toLocaleString()}</strong> pts`;
    } else if (yourPos) {
        yourPos.style.display = 'none';
    }
}

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    const lb = document.getElementById('lbPanel');
    if (lb && window.innerWidth > 899) lb.style.display = '';
    lbRender();
    
    // Set initial calculator visibility based on skill level
    const calcPanel = document.getElementById('calcPanel');
    if (calcPanel) {
        if (currentSkill === 'beginner') {
            calcPanel.classList.add('calc-hidden');
            calcPanel.classList.remove('calc-visible');
        } else {
            calcPanel.classList.remove('calc-hidden');
            calcPanel.classList.add('calc-visible');
        }
    }
    
    const checkGameInterval = setInterval(() => {
        if (typeof window.game !== 'undefined' && window.game && !window.powerupSystem) {
            window.powerupSystem = new PowerupSystem(window.game);
            
            setTimeout(() => {
                if (window.powerupSystem) {
                    window.powerupSystem._renderPowerupBar();
                }
            }, 500);
            
            const originalStartLevel = window.game.startLevel;
            window.game.startLevel = function(level) {
                originalStartLevel.call(this, level);
                setTimeout(() => {
                    if (window.powerupSystem) {
                        window.powerupSystem._renderPowerupBar();
                    }
                }, 100);
            };
            
            // Override the calculator visibility method in BaseGame to prevent flashing
            const originalRebuildGameCard = window.game._rebuildGameCard;
            if (originalRebuildGameCard) {
                window.game._rebuildGameCard = function() {
                    originalRebuildGameCard.call(this);
                    // Restore calculator visibility without flashing
                    const calcPanel = document.getElementById('calcPanel');
                    if (calcPanel) {
                        if (currentSkill === 'beginner') {
                            calcPanel.classList.add('calc-hidden');
                            calcPanel.classList.remove('calc-visible');
                        } else {
                            calcPanel.classList.remove('calc-hidden');
                            calcPanel.classList.add('calc-visible');
                        }
                    }
                };
            }
            
            const originalCheckAnswer = window.game.checkAnswer;
            window.game.checkAnswer = function(choice) {
                if (window.powerupSystem && window.powerupSystem.isFrozen()) {
                    this._showAchievement('⏸️ Timer is frozen! Cannot answer yet.');
                    return;
                }
                if (choice !== this.correctAnswerPosition && window.powerupSystem && window.powerupSystem.shouldBlockWrong()) {
                    this.correctCount++;
                    this._playSound('correct');
                    this._updateStarsPreview();
                    
                    setTimeout(() => {
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
                    return;
                }
                originalCheckAnswer.call(this, choice);
            };
            
            clearInterval(checkGameInterval);
        }
    }, 100);
});
</script>

<?php include 'background-music.php'; ?>
</body>
</html>