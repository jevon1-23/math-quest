// expert/percentage.js - Percentage calculation game
// Requires: js/base-game.js

class PercentageGame extends BaseGame {
    constructor() {
        super({
            modeKey:         'percentage',
            gameName:        'Percentage',
            skillLevel:      'expert',
            timePerQuestion: 45,
            bonusTime:       15,
            baseScore:       20,
            achievementName: '% Percent Pro!',
            difficultyRanges: {
                easy:   { max: 100, percentageMax: 50  },
                medium: { max: 200, percentageMax: 75  },
                hard:   { max: 500, percentageMax: 100 }
            }
        });
    }

    generateQuestion() {
        this._beginQuestion();
        const range      = this.difficultyRanges[this.currentDifficulty];
        const maxVal     = range.max;
        const percentMax = range.percentageMax;
        // Question types restricted by difficulty:
        // easy   → only "x% of y" (straightforward)
        // medium → "x% of y" and "x is what % of y"
        // hard   → all three including % change
        const typesByDiff = {
            easy:   ['percentOf'],
            medium: ['percentOf', 'whatPercent'],
            hard:   ['percentOf', 'whatPercent', 'percentChange']
        };
        const types = typesByDiff[this.currentDifficulty] || typesByDiff['easy'];
        const type  = types[Math.floor(Math.random() * types.length)];

        let correctAnswer, questionText;

        switch (type) {
            case 'percentOf': {
                const pct = Math.floor(Math.random() * percentMax) + 1;
                const num = Math.floor(Math.random() * maxVal) + 1;
                correctAnswer = Math.round((pct / 100) * num);
                questionText  = `${pct}% of ${num}`;
                break;
            }
            case 'whatPercent': {
                const part  = Math.floor(Math.random() * maxVal) + 1;
                const whole = part + Math.floor(Math.random() * maxVal) + 1;
                correctAnswer = Math.round((part / whole) * 100);
                questionText  = `${part} is what % of ${whole}`;
                break;
            }
            case 'percentChange': {
                const original = Math.floor(Math.random() * maxVal) + 10;
                let   newVal;
                if (Math.random() > 0.5) {
                    newVal = original + Math.floor(Math.random() * original * 0.5) + 1;
                } else {
                    newVal = Math.max(1, original - Math.floor(Math.random() * original * 0.3) - 1);
                }
                correctAnswer = Math.round(((newVal - original) / original) * 100);
                // Show % suffix so negative values are unambiguous
                questionText  = `% change from ${original} to ${newVal}`;
                break;
            }
        }

        this.elements.question.innerText = questionText + ' = ?';

        const variance = this.currentLevel < 3 ? 5 : (this.currentLevel < 6 ? 10 : 15);
        let wrong1 = correctAnswer + Math.floor(Math.random() * variance) + 2;
        let wrong2 = correctAnswer - Math.floor(Math.random() * variance) - 2;
        if (wrong2 < -100) wrong2 = correctAnswer + variance;
        while (wrong1 === correctAnswer) wrong1 += 3;
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 -= 3;

        // Append % to all choices for clarity
        this._setChoices(correctAnswer + '%', wrong1 + '%', wrong2 + '%');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Expert Percentage Game Loaded');
    window.game = new PercentageGame();
    window.game.init();
});