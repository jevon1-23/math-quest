// js/core/Animations.js
// Animation System for Math Quest

class AnimationSystem {
    constructor() {
        this.init();
    }
    
    // Confetti animation for perfect levels
    showConfetti(duration = 2000) {
        const colors = ['#ffd700', '#fbbf24', '#ff6b6b', '#4ecdc4', '#48bb78', '#ff9f4a'];
        const confettiCount = 150;
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.width = Math.random() * 8 + 4 + 'px';
            confetti.style.height = Math.random() * 8 + 4 + 'px';
            confetti.style.animationDuration = Math.random() * 2 + 2 + 's';
            confetti.style.animationDelay = Math.random() * 0.5 + 's';
            
            document.body.appendChild(confetti);
            
            setTimeout(() => {
                confetti.remove();
            }, duration);
        }
    }
    
    // Star earning animation
    showStarEarn(x, y) {
        const star = document.createElement('div');
        star.className = 'star-earn';
        star.innerHTML = '⭐';
        star.style.left = x + 'px';
        star.style.top = y + 'px';
        star.style.position = 'fixed';
        document.body.appendChild(star);
        
        setTimeout(() => {
            star.remove();
        }, 1000);
    }
    
    // Multiple stars animation
    showStarsEarned(count) {
        for (let i = 0; i < count; i++) {
            setTimeout(() => {
                const x = window.innerWidth / 2 + (Math.random() - 0.5) * 200;
                const y = window.innerHeight / 2;
                this.showStarEarn(x, y);
            }, i * 200);
        }
    }
    
    // Coin collection animation
    showCoinCollect(x, y, amount = 1) {
        const coin = document.createElement('div');
        coin.className = 'coin-float';
        coin.innerHTML = `+${amount} 🪙`;
        coin.style.left = x + 'px';
        coin.style.top = y + 'px';
        coin.style.position = 'fixed';
        document.body.appendChild(coin);
        
        setTimeout(() => {
            coin.remove();
        }, 1000);
    }
    
    // Button click animation
    animateButton(button) {
        if (!button) return;
        button.classList.add('btn-click');
        setTimeout(() => {
            button.classList.remove('btn-click');
        }, 200);
    }
    
    // Level complete celebration
    celebrateLevelComplete() {
        // Add celebration overlay
        const celebration = document.createElement('div');
        celebration.className = 'level-celebration';
        document.body.appendChild(celebration);
        
        // Show confetti
        this.showConfetti(2500);
        
        // Remove celebration overlay
        setTimeout(() => {
            celebration.remove();
        }, 1000);
    }
    
    // Achievement unlock animation
    animateAchievement(element) {
        if (!element) return;
        element.classList.add('achievement-unlock');
        setTimeout(() => {
            element.classList.remove('achievement-unlock');
        }, 500);
    }
    
    // Number pop animation (for score updates)
    animateNumber(element, start, end, duration = 500) {
        if (!element) return;
        
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const updateNumber = () => {
            current += increment;
            if (current >= end) {
                element.textContent = Math.floor(end);
                return;
            }
            element.textContent = Math.floor(current);
            requestAnimationFrame(updateNumber);
        };
        
        updateNumber();
    }
    
    // Slide in animation for new elements
    slideIn(element, direction = 'right', duration = 300) {
        if (!element) return;
        
        const directions = {
            right: { start: 'translateX(100%)', end: 'translateX(0)' },
            left: { start: 'translateX(-100%)', end: 'translateX(0)' },
            top: { start: 'translateY(-100%)', end: 'translateY(0)' },
            bottom: { start: 'translateY(100%)', end: 'translateY(0)' }
        };
        
        element.style.transform = directions[direction].start;
        element.style.transition = `transform ${duration}ms ease`;
        
        setTimeout(() => {
            element.style.transform = directions[direction].end;
        }, 10);
        
        setTimeout(() => {
            element.style.transition = '';
        }, duration + 10);
    }
    
    // Pulse animation
    pulse(element, scale = 1.1, duration = 300) {
        if (!element) return;
        
        element.style.transition = `transform ${duration}ms ease`;
        element.style.transform = `scale(${scale})`;
        
        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, duration);
        
        setTimeout(() => {
            element.style.transition = '';
        }, duration + 10);
    }
    
    // Shake animation for wrong answers
    shakeElement(element, intensity = 5) {
        if (!element) return;
        
        element.style.animation = 'shake 0.4s ease';
        setTimeout(() => {
            element.style.animation = '';
        }, 400);
    }
    
    // Floating text animation
    showFloatingText(text, x, y, color = '#ffd700') {
        const floating = document.createElement('div');
        floating.textContent = text;
        floating.style.position = 'fixed';
        floating.style.left = x + 'px';
        floating.style.top = y + 'px';
        floating.style.color = color;
        floating.style.fontSize = '1.2rem';
        floating.style.fontWeight = 'bold';
        floating.style.pointerEvents = 'none';
        floating.style.zIndex = '10000';
        floating.style.animation = 'floatUp 1s ease-out forwards';
        
        document.body.appendChild(floating);
        
        setTimeout(() => {
            floating.remove();
        }, 1000);
    }
    
    // Add floatUp keyframe if not exists
    addFloatUpAnimation() {
        if (!document.querySelector('#floatUpAnimation')) {
            const style = document.createElement('style');
            style.id = 'floatUpAnimation';
            style.textContent = `
                @keyframes floatUp {
                    0% {
                        transform: translateY(0);
                        opacity: 1;
                    }
                    100% {
                        transform: translateY(-50px);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    // Seasonal avatar animation
    animateSeasonalAvatar(avatarElement, season) {
        if (!avatarElement) return;
        
        const seasons = {
            halloween: 'halloween-glow',
            christmas: 'christmas-glow'
        };
        
        const animation = seasons[season];
        if (animation) {
            avatarElement.style.animation = `${animation} 2s infinite`;
        }
    }
    
    init() {
        this.addFloatUpAnimation();
        console.log('Animation System Loaded');
    }
}

// Initialize animation system
window.animations = new AnimationSystem();