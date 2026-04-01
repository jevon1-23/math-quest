// beginner/addition.js - Addition game
// Requires: js/core/BaseGame.js, js/core/GameInitializer.js

class AdditionGame extends BaseGame {
    constructor() {
        super({
            modeKey      : 'add',
            gameName     : 'Addition',
            skillLevel   : window.currentSkill || 'beginner',
            timePerQuestion: 30,
            bonusTime    : 10,
            baseScore    : 10,
            difficultyRanges: {
                beginner: { easy: 10,  medium: 15,  hard: 20  },
                advance : { easy: 25,  medium: 40,  hard: 50  },
                expert  : { easy: 50,  medium: 75,  hard: 100 }
            }
        });
    }

    generateQuestion() {
        this._beginQuestion();
        const skill    = this.skillLevel;
        const diff     = this.currentDifficulty;
        const ranges   = this.difficultyRanges;
        let maxRange   = (ranges[skill] && ranges[skill][diff]) ? ranges[skill][diff] : ranges['beginner'][diff];

        let n1 = Math.floor(Math.random() * maxRange) + 1;
        let n2 = Math.floor(Math.random() * maxRange) + 1;

        if (this.currentLevel > 5) {
            n1 += Math.floor(maxRange * 0.3);
            n2 += Math.floor(maxRange * 0.2);
        }

        const correctAnswer = n1 + n2;
        this.elements.question.innerText = `${n1} + ${n2} = ?`;

        const variance = this.currentLevel < 3 ? 2 : (this.currentLevel < 6 ? 4 : 6);
        let wrong1 = correctAnswer + Math.floor(Math.random() * variance) + 1;
        let wrong2 = correctAnswer - Math.floor(Math.random() * variance) - 1;
        if (wrong2 < 0) wrong2 = correctAnswer + variance;
        while (wrong1 === correctAnswer) wrong1++;
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 += 2;

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Addition Game Loaded');
    window.game = new AdditionGame();
    window.game.init();
});