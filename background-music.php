<?php
// background-music.php - Persistent background music player
?>
<script>
(function() {
    if (window.persistentMusicPlayer) {
        const musicEnabled = localStorage.getItem('mq_music') !== 'false';
        if (!musicEnabled) {
            window.persistentMusicPlayer.pause();
        } else if (window.persistentMusicPlayer.audio.paused) {
            window.persistentMusicPlayer.play();
        }
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
    audio.preload = 'auto';

    let musicEnabled = localStorage.getItem('mq_music') !== 'false';

    const savedVolume = localStorage.getItem('bgMusicVolume');
    audio.volume = savedVolume ? parseInt(savedVolume) / 100 : 0.5;

    function loadTrack(index, resumeTime) {
        if (index >= playlist.length) index = 0;
        if (index < 0) index = playlist.length - 1;
        currentTrackIndex = index;
        audio.src = playlist[index].file;
        localStorage.setItem('currentTrackIndex', currentTrackIndex);

        if (resumeTime > 0) {
            audio.addEventListener('canplay', function onCanPlay() {
                audio.currentTime = resumeTime;
                audio.removeEventListener('canplay', onCanPlay);
                if (musicEnabled) audio.play().catch(() => waitForInteraction());
            });
        } else {
            if (musicEnabled) {
                audio.addEventListener('canplay', function onCanPlay() {
                    audio.removeEventListener('canplay', onCanPlay);
                    audio.play().catch(() => waitForInteraction());
                });
            }
        }
    }

    function nextTrack() {
        currentTime = 0;
        currentTrackIndex = (currentTrackIndex + 1) % playlist.length;
        localStorage.setItem('currentTrackIndex', currentTrackIndex);
        localStorage.setItem('currentMusicTime', '0');
        loadTrack(currentTrackIndex, 0);
    }

    function waitForInteraction() {
        const startOnInteraction = () => {
            if (!musicEnabled) return;
            audio.play().catch(() => {});
            document.removeEventListener('click',      startOnInteraction);
            document.removeEventListener('keydown',    startOnInteraction);
            document.removeEventListener('touchstart', startOnInteraction);
        };
        document.addEventListener('click',      startOnInteraction, { once: true });
        document.addEventListener('keydown',    startOnInteraction, { once: true });
        document.addEventListener('touchstart', startOnInteraction, { once: true });
    }

    function playMusic() {
        if (!musicEnabled) return;
        audio.play().catch(() => waitForInteraction());
    }

    audio.addEventListener('ended', () => nextTrack());

    // Save position every second for smooth resume
    setInterval(() => {
        if (!isNaN(audio.currentTime) && audio.currentTime > 0) {
            localStorage.setItem('currentMusicTime', audio.currentTime);
        }
    }, 1000);

    window.addEventListener('beforeunload', () => {
        if (!isNaN(audio.currentTime)) {
            localStorage.setItem('currentMusicTime', audio.currentTime);
        }
    });

    window.addEventListener('storage', (e) => {
        if (e.key === 'mq_music') {
            musicEnabled = e.newValue !== 'false';
            musicEnabled ? playMusic() : audio.pause();
        }
        if (e.key === 'bgMusicVolume') {
            audio.volume = parseInt(e.newValue) / 100;
        }
    });

    // Load and resume from saved position
    loadTrack(currentTrackIndex, currentTime);

    window.persistentMusicPlayer = {
        audio,
        play:            playMusic,
        pause:           () => audio.pause(),
        next:            nextTrack,
        setVolume:       (v) => { audio.volume = v / 100; },
        isPlaying:       () => !audio.paused,
        getCurrentTrack: () => playlist[currentTrackIndex],
    };

    console.log('🎵 Music Player ready');
})();
</script>