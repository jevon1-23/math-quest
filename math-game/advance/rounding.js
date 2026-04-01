// advance/rounding.js - Number rounding game
// Requires: js/base-game.js

class RoundingGame extends BaseGame {
    constructor() {
        super({
            modeKey:      'rounding',
            gameName:     'Rounding',
            skillLevel:   'advance',
            timePerQuestion: 30,
            bonusTime:    10,
            baseScore:    10,
            difficultyRanges: {
                easy:   { max: 100,   places: [10] },
                medium: { max: 1000,  places: [10, 100] },
                hard:   { max: 10000, places: [10, 100, 1000] }
            }
        });
    }

    generateQuestion() {
        this._beginQuestion();
        const range  = this.difficultyRanges[this.currentDifficulty];
        const num    = Math.floor(Math.random() * range.max) + 1;
        const places = range.places;
        const place  = places[Math.floor(Math.random() * places.length)];

        const correctAnswer = Math.round(num / place) * place;

        this.elements.question.innerText = `Round ${num} to the nearest ${place}`;

        let wrong1 = correctAnswer + place;
        let wrong2 = correctAnswer - place;
        if (wrong2 < 0) wrong2 = correctAnswer + place * 2;
        while (wrong1 === correctAnswer) wrong1 += place;
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 += place;

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Advance Rounding Game Loaded');
    window.game = new RoundingGame();
    window.game.init();
});