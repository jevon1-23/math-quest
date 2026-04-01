<?php
require_once 'config.php';
requireLogin();

$allowed_skills = ['beginner', 'advance', 'expert', 'grand-master'];
$skill = in_array($_GET['skill'] ?? '', $allowed_skills) ? $_GET['skill'] : 'beginner';

$games = [
    'beginner' => [
        ['mode' => 'add', 'name' => 'Addition', 'icon' => '➕'],
        ['mode' => 'subtract', 'name' => 'Subtraction', 'icon' => '➖'],
        ['mode' => 'multiply', 'name' => 'Multiplication', 'icon' => '✖️'],
        ['mode' => 'div', 'name' => 'Division', 'icon' => '➗']
    ],
    'advance' => [
        ['mode' => 'decimal', 'name' => 'Decimals', 'icon' => '🔢'],
        ['mode' => 'fractions', 'name' => 'Fractions', 'icon' => '½'],
        ['mode' => 'perimeter', 'name' => 'Perimeter', 'icon' => '📏'],
        ['mode' => 'rounding', 'name' => 'Rounding', 'icon' => '🔄']
    ],
    'expert' => [
        ['mode' => 'algebra', 'name' => 'Algebra', 'icon' => '✖️➕'],
        ['mode' => 'area', 'name' => 'Area', 'icon' => '📐'],
        ['mode' => 'factorization', 'name' => 'Factorization', 'icon' => '🔍'],
        ['mode' => 'percentage', 'name' => 'Percentage', 'icon' => '%'],
        ['mode' => 'simplifying-expressions', 'name' => 'Simplifying', 'icon' => '🔄']
    ],
    'grand-master' => [
        ['mode' => 'logarithms', 'name' => 'Logarithms', 'icon' => '📊'],
        ['mode' => 'pythagorean-theorem', 'name' => 'Pythagorean', 'icon' => '▲'],
        ['mode' => 'trigonometry', 'name' => 'Trigonometry', 'icon' => '📐']
    ]
];

$currentGames = $games[$skill];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest - Select Game</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="card">
    <div class="skill-header">
        <h1>🎮 Choose Your Game Mode</h1>
        <div class="skill-badge"><?php echo ucfirst($skill); ?> Level</div>
        <p>Select a math topic to start your adventure!</p>
    </div>

    <div class="games-grid">
        <?php foreach ($currentGames as $game): ?>
        <div class="game-card" onclick="startGame('<?php echo $game['mode']; ?>')">
            <div class="game-icon"><?php echo $game['icon']; ?></div>
            <div class="game-name"><?php echo $game['name']; ?></div>
            <div class="game-description">Click to play →</div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center">
        <a href="play.php" class="back-link">← Back to Skills</a>
    </div>
</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<script>
    function startGame(mode) {
        window.location.href = 'game-handler.php?skill=<?php echo $skill; ?>&mode=' + mode + '&diff=easy';
    }
</script>

<?php include 'background-music.php'; ?>
</body>
</html>