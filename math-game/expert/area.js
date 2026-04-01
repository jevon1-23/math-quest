// expert/area.js - Area calculation game
// Requires: js/base-game.js

class AreaGame extends BaseGame {
    constructor() {
        super({
            modeKey:         'area',
            gameName:        'Area',
            skillLevel:      'expert',
            timePerQuestion: 45,
            bonusTime:       15,
            baseScore:       20,
            achievementName: '📐 Geometry Master!',
            difficultyRanges: {
                easy:   { max: 10, shapes: ['square', 'rectangle', 'circle'] },
                medium: { max: 15, shapes: ['square', 'rectangle', 'circle', 'triangle'] },
                hard:   { max: 20, shapes: ['square', 'rectangle', 'circle', 'triangle', 'trapezoid'] }
            }
        });
    }

    generateQuestion() {
        this._beginQuestion();
        const range  = this.difficultyRanges[this.currentDifficulty];
        const maxVal = range.max;
        const shapes = range.shapes;
        const shape  = shapes[Math.floor(Math.random() * shapes.length)];

        let correctAnswer, questionText;

        switch (shape) {
            case 'square': {
                const side = Math.floor(Math.random() * maxVal) + 1;
                correctAnswer = side * side;
                questionText = `Area of square with side = ${side}`;
                break;
            }
            case 'rectangle': {
                const l = Math.floor(Math.random() * maxVal) + 1;
                const w = Math.floor(Math.random() * maxVal) + 1;
                correctAnswer = l * w;
                questionText = `Area of rectangle — length = ${l}, width = ${w}`;
                break;
            }
            case 'circle': {
                const r = Math.floor(Math.random() * maxVal) + 1;
                correctAnswer = Math.round(3.14 * r * r);
                questionText = `Area of circle with radius = ${r} (π ≈ 3.14)`;
                break;
            }
            case 'triangle': {
                const base = Math.floor(Math.random() * maxVal) + 1;
                const h    = Math.floor(Math.random() * maxVal) + 1;
                correctAnswer = Math.round((base * h) / 2);
                questionText = `Area of triangle — base = ${base}, height = ${h}`;
                break;
            }
            case 'trapezoid': {
                const b1 = Math.floor(Math.random() * maxVal) + 1;
                const b2 = Math.floor(Math.random() * maxVal) + 1;
                const h  = Math.floor(Math.random() * maxVal) + 1;
                correctAnswer = Math.round(((b1 + b2) * h) / 2);
                questionText = `Area of trapezoid — bases = ${b1}, ${b2}, height = ${h}`;
                break;
            }
        }

        this.elements.question.innerText = questionText + '?';

        const variance = this.currentLevel < 3 ? 3 : (this.currentLevel < 6 ? 6 : 10);
        let wrong1 = correctAnswer + Math.floor(Math.random() * variance) + 2;
        let wrong2 = correctAnswer - Math.floor(Math.random() * variance) - 2;
        if (wrong2 < 1) wrong2 = correctAnswer + variance;
        while (wrong1 === correctAnswer) wrong1 += 2;
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 += 3;

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Expert Area Game Loaded');
    window.game = new AreaGame();
    window.game.init();
});