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

    <div id="calcPanel">
        <div class="calc-title">🧮 Calculator</div>

        <div class="calc-display" id="calcDisplay">
            <span class="calc-expr" id="calcExpr"></span>
            <span class="calc-val" id="calcVal">0</span>
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
            <button class="calc-btn cb-op"  onclick="calcInput('+')">+</button>
            <button class="calc-btn cb-eq"  onclick="calcEquals()">=</button>
        </div>

        <div style="text-align:center; margin-top:4px;">
            <button class="calc-btn cb-fn" id="angleToggle" onclick="calcToggleAngle()" style="width:100%;font-size:0.72rem;">
                Mode: DEG
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
let calcExpression = '';
let calcAngleDeg   = true;

function calcUpdateDisplay() {
    document.getElementById('calcExpr').textContent = calcExpression;
    document.getElementById('calcVal').textContent  = calcExpression || '0';
}

function calcInput(val) {
    calcExpression += val;
    calcUpdateDisplay();
}

function calcClear() {
    calcExpression = '';
    document.getElementById('calcExpr').textContent = '';
    document.getElementById('calcVal').textContent  = '0';
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
    document.getElementById('angleToggle').textContent = 'Mode: ' + (calcAngleDeg ? 'DEG' : 'RAD');
}

// Safe math parser — no eval() used anywhere
function safeMathParse(raw) {
    // Normalise input
    let src = raw
        .replace(/×/g, '*').replace(/÷/g, '/')
        .replace(/π/g, '3.14159265358979')
        .replace(/\^/g, '**')
        .trim();

    const toRad = v => calcAngleDeg ? v * Math.PI / 180 : v;
    let pos = 0;

    function peek()  { return src[pos]; }
    function consume(ch) { if (src[pos] !== ch) throw new Error('Expected ' + ch); pos++; }

    function parseExpr()   { return parseAddSub(); }

    function parseAddSub() {
        let v = parseMulDiv();
        while (pos < src.length && (peek() === '+' || peek() === '-')) {
            const op = src[pos++];
            const r  = parseMulDiv();
            v = op === '+' ? v + r : v - r;
        }
        return v;
    }

    function parseMulDiv() {
        let v = parsePow();
        while (pos < src.length && (peek() === '*' || peek() === '/')) {
            const op = src[pos++];
            // handle **
            if (op === '*' && peek() === '*') { pos++; v = Math.pow(v, parsePow()); continue; }
            const r = parsePow();
            v = op === '*' ? v * r : v / r;
        }
        return v;
    }

    function parsePow() { return parseUnary(); }

    function parseUnary() {
        if (peek() === '-') { pos++; return -parsePrimary(); }
        if (peek() === '+') { pos++; return  parsePrimary(); }
        return parsePrimary();
    }

    function parsePrimary() {
        // Number
        if (/[\d.]/.test(peek() || '')) {
            let num = '';
            while (pos < src.length && /[\d.]/.test(src[pos])) num += src[pos++];
            return parseFloat(num);
        }
        // Parenthesised expression
        if (peek() === '(') {
            consume('(');
            const v = parseExpr();
            consume(')');
            return v;
        }
        // Named functions / constants
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
                case 'ln':  return Math.log(arg);
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
        const result  = safeMathParse(calcExpression);
        const rounded = Math.round(result * 1e10) / 1e10;
        document.getElementById('calcExpr').textContent = calcExpression + ' =';
        document.getElementById('calcVal').textContent  = isNaN(rounded) ? 'Error' : rounded;
        calcExpression = isNaN(rounded) ? '' : String(rounded);
    } catch(e) {
        document.getElementById('calcVal').textContent = 'Error';
        calcExpression = '';
    }
}

const LB_KEY  = 'mathQuest_lb_' + window.currentUserId + '_' + currentMode;
const LB_MAX  = 20;
const TIERS   = [
    { label:'Champion', icon:'🏆', cls:'champion', range:[1,1],   avatarBg:'#d97706' },
    { label:'Diamond',  icon:'💎', cls:'diamond',  range:[2,4],   avatarBg:'#7c3aed' },
    { label:'Gold',     icon:'🥇', cls:'gold',     range:[5,8],   avatarBg:'#ca8a04' },
    { label:'Silver',   icon:'🥈', cls:'silver',   range:[9,13],  avatarBg:'#64748b' },
    { label:'Bronze',   icon:'🥉', cls:'bronze',   range:[14,20], avatarBg:'#b45309' },
];

function lbLoad() { try { return JSON.parse(localStorage.getItem(LB_KEY)) || []; } catch(e) { return []; } }
function lbSave(e) { localStorage.setItem(LB_KEY, JSON.stringify(e)); }
function lbGetName() { return localStorage.getItem('mathQuest_playerName_' + window.currentUserId) || ''; }

function lbSetName() {
    const val = document.getElementById('lbNameInput').value.trim();
    if (!val) return;
    localStorage.setItem('mathQuest_playerName_' + window.currentUserId, val);
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
    const myName  = lbGetName();
    const body    = document.getElementById('lbBody');
    const yourPos = document.getElementById('lbYourPos');
    const inp     = document.getElementById('lbNameInput');
    if (myName && inp && !inp.value) inp.value = myName;

    if (!entries.length) {
        body.innerHTML = '<div class="lb-empty-msg">No scores yet.<br>Complete a level to appear!</div>';
        yourPos.style.display = 'none';
        return;
    }

    let html = '';
    TIERS.forEach(tier => {
        const [from, to] = tier.range;
        const slice = entries.slice(from-1, to);
        if (!slice.length) return;
        html += `<div class="lb-tier"><div class="lb-tier-header ${tier.cls}"><span class="lb-tier-icon">${tier.icon}</span><span>${tier.label}</span></div><div class="lb-tier-entries">`;
        slice.forEach((e,i) => {
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
    if (myPos !== -1 && myName) {
        const tier = TIERS.find(t => myPos+1 >= t.range[0] && myPos+1 <= t.range[1]);
        yourPos.style.display = 'block';
        yourPos.innerHTML = `You are <strong>#${myPos+1}</strong>${tier?' · '+tier.icon+' '+tier.label:''} · <strong>${entries[myPos].score.toLocaleString()}</strong> pts`;
    } else {
        yourPos.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const lb = document.getElementById('lbPanel');
    if (lb && window.innerWidth > 899) lb.style.display = '';
    lbRender();
});
</script>

<?php include 'background-music.php'; ?>
</body>
</html>