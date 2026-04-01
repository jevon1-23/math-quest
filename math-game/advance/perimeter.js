// advance/perimeter.js - Perimeter calculation game
// Requires: js/base-game.js

class PerimeterGame extends BaseGame {
    constructor() {
        super({
            modeKey:      'perimeter',
            gameName:     'Perimeter',
            skillLevel:   'advance',
            timePerQuestion: 30,
            bonusTime:    10,
            baseScore:    10,
            difficultyRanges: {
                easy:   { max: 10 },
                medium: { max: 20 },
                hard:   { max: 30 }
            }
        });
    }

    generateQuestion() {
        this._beginQuestion();
        const maxVal  = this.difficultyRanges[this.currentDifficulty].max;
        const shapes  = ['square', 'rectangle', 'triangle'];
        const shape   = shapes[Math.floor(Math.random() * shapes.length)];

        let correctAnswer;

        if (shape === 'square') {
            const side = Math.floor(Math.random() * maxVal) + 1;
            correctAnswer = side * 4;
            this.elements.question.innerText = `Perimeter of a square with side = ${side}?`;
        } else if (shape === 'rectangle') {
            const l = Math.floor(Math.random() * maxVal) + 1;
            const w = Math.floor(Math.random() * maxVal) + 1;
            correctAnswer = 2 * (l + w);
            this.elements.question.innerText = `Perimeter of a rectangle — length = ${l}, width = ${w}?`;
        } else {
            const s1 = Math.floor(Math.random() * maxVal) + 1;
            const s2 = Math.floor(Math.random() * maxVal) + 1;
            const s3 = Math.floor(Math.random() * maxVal) + 1;
            correctAnswer = s1 + s2 + s3;
            this.elements.question.innerText = `Perimeter of a triangle with sides = ${s1}, ${s2}, ${s3}?`;
        }

        const variance = this.currentLevel < 3 ? 2 : (this.currentLevel < 6 ? 4 : 6);
        let wrong1 = correctAnswer + Math.floor(Math.random() * variance) + 1;
        let wrong2 = correctAnswer - Math.floor(Math.random() * variance) - 1;
        if (wrong2 < 1) wrong2 = correctAnswer + variance + 1;
        while (wrong1 === correctAnswer) wrong1++;
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 += 2;

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Advance Perimeter Game Loaded');
    window.game = new PerimeterGame();
    window.game.init();
});