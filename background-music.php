<?php
// background-music.php - Persistent background music player
?>
<script>
// Persistent Background Music Player - Music continues across all pages
(function() {
    // Check if music player already exists globally
    if (window.persistentMusicPlayer) {
        console.log('Music player already exists, reusing...');
        
        // Just update the UI or do nothing - music continues playing
        window.persistentMusicPlayer.refreshHint = function() {
            // Remove any existing hints
            const hint = document.getElementById('musicHint');
            if (hint) hint.remove();
        };
        
        // Remove any stray hints
        window.persistentMusicPlayer.refreshHint();
        
        // Make sure we have the latest settings
        const musicEnabled = localStorage.getItem('mq_music') === 'true';
        if (!musicEnabled && window.persistentMusicPlayer.isPlaying) {
            window.persistentMusicPlayer.pause();
        } else if (musicEnabled && !window.persistentMusicPlayer.isPlaying && !window.persistentMusicPlayer.audio.paused === false) {
            window.persistentMusicPlayer.play();
        }
        
        return;
    }
    
    // Create persistent music player
    const playlist = [
        { name: 'Math Quest Theme', file: 'sounds/background-music.mp3' },
        { name: 'Adventure Theme', file: 'sounds/adventure-music.mp3' }
    ];
    
    let currentTrackIndex = parseInt(localStorage.getItem('currentTrackIndex') || '0', 10);
    let currentTime = parseFloat(localStorage.getItem('currentMusicTime') || '0', 10);
    
    const audio = new Audio();
    audio.loop = false;
    
    function loadTrack(index) {
        if (index >= playlist.length) index = 0;
        if (index < 0) index = playlist.length - 1;
        
        currentTrackIndex = index;
        audio.src = playlist[index].file;
        localStorage.setItem('currentTrackIndex', currentTrackIndex);
        
        // Restore previous position if within reasonable range
        if (currentTime > 0 && currentTime < audio.duration) {
            audio.currentTime = currentTime;
        }
        
        console.log('Loaded track:', playlist[index].name);
    }
    
    function nextTrack() {
        currentTrackIndex = (currentTrackIndex + 1) % playlist.length;
        currentTime = 0;
        loadTrack(currentTrackIndex);
        if (musicEnabled && !audio.paused) {
            audio.play().catch(() => {});
        }
    }
    
    function playMusic() {
        if (!musicEnabled) return;
        
        audio.play().then(() => {
            console.log('✓ Music playing:', playlist[currentTrackIndex].name);
            // Remove hint if exists
            const hint = document.getElementById('musicHint');
            if (hint) hint.remove();
        }).catch(() => {
            // Only show hint if music is enabled and not playing
            if (musicEnabled && audio.paused) {
                showMusicHint();
            }
        });
    }
    
    function showMusicHint() {
        const existingHint = document.getElementById('musicHint');
        if (existingHint) return;
        
        const hint = document.createElement('div');
        hint.id = 'musicHint';
        hint.innerHTML = '🎵 Click anywhere to enable background music 🎵';
        hint.style.cssText = `
            position: fixed;
            bottom: 80px;
            right: 20px;
            background: linear-gradient(135deg, #ffd700, #c9920a);
            color: #1a1a2e;
            padding: 12px 20px;
            border-radius: 50px;
            font-family: 'Nunito', sans-serif;
            font-weight: bold;
            font-size: 0.9rem;
            z-index: 10000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            cursor: pointer;
            animation: musicHintPulse 1s infinite;
        `;
        
        hint.onclick = () => {
            audio.play().then(() => {
                hint.remove();
            }).catch(() => {});
        };
        
        document.body.appendChild(hint);
        
        // Auto-remove after 8 seconds
        setTimeout(() => {
            const h = document.getElementById('musicHint');
            if (h) h.remove();
        }, 8000);
    }
    
    // Save current position before page unload
    window.addEventListener('beforeunload', function() {
        if (audio && !isNaN(audio.currentTime)) {
            localStorage.setItem('currentMusicTime', audio.currentTime);
        }
    });
    
    // Save position periodically
    setInterval(function() {
        if (audio && !isNaN(audio.currentTime) && audio.currentTime > 0) {
            localStorage.setItem('currentMusicTime', audio.currentTime);
        }
    }, 5000);
    
    // When song ends, play next
    audio.addEventListener('ended', function() {
        console.log('Song ended, playing next...');
        currentTime = 0;
        nextTrack();
        if (musicEnabled) {
            audio.play().catch(() => {});
        }
    });
    
    // Load saved settings
    let musicEnabled = localStorage.getItem('mq_music') === 'true';
    const savedVolume = localStorage.getItem('bgMusicVolume');
    if (savedVolume) {
        audio.volume = parseInt(savedVolume) / 100;
    } else {
        audio.volume = 0.5;
    }
    
    // Load first track
    loadTrack(currentTrackIndex);
    
    // Start playing if enabled
    if (musicEnabled) {
        playMusic();
    }
    
    // Listen for setting changes
    window.addEventListener('storage', function(e) {
        if (e.key === 'mq_music') {
            musicEnabled = e.newValue === 'true';
            if (musicEnabled) {
                playMusic();
            } else {
                audio.pause();
            }
        }
        
        if (e.key === 'bgMusicVolume') {
            audio.volume = parseInt(e.newValue) / 100;
        }
    });
    
    // Store player globally
    window.persistentMusicPlayer = {
        audio: audio,
        play: playMusic,
        pause: () => audio.pause(),
        next: nextTrack,
        setVolume: (v) => { audio.volume = v / 100; },
        isPlaying: () => !audio.paused,
        getCurrentTrack: () => playlist[currentTrackIndex],
        refreshHint: () => {
            const hint = document.getElementById('musicHint');
            if (hint) hint.remove();
        }
    };
    
    console.log('🎵 Persistent Music Player Initialized');
    console.log('📀 Playlist:', playlist.map(t => t.name).join(' → '));
    console.log('🎵 Music will continue across all pages without restarting!');
})();
</script>