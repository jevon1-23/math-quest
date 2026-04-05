<?php
require_once 'config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest - Play Game</title>
    <link rel="stylesheet" href="style.css?v=2">
    <script>
        window.currentUserId = '<?php echo $_SESSION['user_id'] ?? 'guest'; ?>';
        localStorage.setItem('mathQuest_userId', window.currentUserId);
    </script>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="card">
    <h1>🎮 Choose Your Challenge</h1>
    <p>Select a skill level to begin your math journey!</p>

    <div class="skills-grid">
        <div class="skill-card beginner" onclick="window.location.href='game-selector.php?skill=beginner'">
            <div class="skill-icon">🌱</div>
            <div class="skill-title">Beginner</div>
            <div class="skill-description">Addition • Subtraction • Multiplication • Division</div>
        </div>
        <div class="skill-card advance" onclick="window.location.href='game-selector.php?skill=advance'">
            <div class="skill-icon">🚀</div>
            <div class="skill-title">Advance</div>
            <div class="skill-description">Decimals • Fractions • Perimeter • Rounding</div>
        </div>
        <div class="skill-card expert" onclick="window.location.href='game-selector.php?skill=expert'">
            <div class="skill-icon">⚡</div>
            <div class="skill-title">Expert</div>
            <div class="skill-description">Algebra • Area • Factorization • Percentage • Simplifying</div>
        </div>
        <div class="skill-card grand-master" onclick="window.location.href='game-selector.php?skill=grand-master'">
            <div class="skill-icon">👑</div>
            <div class="skill-title">Grand Master</div>
            <div class="skill-description">Logarithms • Pythagorean Theorem • Trigonometry</div>
        </div>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <a href="index.php" class="btn">← Back to Home</a>
    </div>
</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<?php include 'background-music.php'; ?>
<script src="/js/core/coin_sync.js"></script>
</body>
</html>