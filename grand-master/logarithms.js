// expert/logarithms.js - Logarithms game
// Requires: js/base-game.js

class LogarithmsGame extends BaseGame {
    constructor() {
        super({
            modeKey:         'logarithms',
            gameName:        'Logarithms',
            skillLevel:      'expert',
            timePerQuestion: 60,
            bonusTime:       20,
            baseScore:       25,
            achievementName: '📊 Log Master!',
            difficultyRanges: {
                easy:   { bases: [10],              max: 100,   useNatural: false },
                medium: { bases: [10, 2, 5],        max: 1000,  useNatural: false },
                hard:   { bases: [10, 2, 5, 3, 'e'],max: 10000, useNatural: true  }
            }
        });

        // Exact lookup table — avoids floating point drift (e.g. Math.sin(PI) ≠ 0)
        this._logTable = {
            10: { 1: 0, 10: 1, 100: 2, 1000: 3, 10000: 4 },
            2:  { 1: 0, 2: 1, 4: 2, 8: 3, 16: 4, 32: 5, 64: 6 },
            5:  { 1: 0, 5: 1, 25: 2, 125: 3, 625: 4 },
            3:  { 1: 0, 3: 1, 9: 2, 27: 3, 81: 4 }
        };
    }

    _log(base, x) {
        if (base === 'e') return Math.log(x);
        return Math.log(x) / Math.log(base);
    }

    _formatBase(base) {
        if (base === 'e') return 'ln';
        return base === 10 ? 'log' : `log_${base}`;
    }

    generateQuestion() {
        this._beginQuestion();
        const range  = this.difficultyRanges[this.currentDifficulty];
        const bases  = range.bases;
        const base   = bases[Math.floor(Math.random() * bases.length)];
        const types  = ['basic', 'equation', 'property'];
        const type   = types[Math.floor(Math.random() * types.length)];

        let correctAnswer, questionText;

        switch (type) {
            case 'basic': {
                const power = Math.floor(Math.random() * 3) + 1;
                const arg   = base === 'e' ? Math.round(Math.exp(power)) : Math.pow(base, power);
                correctAnswer = power;
                questionText  = `${this._formatBase(base)}(${arg})`;
                break;
            }
            case 'equation': {
                const c = Math.floor(Math.random() * 3) + 1;
                if (base === 'e') {
                    correctAnswer = Math.round(Math.exp(c));
                    questionText  = `ln(x) = ${c}, find x`;
                } else {
                    correctAnswer = Math.pow(base, c);
                    questionText  = `${this._formatBase(base)}(x) = ${c}, find x`;
                }
                break;
            }
            case 'property': {
                const props = ['product', 'quotient', 'power'];
                const prop  = props[Math.floor(Math.random() * props.length)];
                const m = Math.floor(Math.random() * 5) + 2;
                const n = Math.floor(Math.random() * 4) + 2;
                switch (prop) {
                    case 'product':
                        correctAnswer = Math.round(this._log(base, m * n) * 100) / 100;
                        questionText  = `${this._formatBase(base)}(${m}) + ${this._formatBase(base)}(${n})`;
                        break;
                    case 'quotient':
                        correctAnswer = Math.round(this._log(base, m / n) * 100) / 100;
                        questionText  = `${this._formatBase(base)}(${m}) - ${this._formatBase(base)}(${n})`;
                        break;
                    case 'power':
                        correctAnswer = Math.round(this._log(base, Math.pow(m, n)) * 100) / 100;
                        questionText  = `${n} × ${this._formatBase(base)}(${m})`;
                        break;
                }
                break;
            }
        }

        correctAnswer = Math.round(correctAnswer * 100) / 100;
        this.elements.question.innerText = questionText + ' = ?';

        const variance = 1 + this.currentLevel;
        let wrong1 = Math.round((correctAnswer + Math.floor(Math.random() * variance) + 0.5) * 100) / 100;
        let wrong2 = Math.round((correctAnswer - Math.floor(Math.random() * variance) - 0.5) * 100) / 100;
        if (wrong2 < 0) wrong2 = correctAnswer + variance;
        while (wrong1 === correctAnswer) wrong1 += 1;
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 += 2;

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Expert Logarithms Game Loaded');
    window.game = new LogarithmsGame();
    window.game.init();
});