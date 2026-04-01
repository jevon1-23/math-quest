// expert/trigonometry.js - Trigonometry game
// Requires: js/base-game.js

class TrigonometryGame extends BaseGame {
    constructor() {
        super({
            modeKey:         'trigonometry',
            gameName:        'Trigonometry',
            skillLevel:      'expert',
            timePerQuestion: 60,
            bonusTime:       20,
            baseScore:       25,
            achievementName: '📐 Trig Master!',
            difficultyRanges: {
                easy:   { angles: [0,30,45,60,90],                                                              functions: ['sin','cos','tan'], useRadians: false, useInverse: false },
                medium: { angles: [0,30,45,60,90,120,135,150,180],                                              functions: ['sin','cos','tan','csc','sec','cot'], useRadians: false, useInverse: true  },
                hard:   { angles: [0,30,45,60,90,120,135,150,180,210,225,240,270,300,315,330],                  functions: ['sin','cos','tan','csc','sec','cot'], useRadians: true,  useInverse: true  }
            }
        });

        // Exact trig lookup table — avoids floating point drift
        const S = { 0:0, 30:0.5, 45:0.707, 60:0.866, 90:1, 120:0.866, 135:0.707, 150:0.5, 180:0, 210:-0.5, 225:-0.707, 240:-0.866, 270:-1, 300:-0.866, 315:-0.707, 330:-0.5 };
        const C = { 0:1, 30:0.866, 45:0.707, 60:0.5, 90:0, 120:-0.5, 135:-0.707, 150:-0.866, 180:-1, 210:-0.866, 225:-0.707, 240:-0.5, 270:0, 300:0.5, 315:0.707, 330:0.866 };

        this._trigTable = {
            sin: S,
            cos: C,
            tan: Object.fromEntries(Object.keys(S).map(a => {
                const t = Number(a);
                if (t % 180 === 90) return [t, null]; // undefined
                const cosVal = C[t];
                return [t, cosVal === 0 ? null : Math.round((S[t] / cosVal) * 1000) / 1000];
            })),
            csc: Object.fromEntries(Object.keys(S).map(a => {
                const s = S[Number(a)];
                return [a, s === 0 ? null : Math.round((1 / s) * 1000) / 1000];
            })),
            sec: Object.fromEntries(Object.keys(C).map(a => {
                const c = C[Number(a)];
                return [a, c === 0 ? null : Math.round((1 / c) * 1000) / 1000];
            })),
            cot: Object.fromEntries(Object.keys(S).map(a => {
                const s = S[Number(a)];
                const c = C[Number(a)];
                return [a, s === 0 ? null : Math.round((c / s) * 1000) / 1000];
            }))
        };
    }

    // Safe lookup — returns null for undefined values (tan 90, etc.)
    _trigValue(func, angle) {
        const table = this._trigTable[func];
        return table ? (table[angle] !== undefined ? table[angle] : null) : null;
    }

    generateQuestion(retries = 0) {
        if (retries > 20) {
            // Fallback to a known-safe question
            this.elements.question.innerText = 'sin(30°) = ?';
            this._setChoices(0.5, 0.866, 0.707);
            this.correctAnswerPosition = 0;
            return;
        }

        this._beginQuestion();
        const range    = this.difficultyRanges[this.currentDifficulty];
        const angles   = range.angles;
        const funcs    = range.functions;
        const types    = ['basic', 'inverse', 'identity'];
        const type     = types[Math.floor(Math.random() * types.length)];

        let correctAnswer, questionText;

        switch (type) {
            case 'basic': {
                const func  = funcs[Math.floor(Math.random() * funcs.length)];
                const angle = angles[Math.floor(Math.random() * angles.length)];
                const val   = this._trigValue(func, angle);
                if (val === null) { this.generateQuestion(retries + 1); return; }
                correctAnswer = val;
                questionText  = range.useRadians && this.currentLevel > 5
                    ? `${func}(${(angle * Math.PI / 180).toFixed(2)} rad)`
                    : `${func}(${angle}°)`;
                break;
            }
            case 'inverse': {
                if (!range.useInverse) { this.generateQuestion(retries + 1); return; }
                const invFuncs = ['arcsin','arccos','arctan'];
                const fn = invFuncs[Math.floor(Math.random() * invFuncs.length)];
                const x  = (Math.floor(Math.random() * 8) + 1) / 10;
                switch (fn) {
                    case 'arcsin': correctAnswer = Math.round(Math.asin(x) * 180 / Math.PI); break;
                    case 'arccos': correctAnswer = Math.round(Math.acos(x) * 180 / Math.PI); break;
                    case 'arctan': correctAnswer = Math.round(Math.atan(x) * 180 / Math.PI); break;
                }
                questionText = `${fn}(${x}) in degrees`;
                break;
            }
            case 'identity': {
                const angle = angles[Math.floor(Math.random() * angles.length)];
                const sin   = this._trigValue('sin', angle);
                const cos   = this._trigValue('cos', angle);
                if (sin === null || cos === null) { this.generateQuestion(retries + 1); return; }
                if (Math.random() > 0.5) {
                    correctAnswer = 1; // sin²θ + cos²θ = 1
                    questionText  = `sin²(${angle}°) + cos²(${angle}°)`;
                } else {
                    correctAnswer = Math.round(cos * cos * 1000) / 1000;
                    questionText  = `1 - sin²(${angle}°)`;
                }
                break;
            }
        }

        correctAnswer = Math.round(correctAnswer * 1000) / 1000;
        this.elements.question.innerText = questionText + ' = ?';

        const variance = 0.1 + this.currentLevel * 0.05;
        let wrong1 = Math.round((correctAnswer + variance + Math.random() * 0.1) * 1000) / 1000;
        let wrong2 = Math.round((correctAnswer - variance - Math.random() * 0.1) * 1000) / 1000;
        if (wrong2 < -1) wrong2 = Math.round((correctAnswer + variance) * 1000) / 1000;
        while (wrong1 === correctAnswer) wrong1 = Math.round((wrong1 + 0.1) * 1000) / 1000;
        while (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 = Math.round((wrong2 - 0.1) * 1000) / 1000;

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Expert Trigonometry Game Loaded');
    window.game = new TrigonometryGame();
    window.game.init();
});