// beginner/division.js - Division game
// Requires: js/base-game.js

class DivisionGame extends BaseGame {
    constructor() {
        super({
            modeKey:      'div',
            gameName:     'Division',
            skillLevel:   window.currentSkill || 'beginner',
            timePerQuestion: 30,
            bonusTime:    10,
            baseScore:    10,
            difficultyRanges: {
                beginner: { easy: 5,  medium: 7,  hard: 9  },
                advance:  { easy: 8,  medium: 10, hard: 12 },
                expert:   { easy: 12, medium: 15, hard: 20 }
            }
        });
    }

    generateQuestion() {
        this._beginQuestion();
        const skill  = this.skillLevel;
        const diff   = this.currentDifficulty;
        const ranges = this.difficultyRanges;
        let maxRange = (ranges[skill] && ranges[skill][diff]) ? ranges[skill][diff] : ranges['beginner'][diff];

        if (this.currentLevel > 5) maxRange += 2;
        if (this.currentLevel > 8) maxRange += 2;

        // Generate from answer backwards to guarantee whole-number division
        const divisor  = Math.floor(Math.random() * maxRange) + 1;
        const answer   = Math.floor(Math.random() * maxRange) + 1;
        const dividend = divisor * answer;
        const correctAnswer = answer;

        this.elements.question.innerText = `${dividend} ÷ ${divisor} = ?`;

        const variance = this.currentLevel < 3 ? 2 : (this.currentLevel < 6 ? 3 : 4);
        let wrong1 = correctAnswer + Math.floor(Math.random() * variance) + 1;
        let wrong2 = correctAnswer - Math.floor(Math.random() * variance) - 1;
        if (wrong2 < 1) wrong2 = correctAnswer + variance;
        while (wrong1 === correctAnswer) wrong1++;
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 += 2;

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Division Game Loaded');
    window.game = new DivisionGame();
    window.game.init();
});