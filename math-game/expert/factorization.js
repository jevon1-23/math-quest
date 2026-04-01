// expert/factorization.js - Prime factorization game
// Requires: js/base-game.js

class FactorizationGame extends BaseGame {
    constructor() {
        super({
            modeKey:         'factorization',
            gameName:        'Factorization',
            skillLevel:      'expert',
            timePerQuestion: 60,
            bonusTime:       20,
            baseScore:       25,
            achievementName: '🔢 Factor Master!',
            difficultyRanges: {
                easy:   { max: 50,  includePrime: false },
                medium: { max: 100, includePrime: true  },
                hard:   { max: 200, includePrime: true  }
            }
        });
    }

    _isPrime(num) {
        if (num < 2) return false;
        for (let i = 2; i * i <= num; i++) {
            if (num % i === 0) return false;
        }
        return true;
    }

    // Efficient: only loops to sqrt(n)
    _getPrimeFactors(num) {
        const factors = [];
        let n = num;
        for (let i = 2; i * i <= n; i++) {
            while (n % i === 0) { factors.push(i); n /= i; }
        }
        if (n > 1) factors.push(n);
        return factors;
    }

    _formatFactors(factors) {
        if (factors.length === 0) return '1';
        if (factors.length === 1) return factors[0].toString();
        const counts = {};
        factors.forEach(f => { counts[f] = (counts[f] || 0) + 1; });
        return Object.entries(counts)
            .map(([f, c]) => c === 1 ? f : `${f}^${c}`)
            .join(' × ');
    }

    generateQuestion() {
        this._beginQuestion();
        const range        = this.difficultyRanges[this.currentDifficulty];
        const includePrime = range.includePrime;

        let num;
        let attempts = 0;
        do {
            num = Math.floor(Math.random() * range.max) + 2;
            attempts++;
        } while (!includePrime && this._isPrime(num) && attempts < 100);

        const factors       = this._getPrimeFactors(num);
        const correctAnswer = this._formatFactors(factors);

        this.elements.question.innerText = `Prime factorization of ${num} = ?`;

        let wrong1, wrong2;

        if (factors.length === 1) {
            let w = num + 1;
            while (!this._isPrime(w)) w++;
            wrong1 = w.toString();
            wrong2 = (num * 2).toString();
        } else {
            const wf1 = [...factors]; wf1[0] = wf1[0] * 2; wf1.sort((a, b) => a - b);
            const wf2 = [...factors, 2].sort((a, b) => a - b);
            wrong1 = this._formatFactors(wf1);
            wrong2 = this._formatFactors(wf2);
            if (wrong1 === correctAnswer) wrong1 = this._formatFactors([...factors, 3]);
            if (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 = this._formatFactors([...factors, 5]);
        }

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Expert Factorization Game Loaded');
    window.game = new FactorizationGame();
    window.game.init();
});