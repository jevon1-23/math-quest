<?php
require_once 'config.php';
requireLogin();
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
    
    <div style="text-align: center; margin: 15px auto; padding: 10px; background: linear-gradient(135deg, #FFD700, #FFA500); border-radius: 50px; display: inline-block; width: auto;">
        <span style="font-size: 24px;">💰</span>
        <span id="coinCount" style="font-size: 28px; font-weight: bold; color: #fff;">0</span>
        <span style="font-size: 18px;">Coins</span>
    </div>
    
    <div class="feature-buttons">
        <a href="play.php" class="btn" style="padding: 12px 35px;">🎯 Play Game</a>
        <a href="shop.php" class="btn btn-shop" style="padding: 12px 35px;">🛒 Visit Shop</a>
        <a href="daily-rewards.php" class="btn btn-daily" style="padding: 12px 35px;">🎁 Daily Rewards</a>
        <a href="settings_fixed.php" class="btn" style="padding: 12px 35px;">⚙️ Settings</a>
    </div>
    
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
<script src="/js/coin_sync.js"></script>
</body>
</html>