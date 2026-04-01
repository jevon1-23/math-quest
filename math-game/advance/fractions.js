// advance/fractions.js - Fractions arithmetic game
// Requires: js/base-game.js

class FractionsGame extends BaseGame {
    constructor() {
        super({
            modeKey:      'fractions',
            gameName:     'Fractions',
            skillLevel:   'advance',
            timePerQuestion: 30,
            bonusTime:    10,
            baseScore:    10,
            difficultyRanges: {
                easy:   { max: 8  },
                medium: { max: 12 },
                hard:   { max: 16 }
            }
        });
    }

    _gcd(a, b) {
        a = Math.abs(a);
        b = Math.abs(b);
        if (a === 0 && b === 0) return 1;
        while (b !== 0) { const t = b; b = a % b; a = t; }
        return a;
    }

    _fractionStr(num, den) {
        if (den === 1) return num.toString();
        return `${num}/${den}`;
    }

    generateQuestion() {
        this._beginQuestion();
        const maxVal = this.difficultyRanges[this.currentDifficulty].max;
        const ops    = ['+', '-', '×'];
        const op     = ops[Math.floor(Math.random() * ops.length)];

        let num1 = Math.floor(Math.random() * maxVal) + 1;
        let den1 = Math.floor(Math.random() * maxVal) + 2;
        let num2 = Math.floor(Math.random() * maxVal) + 1;
        let den2 = Math.floor(Math.random() * maxVal) + 2;

        // Keep fractions proper for higher levels
        if (this.currentLevel > 5) {
            if (num1 >= den1) num1 = den1 - 1;
            if (num2 >= den2) num2 = den2 - 1;
        }

        let ansNum, ansDen;

        if (op === '+') {
            ansNum = num1 * den2 + num2 * den1;
            ansDen = den1 * den2;
        } else if (op === '-') {
            if (num1 / den1 < num2 / den2) {
                [num1, den1, num2, den2] = [num2, den2, num1, den1];
            }
            ansNum = num1 * den2 - num2 * den1;
            ansDen = den1 * den2;
        } else {
            ansNum = num1 * num2;
            ansDen = den1 * den2;
        }

        const g = this._gcd(Math.abs(ansNum), ansDen);
        ansNum = Math.floor(ansNum / g);
        ansDen = Math.floor(ansDen / g);

        const correctAnswer = this._fractionStr(ansNum, ansDen);

        this.elements.question.innerText = `${num1}/${den1} ${op} ${num2}/${den2} = ?`;

        let w1Num = ansNum + Math.floor(Math.random() * 3) + 1;
        let w2Den = ansDen + Math.floor(Math.random() * 3) + 1;
        let wrong1 = this._fractionStr(w1Num, ansDen);
        let wrong2 = this._fractionStr(ansNum, w2Den);

        if (wrong1 === correctAnswer) wrong1 = this._fractionStr(ansNum + 3, ansDen);
        if (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 = this._fractionStr(ansNum, ansDen + 5);

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Advance Fractions Game Loaded');
    window.game = new FractionsGame();
    window.game.init();
});