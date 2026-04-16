<?php
// background-music.php - Persistent background music player
?>
<script>
// Persistent Background Music Player - Music continues across all pages
(function() {
    // Check if music player already exists globally
    if (window.persistentMusicPlayer) {
        const musicEnabled = localStorage.getItem('mq_music') !== 'false'; // default ON
        if (!musicEnabled) {
            window.persistentMusicPlayer.pause();
        } else if (window.persistentMusicPlayer.audio.paused) {
            window.persistentMusicPlayer.play();
        }
        const hint = document.getElementById('musicHint');
        if (hint) hint.remove();
        return;
    }

    const playlist = [
        { name: 'Math Quest Theme', file: 'sounds/background-music.mp3' },
        { name: 'Adventure Theme',  file: 'sounds/adventure-music.mp3'  }
    ];

    let currentTrackIndex = parseInt(localStorage.getItem('currentTrackIndex') || '0', 10);
    let currentTime       = parseFloat(localStorage.getItem('currentMusicTime') || '0');

    const audio  = new Audio();
    audio.loop   = false;

    // Default music ON unless user explicitly turned it off
    let musicEnabled = localStorage.getItem('mq_music') !== 'false';

    const savedVolume = localStorage.getItem('bgMusicVolume');
    audio.volume = savedVolume ? parseInt(savedVolume) / 100 : 0.5;

    function loadTrack(index) {
        if (index >= playlist.length) index = 0;
        if (index < 0) index = playlist.length - 1;
        currentTrackIndex = index;
        audio.src = playlist[index].file;
        localStorage.setItem('currentTrackIndex', currentTrackIndex);
        if (currentTime > 0) {
            audio.addEventListener('loadedmetadata', function onMeta() {
                if (currentTime < audio.duration) audio.currentTime = currentTime;
                audio.removeEventListener('loadedmetadata', onMeta);
            });
        }
    }

    function nextTrack() {
        currentTime = 0;
        currentTrackIndex = (currentTrackIndex + 1) % playlist.length;
        loadTrack(currentTrackIndex);
        if (musicEnabled) audio.play().catch(() => {});
    }

    function playMusic() {
        if (!musicEnabled) return;
        audio.play().then(() => {
            const hint = document.getElementById('musicHint');
            if (hint) hint.remove();
        }).catch(() => {
            // Browser blocked autoplay — show click-to-play hint
            showMusicHint();
        });
    }

    function showMusicHint() {
        if (document.getElementById('musicHint')) return;
        const hint = document.createElement('div');
        hint.id = 'musicHint';
        hint.innerHTML = '🎵 Click anywhere to start music 🎵';
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
        `;
        hint.onclick = () => {
            audio.play().then(() => hint.remove()).catch(() => {});
        };
        document.body.appendChild(hint);

        // Also start on any user interaction with the page
        const startOnInteraction = () => {
            audio.play().then(() => {
                const h = document.getElementById('musicHint');
                if (h) h.remove();
            }).catch(() => {});
            document.removeEventListener('click',     startOnInteraction);
            document.removeEventListener('keydown',   startOnInteraction);
            document.removeEventListener('touchstart', startOnInteraction);
        };
        document.addEventListener('click',      startOnInteraction, { once: true });
        document.addEventListener('keydown',    startOnInteraction, { once: true });
        document.addEventListener('touchstart', startOnInteraction, { once: true });
    }

    // Loop: when a track ends, play the next one
    audio.addEventListener('ended', () => {
        currentTime = 0;
        nextTrack();
    });

    // Save position periodically and on unload
    setInterval(() => {
        if (!isNaN(audio.currentTime) && audio.currentTime > 0)
            localStorage.setItem('currentMusicTime', audio.currentTime);
    }, 5000);
    window.addEventListener('beforeunload', () => {
        if (!isNaN(audio.currentTime))
            localStorage.setItem('currentMusicTime', audio.currentTime);
    });

    // React to settings changes (e.g. user toggles music in Settings page)
    window.addEventListener('storage', (e) => {
        if (e.key === 'mq_music') {
            musicEnabled = e.newValue !== 'false';
            musicEnabled ? playMusic() : audio.pause();
        }
        if (e.key === 'bgMusicVolume') {
            audio.volume = parseInt(e.newValue) / 100;
        }
    });

    // Load and start
    loadTrack(currentTrackIndex);
    playMusic();

    // Expose global controls
    window.persistentMusicPlayer = {
        audio,
        play:            playMusic,
        pause:           () => audio.pause(),
        next:            nextTrack,
        setVolume:       (v) => { audio.volume = v / 100; },
        isPlaying:       () => !audio.paused,
        getCurrentTrack: () => playlist[currentTrackIndex],
    };

    console.log('🎵 Music Player ready — plays on repeat by default');
})();
</script>