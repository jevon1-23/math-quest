<?php
require_once 'config.php';
requireLogin();  // This forces login if not logged in
?>
<?php
// index.php - Home page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest - Home</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="card">
    <h1>🎮 Welcome to Math Quest!</h1>
    <p style="text-align: center; font-size: 1.1rem;">Test your math skills and become a Math Champion!</p>
    
    <!-- Main Action Buttons -->
    <div class="feature-buttons">
        <a href="play.php" class="btn" style="padding: 12px 35px; font-size: 1.1rem;">🎯 Play Game</a>
        <a href="shop.php" class="btn btn-shop" style="padding: 12px 35px; font-size: 1.1rem;">🛒 Visit Shop</a>
        <a href="daily-rewards.php" class="btn btn-daily" style="padding: 12px 35px; font-size: 1.1rem;">🎁 Daily Rewards</a>
        <a href="settings.php" class="btn" style="padding: 12px 35px; font-size: 1.1rem;">⚙️ Settings</a>
    </div>
    
    <!-- Info Section -->
    <div class="info-section">
        <div class="info-text">
            <p>✨ Complete levels, earn stars, and collect coins! ✨</p>
            <p>🏆 Unlock achievements, customize your avatar, and climb the leaderboards! 🏆</p>
        </div>
    </div>
</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<?php include 'background-music.php'; ?>
</body>
</html>