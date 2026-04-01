// expert/algebra.js - Algebra equation solving game
// Requires: js/base-game.js

class AlgebraGame extends BaseGame {
    constructor() {
        super({
            modeKey:         'algebra',
            gameName:        'Algebra',
            skillLevel:      'expert',
            timePerQuestion: 60,
            bonusTime:       20,
            baseScore:       25,
            achievementName: '🔣 Algebra Master!',
            difficultyRanges: {
                easy:   { maxCoeff: 5,  maxConstant: 10, useNegatives: false, useFractions: false },
                medium: { maxCoeff: 8,  maxConstant: 15, useNegatives: true,  useFractions: false },
                hard:   { maxCoeff: 12, maxConstant: 20, useNegatives: true,  useFractions: true  }
            }
        });
    }

    generateQuestion() {
        this._beginQuestion();
        const range    = this.difficultyRanges[this.currentDifficulty];
        const maxCoeff = range.maxCoeff;
        const maxConst = range.maxConstant;

        // ax + b = c  →  x = (c - b) / a
        let a = Math.floor(Math.random() * maxCoeff) + 1;
        let b = Math.floor(Math.random() * maxConst) + 1;
        let x = Math.floor(Math.random() * 5) + 1;

        if (range.useNegatives && Math.random() > 0.5) b = -b;

        const c = a * x + b;
        const correctAnswer = x;

        // Display sign correctly (avoid "ax + -b")
        const bStr = b >= 0 ? `+ ${b}` : `- ${Math.abs(b)}`;
        this.elements.question.innerText = `Solve for x: ${a}x ${bStr} = ${c}`;

        const variance = this.currentLevel < 3 ? 1 : (this.currentLevel < 6 ? 2 : 3);
        let wrong1 = correctAnswer + variance;
        let wrong2 = correctAnswer - variance;
        if (wrong2 < 0) wrong2 = correctAnswer + variance + 1;
        while (wrong1 === correctAnswer) wrong1++;
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 += 2;

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Expert Algebra Game Loaded');
    window.game = new AlgebraGame();
    window.game.init();
});