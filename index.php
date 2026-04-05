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
    <style>
        .feature-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 14px 32px;
            font-size: 1.1rem;
            font-weight: bold;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-align: center;
            min-width: 160px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
        }
        
        .btn-shop {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
        }
        
        .btn-shop:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 152, 0, 0.4);
        }
        
        .btn-daily {
            background: linear-gradient(135deg, #9C27B0, #7B1FA2);
            color: white;
            box-shadow: 0 4px 15px rgba(156, 39, 176, 0.3);
        }
        
        .btn-daily:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(156, 39, 176, 0.4);
        }
        
        .btn-settings {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
        }
        
        .btn-settings:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(33, 150, 243, 0.4);
        }
        
        .coin-display-large {
            text-align: center;
            margin: 20px auto;
            padding: 15px 30px;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            border-radius: 60px;
            display: inline-block;
            width: auto;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        
        .coin-display-large span {
            font-size: 28px;
            font-weight: bold;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .info-section {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 20px;
            margin-top: 30px;
            text-align: center;
        }
        
        .info-text p {
            margin: 10px 0;
            font-size: 1rem;
        }
        
        .card {
            text-align: center;
            max-width: 800px;
            margin: 20px auto;
        }
        
        @media (max-width: 600px) {
            .feature-buttons {
                gap: 15px;
            }
            
            .btn {
                padding: 12px 24px;
                font-size: 0.95rem;
                min-width: 140px;
            }
            
            .coin-display-large span {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="card">
    <h1>🎮 Welcome to Math Quest!</h1>
    <p style="text-align: center; font-size: 1.1rem;">Test your math skills and become a Math Champion!</p>
    
    <!-- Coin Display — loaded directly from DB, no flash -->
    <div style="display: flex; justify-content: center;">
        <div class="coin-display-large">
            <span>💰</span>
            <span id="coinCount"><?php echo number_format(getUserCoins($_SESSION['user_id'])); ?></span>
            <span>Coins</span>
        </div>
    </div>
    
    <!-- Main Action Buttons - Spaced Out -->
    <div class="feature-buttons">
        <a href="play.php" class="btn btn-primary">🎯 Play Game</a>
        <a href="shop.php" class="btn btn-shop">🛒 Visit Shop</a>
        <a href="daily-rewards.php" class="btn btn-daily">🎁 Daily Rewards</a>
        <a href="profile.php" class="btn btn-profile">👤 My Profile</a>
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
<script src="/js/coin_sync.js"></script>
</body>
</html>