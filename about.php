<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest - About</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="card">
    <h1>📖 About Math Quest</h1>
    <p>Math Quest is an interactive math learning game designed to make practicing math fun and engaging for students of all levels.</p>
    <p>From beginner addition all the way up to grand master logarithms and trigonometry — earn stars, climb the leaderboard, and master every level!</p>
    <p>© 2026 Math Quest | Created by Jevon Andrews | Version 2.0</p>

    <h2>🗺️ How Levels Work</h2>
    <p>There are 45 levels per game mode, split into three zones with 3 Boss levels:</p>
    <ul style="text-align: left; margin: 1rem 0;">
        <li>🟢 Levels 1–10 — Easy (13 questions each)</li>
        <li>🟡 Levels 11–25 — Medium (13 questions) · 👹 Level 11 is a Boss (23 questions)</li>
        <li>🔴 Levels 26–45 — Hard (13 questions) · 👹 Levels 25 &amp; 45 are Bosses (23 questions)</li>
    </ul>
    <p>Complete a level with at least 1 star to unlock the next one.</p>

    <h2>⭐ Star System</h2>
    <p>Stars are earned based on correct answers. Thresholds differ for regular vs boss levels:</p>
    <ul style="text-align: left; margin: 1rem 0;">
        <li>Regular levels (13 questions): ⭐⭐⭐ 11–13 correct | ⭐⭐ 8–10 correct | ⭐ 4–7 correct | ☆☆☆ 3 or fewer</li>
        <li>Boss levels (23 questions): ⭐⭐⭐ 20–23 correct | ⭐⭐ 9–19 correct | ⭐ 6–8 correct | ☆☆☆ 5 or fewer</li>
    </ul>

    <h2>💰 Scoring</h2>
    <p>Points are awarded for every correct answer. The faster you answer, the bigger your speed bonus. Higher levels multiply your score — by Level 45 you earn roughly 7× more per correct answer than Level 1.</p>

    <h2>🏆 Leaderboard</h2>
    <p>Each game mode has its own leaderboard. Your username is automatically used when you're logged in — no setup needed. Your personal best score is saved, and only your highest score counts.</p>
    <p>Tiers: 🏆 Champion (1st) · 💎 Diamond (2–4) · 🥇 Gold (5–8) · 🥈 Silver (9–13) · 🥉 Bronze (14–20)</p>

    <h2>⏱️ Timer</h2>
    <p>Each question has a countdown timer. If time runs out the level ends — so stay sharp! The timer turns orange when time is running low and red when almost out.</p>

    <h2>🧮 Calculator</h2>
    <p>A built-in scientific calculator is available for Advance, Expert, and Grand Master modes. It supports sin, cos, tan, log, ln, √, powers, and π. You can switch between degree and radian mode for trig functions.</p>

    <h2>🎓 Skill Levels</h2>
    <ul style="text-align: left; margin: 1rem 0;">
        <li>🌱 Beginner — Addition, Subtraction, Multiplication, Division</li>
        <li>🚀 Advance — Decimals, Fractions, Perimeter, Rounding</li>
        <li>⚡ Expert — Algebra, Area, Factorization, Percentage, Simplifying</li>
        <li>👑 Grand Master — Logarithms, Pythagorean Theorem, Trigonometry</li>
    </ul>

    <a href="index.php"><button>Back to Home</button></a>
</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<?php include 'background-music.php'; ?>
</body>
</html>