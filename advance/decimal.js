// advance/decimal.js - Decimal arithmetic game
// Requires: js/base-game.js

class DecimalGame extends BaseGame {
    constructor() {
        super({
            modeKey:      'decimal',
            gameName:     'Decimals',
            skillLevel:   'advance',
            timePerQuestion: 30,
            bonusTime:    10,
            baseScore:    10,
            difficultyRanges: {
                easy:   { min: 1, max: 10,  places: 1 },
                medium: { min: 1, max: 20,  places: 2 },
                hard:   { min: 1, max: 50,  places: 2 }
            }
        });
    }

    generateQuestion() {
        this._beginQuestion();
        const range  = this.difficultyRanges[this.currentDifficulty];
        const places = range.places;
        const ops    = ['+', '-', '×', '÷'];
        const op     = ops[Math.floor(Math.random() * ops.length)];

        let n1 = parseFloat((Math.random() * (range.max - range.min) + range.min).toFixed(places));
        let n2 = parseFloat((Math.random() * (range.max - range.min) + range.min).toFixed(places));

        let correctAnswer;

        if (op === '+') {
            correctAnswer = parseFloat((n1 + n2).toFixed(places));
        } else if (op === '-') {
            if (n1 < n2) [n1, n2] = [n2, n1];
            correctAnswer = parseFloat((n1 - n2).toFixed(places));
        } else if (op === '×') {
            correctAnswer = parseFloat((n1 * n2).toFixed(places));
        } else {
            // Division — regenerate n2 to avoid near-zero values
            n2 = parseFloat((Math.random() * (range.max - range.min) + range.min).toFixed(places));
            if (n2 === 0) n2 = 1;
            correctAnswer = parseFloat((n1 / n2).toFixed(places));
        }

        this.elements.question.innerText = `${n1} ${op} ${n2} = ?`;

        const variance = this.currentLevel < 3 ? 0.5 : (this.currentLevel < 6 ? 1 : 2);
        let wrong1 = parseFloat((correctAnswer + Math.random() * variance + 0.1).toFixed(places));
        let wrong2 = parseFloat((correctAnswer - Math.random() * variance - 0.1).toFixed(places));

        // Ensure wrong answers are distinct from correct and each other
        if (wrong2 < 0) wrong2 = parseFloat((correctAnswer + 0.5).toFixed(places));
        while (wrong1 === correctAnswer) wrong1 = parseFloat((wrong1 + 0.1).toFixed(places));
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 = parseFloat((wrong2 + 0.2).toFixed(places));

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Advance Decimal Game Loaded');
    window.game = new DecimalGame();
    window.game.init();
});