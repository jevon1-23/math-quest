<?php
require_once 'config.php';
requireLogin();  // This forces login if not logged in
?>
<?php
// level-selector.php - Shows level grid for a selected game mode

// ── Input validation ────────────────────────────────────────────────────────
$allowed_skills = ['beginner', 'advance', 'expert', 'grand-master'];
$allowed_modes  = [
    'beginner'     => ['add', 'subtract', 'multiply', 'div'],
    'advance'      => ['decimal', 'fractions', 'perimeter', 'rounding'],
    'expert'       => ['algebra', 'area', 'factorization', 'percentage', 'simplifying-expressions'],
    'grand-master' => ['logarithms', 'pythagorean-theorem', 'trigonometry'],
];
$allowed_diffs  = ['easy', 'medium', 'hard'];

$skill = in_array($_GET['skill'] ?? '', $allowed_skills) ? $_GET['skill'] : 'beginner';
$mode  = in_array($_GET['mode']  ?? '', $allowed_modes[$skill]) ? $_GET['mode']  : $allowed_modes[$skill][0];
$diff  = in_array($_GET['diff']  ?? '', $allowed_diffs)  ? $_GET['diff']  : 'easy';

$gameNames = [
    'add'                     => 'Addition',
    'subtract'                => 'Subtraction',
    'multiply'                => 'Multiplication',
    'div'                     => 'Division',
    'decimal'                 => 'Decimals',
    'fractions'               => 'Fractions',
    'perimeter'               => 'Perimeter',
    'rounding'                => 'Rounding',
    'algebra'                 => 'Algebra',
    'area'                    => 'Area',
    'factorization'           => 'Factorization',
    'percentage'              => 'Percentage',
    'simplifying-expressions' => 'Simplifying Expressions',
    'logarithms'              => 'Logarithms',
    'pythagorean-theorem'     => 'Pythagorean Theorem',
    'trigonometry'            => 'Trigonometry',
];

$gameName = $gameNames[$mode] ?? ucfirst($mode);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest — <?php echo htmlspecialchars($gameName); ?> Levels</title>
    <link rel="stylesheet" href="style.css?v=2">
    <style>
        /* ── level-selector.php ── */
        .game-header-info { text-align: center; margin-bottom: 30px; }
        .skill-path { display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom: 10px; color: #718096; }
        .skill-path a { color: #4299e1; text-decoration: none; }
        .skill-path a:hover { text-decoration: underline; }
        .current-game { font-size: 2rem; font-weight: bold; color: #2d3748; margin-bottom: 5px; }
        .levels-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 15px; margin: 30px 0; max-width: 600px; margin-left: auto; margin-right: auto; }
        .level-btn { aspect-ratio: 1; border: none; border-radius: 15px; font-size: 1.5rem; font-weight: bold; cursor: pointer; transition: transform 0.2s; display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .level-btn.unlocked { background: linear-gradient(135deg, #4299e1, #3182ce); color: white; }
        .level-btn.locked { background: #cbd5e0; color: #718096; cursor: not-allowed; opacity: 0.7; }
        .level-btn:hover:not(.locked) { transform: scale(1.1); }
        .level-number { font-size: 1.8rem; line-height: 1; }
        .level-stars { font-size: 0.8rem; margin-top: 5px; }
        .difficulty-selector { display: flex; justify-content: center; gap: 10px; margin: 20px 0; }
        .difficulty-btn { padding: 8px 16px; border: none; border-radius: 20px; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; color: white; }
        .difficulty-btn.easy { background: #48bb78; }
        .difficulty-btn.medium { background: #ecc94b; color: #1a202c; }
        .difficulty-btn.hard { background: #f56565; }
        .difficulty-btn.active { transform: scale(1.1); border: 3px solid #2d3748; }
        .back-link { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #718096; color: white; text-decoration: none; border-radius: 8px; }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="card">
    <div class="game-header-info">
        <div class="skill-path">
            <a href="play.php">Skills</a> ›
            <a href="game-selector.php?skill=<?php echo htmlspecialchars($skill); ?>"><?php echo htmlspecialchars(ucfirst($skill)); ?></a> ›
            <span><?php echo htmlspecialchars($gameName); ?></span>
        </div>
        <div class="current-game"><?php echo htmlspecialchars($gameName); ?></div>
        <p>Select a level to begin</p>
    </div>

    <!-- Difficulty selector -->
    <div class="difficulty-selector">
        <button onclick="changeDifficulty('easy')"   class="difficulty-btn easy   <?php echo $diff === 'easy'   ? 'active' : ''; ?>">Easy</button>
        <button onclick="changeDifficulty('medium')" class="difficulty-btn medium <?php echo $diff === 'medium' ? 'active' : ''; ?>">Medium</button>
        <button onclick="changeDifficulty('hard')"   class="difficulty-btn hard   <?php echo $diff === 'hard'   ? 'active' : ''; ?>">Hard</button>
    </div>

    <!--
        Level grid — rendered server-side as all UNLOCKED.
        JavaScript reads localStorage and locks levels that haven't been reached.
        This fixes the bug where PHP read $_COOKIE (never set) and always showed
        all levels as locked.
    -->
    <div class="levels-grid" id="levelsGrid">
        <?php for ($level = 1; $level <= 10; $level++): ?>
        <button
            class="level-btn unlocked"
            id="lvl<?php echo $level; ?>"
            onclick="goToLevel(<?php echo $level; ?>)">
            <span class="level-number"><?php echo $level; ?></span>
            <span class="level-stars" id="stars<?php echo $level; ?>"></span>
        </button>
        <?php endfor; ?>
    </div>

    <div style="text-align:center;">
        <a href="game-selector.php?skill=<?php echo htmlspecialchars($skill); ?>" class="back-link">← Back to Games</a>
    </div>
</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<script>
    var skill = <?php echo json_encode($skill); ?>;
    var mode  = <?php echo json_encode($mode);  ?>;
    var diff  = <?php echo json_encode($diff);  ?>;

    // Read progress from localStorage (where JS games save it)
    var storageKey    = 'mathQuest_' + skill + '_' + mode + '_' + diff;
    var unlockedLevel = parseInt(localStorage.getItem(storageKey) || '1', 10);

    for (var i = 1; i <= 10; i++) {
        var btn   = document.getElementById('lvl' + i);
        var stars = parseInt(localStorage.getItem(storageKey + '_level_' + i + '_stars') || '0', 10);

        // Apply star display
        var starsEl = document.getElementById('stars' + i);
        if (starsEl && stars > 0) starsEl.innerText = '⭐'.repeat(stars);

        // Lock levels beyond unlockedLevel
        if (i > unlockedLevel) {
            btn.classList.remove('unlocked');
            btn.classList.add('locked');
            btn.disabled = true;
        }
    }

    function goToLevel(level) {
        window.location.href = 'game-handler.php?skill=' + skill + '&mode=' + mode + '&diff=' + diff + '&level=' + level;
    }

    function changeDifficulty(newDiff) {
        window.location.href = 'level-selector.php?skill=' + skill + '&mode=' + mode + '&diff=' + newDiff;
    }
</script>

<?php include 'background-music.php'; ?>
</body>
</html>