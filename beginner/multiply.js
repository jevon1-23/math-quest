// beginner/multiplication.js - Multiplication game
// Requires: js/core/BaseGame.js, js/core/GameInitializer.js

class MultiplicationGame extends BaseGame {
    constructor() {
        super({
            modeKey      : 'multiply',
            gameName     : 'Multiplication',
            skillLevel   : window.currentSkill || 'beginner',
            timePerQuestion: 30,
            bonusTime    : 10,
            baseScore    : 10,
            difficultyRanges: {
                beginner: { easy: 5,  medium: 8,  hard: 12 },
                advance : { easy: 10, medium: 15, hard: 20 },
                expert  : { easy: 15, medium: 20, hard: 25 }
            }
        });
    }

    generateQuestion() {
        this._beginQuestion();
        const skill    = this.skillLevel;
        const diff     = this.currentDifficulty;
        const ranges   = this.difficultyRanges;
        let maxRange   = (ranges[skill] && ranges[skill][diff]) ? ranges[skill][diff] : ranges['beginner'][diff];

        if (this.currentLevel > 5) maxRange += 2;
        if (this.currentLevel > 8) maxRange += 2;

        const n1 = Math.floor(Math.random() * maxRange) + 1;
        const n2 = Math.floor(Math.random() * maxRange) + 1;
        const correctAnswer = n1 * n2;

        this.elements.question.innerText = `${n1} × ${n2} = ?`;

        const variance = this.currentLevel < 3 ? 2 : (this.currentLevel < 6 ? 5 : 10);
        let wrong1 = correctAnswer + Math.floor(Math.random() * variance) + 1;
        let wrong2 = correctAnswer - Math.floor(Math.random() * variance) - 1;
        if (wrong2 < 0) wrong2 = correctAnswer + variance;
        while (wrong1 === correctAnswer) wrong1++;
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 += 2;

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Multiplication Game Loaded');
    window.game = new MultiplicationGame();
    window.game.init();
});