<?php
// settings.php
require_once 'config.php';
requireLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quest — Settings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'nav.php'; ?>

<div class="card">
    <h1>⚙️ Settings</h1>

    <!-- Sound settings -->
    <div class="settings-section">
        <h3>🔊 Audio</h3>
        <div class="settings-row">
            <span>Game sounds</span>
            <label class="toggle">
                <input type="checkbox" id="soundToggle" checked onchange="toggleSound(this)">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="settings-row">
            <span>Music</span>
            <label class="toggle">
                <input type="checkbox" id="musicToggle" onchange="toggleMusic(this)">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="settings-row" id="musicVolumeRow">
            <span>Music volume</span>
            <div>
                <input type="range" id="musicVolumeSlider" min="0" max="100" value="50" oninput="setMusicVolume(this.value)">
                <span id="volumeValue">50%</span>
            </div>
        </div>
    </div>

    <!-- Game settings -->
    <div class="settings-section">
        <h3>🎮 Game</h3>
        <div class="settings-row">
            <span>Show fastest time</span>
            <label class="toggle">
                <input type="checkbox" id="fastestTimeToggle" checked onchange="saveSetting('showFastestTime', this.checked)">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="settings-row">
            <span>Animations</span>
            <label class="toggle">
                <input type="checkbox" id="animToggle" checked onchange="saveSetting('animations', this.checked)">
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>

    <!-- Account Settings -->
    <div class="settings-section">
        <h3>👤 Account</h3>
        <div class="settings-row">
            <span>Username</span>
            <span><?php echo htmlspecialchars($user['username']); ?></span>
        </div>
        <div class="settings-row">
            <span>Email</span>
            <span><?php echo htmlspecialchars($user['email']); ?></span>
        </div>
        <div class="settings-row">
            <span>Coins</span>
            <span>🪙 <?php echo number_format($user['coins']); ?></span>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="settings-section">
        <h3>🚪 Session</h3>
        <div>
            <form action="logout.php" method="POST">
                <button type="submit">🚪 Logout</button>
            </form>
            <p>Log out of your Math Quest account</p>
        </div>
    </div>

    <!-- About -->
    <div class="settings-section">
        <h3>📖 About</h3>
        <div>
            <p><strong>Math Quest</strong> is an interactive math learning game designed to make practicing math fun and engaging for students of all levels.</p>
            <p>From beginner addition all the way up to grand master logarithms and trigonometry — earn stars, climb the leaderboard, and master every level!</p>
            <p>© 2026 Math Quest | Created by Jevon Andrews</p>
            <span>Version 2.0</span>

            <div>
                <button onclick="toggleLogistics()" id="moreBtn">More… ▼</button>
            </div>

            <div id="logisticsPanel" style="display:none;">
                <div>
                    <h4>🗺️ How Levels Work</h4>
                    <p>There are <strong>45 levels</strong> per game mode, split into three zones with <strong>3 Boss levels</strong>:</p>
                    <ul>
                        <li>🟢 <strong>Levels 1–10</strong> — Easy (13 questions each)</li>
                        <li>🟡 <strong>Levels 11–25</strong> — Medium (13 questions) · 👹 Level 11 is a Boss (23 questions)</li>
                        <li>🔴 <strong>Levels 26–45</strong> — Hard (13 questions) · 👹 Levels 25 & 45 are Bosses (23 questions)</li>
                    </ul>
                    <p>Complete a level with at least <strong>1 star</strong> to unlock the next one.</p>
                </div>

                <div>
                    <h4>⭐ Star System</h4>
                    <p>Stars are earned based on correct answers. Thresholds differ for regular vs boss levels:</p>
                    <ul>
                        <li><strong>Regular levels (13 questions):</strong></li>
                        <li>⭐⭐⭐ 11–13 correct &nbsp;⭐⭐ 8–10 correct &nbsp;⭐ 4–7 &nbsp;☆☆☆ 3 or fewer</li>
                        <li><strong>Boss levels (23 questions):</strong></li>
                        <li>⭐⭐⭐ 20–23 correct &nbsp;⭐⭐ 9–19 correct &nbsp;⭐ 6–8 &nbsp;☆☆☆ — 5 or fewer</li>
                    </ul>
                </div>

                <div>
                    <h4>💰 Scoring</h4>
                    <p>Points are awarded for every correct answer. The faster you answer, the bigger your speed bonus. Higher levels multiply your score — by Level 45 you earn roughly <strong>7× more</strong> per correct answer than Level 1.</p>
                </div>

                <div>
                    <h4>🏆 Leaderboard</h4>
                    <p>Each game mode has its own leaderboard. Set your name in the leaderboard panel before playing. Your personal best score is saved — only your highest score counts.</p>
                    <p>Tiers: 🏆 Champion (1st) · 💎 Diamond (2–4) · 🥇 Gold (5–8) · 🥈 Silver (9–13) · 🥉 Bronze (14–20)</p>
                </div>

                <div>
                    <h4>⏱️ Timer</h4>
                    <p>Each question has a countdown timer. If time runs out the level ends — so stay sharp! The timer turns orange when time is running low and red when almost out.</p>
                </div>

                <div>
                    <h4>🧮 Calculator</h4>
                    <p>A built-in scientific calculator is available for <strong>Advance, Expert, and Grand Master</strong> modes. It supports sin, cos, tan, log, ln, √, powers, and π. You can switch between degree and radian mode for trig functions.</p>
                </div>

                <div>
                    <h4>🎓 Skill Levels</h4>
                    <ul>
                        <li>🌱 <strong>Beginner</strong> — Addition, Subtraction, Multiplication, Division</li>
                        <li>🚀 <strong>Advance</strong> — Decimals, Fractions, Perimeter, Rounding</li>
                        <li>⚡ <strong>Expert</strong> — Algebra, Area, Factorization, Percentage, Simplifying</li>
                        <li>👑 <strong>Grand Master</strong> — Logarithms, Pythagorean Theorem, Trigonometry</li>
                    </ul>
                </div>

                <button onclick="toggleLogistics()" id="lessBtn">Show less ▲</button>
            </div>
        </div>
    </div>

    <a href="index.php" class="btn">← Back to Home</a>
</div>

<footer>
    <p>© 2026 Math Quest | Created by Jevon Andrews</p>
</footer>

<script>
function toggleLogistics() {
    const panel = document.getElementById('logisticsPanel');
    const btn = document.getElementById('moreBtn');
    const open = panel.style.display === 'block';
    panel.style.display = open ? 'none' : 'block';
    if (btn) btn.style.display = open ? '' : 'none';
}

function loadSettings() {
    const sound = localStorage.getItem('mq_sound') !== 'false';
    const music = localStorage.getItem('mq_music') === 'true';
    const musicVolume = localStorage.getItem('bgMusicVolume') || '50';
    const fastest = localStorage.getItem('mq_showFastestTime') !== 'false';
    const anim = localStorage.getItem('mq_animations') !== 'false';
    
    const soundToggle = document.getElementById('soundToggle');
    const musicToggle = document.getElementById('musicToggle');
    const fastestToggle = document.getElementById('fastestTimeToggle');
    const animToggle = document.getElementById('animToggle');
    const volumeSlider = document.getElementById('musicVolumeSlider');
    const volumeValue = document.getElementById('volumeValue');
    const musicVolumeRow = document.getElementById('musicVolumeRow');
    
    if (soundToggle) soundToggle.checked = sound;
    if (musicToggle) musicToggle.checked = music;
    if (fastestToggle) fastestToggle.checked = fastest;
    if (animToggle) animToggle.checked = anim;
    
    if (volumeSlider) {
        volumeSlider.value = musicVolume;
        if (volumeValue) volumeValue.textContent = musicVolume + '%';
    }
    
    if (musicVolumeRow) {
        musicVolumeRow.style.display = music ? 'flex' : 'none';
    }
}

function toggleSound(el) {
    localStorage.setItem('mq_sound', el.checked);
}

function toggleMusic(el) {
    localStorage.setItem('mq_music', el.checked);
    const musicVolumeRow = document.getElementById('musicVolumeRow');
    if (musicVolumeRow) {
        musicVolumeRow.style.display = el.checked ? 'flex' : 'none';
    }
}

function setMusicVolume(value) {
    const volume = parseInt(value);
    const volumeValue = document.getElementById('volumeValue');
    if (volumeValue) volumeValue.textContent = volume + '%';
    localStorage.setItem('bgMusicVolume', volume);
}

function saveSetting(key, val) {
    localStorage.setItem('mq_' + key, val);
}

window.addEventListener('storage', function(e) {
    if (e.key === 'mq_music') {
        const musicToggle = document.getElementById('musicToggle');
        if (musicToggle) musicToggle.checked = e.newValue === 'true';
        const musicVolumeRow = document.getElementById('musicVolumeRow');
        if (musicVolumeRow) musicVolumeRow.style.display = e.newValue === 'true' ? 'flex' : 'none';
    }
    if (e.key === 'bgMusicVolume') {
        const volumeSlider = document.getElementById('musicVolumeSlider');
        const volumeValue = document.getElementById('volumeValue');
        if (volumeSlider) volumeSlider.value = e.newValue;
        if (volumeValue) volumeValue.textContent = e.newValue + '%';
    }
});

document.addEventListener('DOMContentLoaded', loadSettings);
</script>

<?php include 'background-music.php'; ?>
</body>
</html>