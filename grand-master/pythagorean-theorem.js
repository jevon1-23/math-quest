// expert/pythagorean-theorem.js - Pythagorean theorem game
// Requires: js/base-game.js

class PythagoreanTheoremGame extends BaseGame {
    constructor() {
        super({
            modeKey:         'pythagorean-theorem',
            gameName:        'Pythagorean Theorem',
            skillLevel:      'expert',
            timePerQuestion: 60,
            bonusTime:       20,
            baseScore:       25,
            achievementName: '📐 Pythagoras Master!',
            difficultyRanges: {
                easy:   { max: 10, useTriples: false, decimals: 0 },
                medium: { max: 15, useTriples: true,  decimals: 1 },
                hard:   { max: 25, useTriples: true,  decimals: 2 }
            }
        });

        // Core triples only — scaled dynamically to control difficulty
        this._coreTriples = [[3,4,5],[5,12,13],[8,15,17],[7,24,25],[20,21,29]];
    }

    _round(val, decimals) {
        const factor = Math.pow(10, decimals);
        return Math.round(val * factor) / factor;
    }

    _pickTriple(maxVal) {
        for (let attempt = 0; attempt < 50; attempt++) {
            const triple = this._coreTriples[Math.floor(Math.random() * this._coreTriples.length)];
            const scale  = Math.floor(Math.random() * 3) + 1;
            const scaled = triple.map(v => v * scale);
            if (scaled[2] <= maxVal * 2) return scaled;
        }
        return [3, 4, 5]; // fallback
    }

    generateQuestion() {
        this._beginQuestion();
        const range      = this.difficultyRanges[this.currentDifficulty];
        const maxVal     = range.max;
        const useTriples = range.useTriples;
        const decimals   = range.decimals;

        const sideType = Math.floor(Math.random() * 3); // 0=hyp, 1=leg-a, 2=leg-b
        let correctAnswer, questionText;

        if (sideType === 0) {
            // Find hypotenuse c
            let a, b;
            if (useTriples) {
                const [ta, tb] = this._pickTriple(maxVal);
                a = ta; b = tb;
                correctAnswer = this._round(Math.sqrt(a * a + b * b), decimals);
            } else {
                a = Math.floor(Math.random() * maxVal) + 3;
                b = Math.floor(Math.random() * maxVal) + 3;
                correctAnswer = this._round(Math.sqrt(a * a + b * b), decimals);
            }
            questionText = `Find hypotenuse: a = ${a}, b = ${b}`;
        } else {
            // Find a leg — ensure c > b to avoid NaN from sqrt of negative
            let knownLeg, hyp;
            if (useTriples) {
                const triple = this._pickTriple(maxVal);
                if (sideType === 1) {
                    correctAnswer = triple[0];
                    knownLeg = triple[1]; hyp = triple[2];
                    questionText = `Find leg a: b = ${knownLeg}, c = ${hyp}`;
                } else {
                    correctAnswer = triple[1];
                    knownLeg = triple[0]; hyp = triple[2];
                    questionText = `Find leg b: a = ${knownLeg}, c = ${hyp}`;
                }
            } else {
                knownLeg = Math.floor(Math.random() * maxVal) + 3;
                // Guarantee hyp > knownLeg to avoid sqrt of negative
                hyp = knownLeg + Math.floor(Math.random() * maxVal) + 1;
                const val = Math.sqrt(hyp * hyp - knownLeg * knownLeg);
                correctAnswer = this._round(val, decimals);
                questionText = sideType === 1
                    ? `Find leg a: b = ${knownLeg}, c = ${hyp}`
                    : `Find leg b: a = ${knownLeg}, c = ${hyp}`;
            }
        }

        this.elements.question.innerText = questionText + ' = ?';

        const variance = 1 + this.currentLevel;
        let wrong1 = this._round(correctAnswer + Math.floor(Math.random() * variance) + 1, decimals);
        let wrong2 = this._round(correctAnswer - Math.floor(Math.random() * variance) - 1, decimals);
        if (wrong2 < 1) wrong2 = this._round(correctAnswer + variance, decimals);
        while (wrong1 === correctAnswer) wrong1 = this._round(wrong1 + 1, decimals);
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 = this._round(wrong2 + 2, decimals);

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Expert Pythagorean Theorem Game Loaded');
    window.game = new PythagoreanTheoremGame();
    window.game.init();
});