// expert/simplifying-expressions.js - Simplifying algebraic expressions game
// Requires: js/base-game.js

class SimplifyingExpressionsGame extends BaseGame {
    constructor() {
        super({
            modeKey:         'simplifying-expressions',
            gameName:        'Simplifying Expressions',
            skillLevel:      'expert',
            timePerQuestion: 60,
            bonusTime:       20,
            baseScore:       25,
            achievementName: '✂️ Simplify Master!',
            difficultyRanges: {
                easy:   { maxCoeff: 5,  maxConstant: 10, terms: 2, variables: ['x'] },
                medium: { maxCoeff: 8,  maxConstant: 15, terms: 3, variables: ['x', 'y'] },
                hard:   { maxCoeff: 12, maxConstant: 20, terms: 4, variables: ['x', 'y', 'z'] }
            }
        });
    }

    _gcd(a, b) {
        a = Math.abs(a); b = Math.abs(b);
        while (b) { const t = b; b = a % b; a = t; }
        return a || 1;
    }

    _formatTerm(coeff, variable, power = 1) {
        if (coeff === 0) return '';
        if (!variable || power === 0) return coeff.toString();
        const coeffStr = coeff === 1 ? '' : (coeff === -1 ? '-' : coeff.toString());
        const varStr   = power > 1 ? `${variable}^${power}` : variable;
        return coeffStr + varStr;
    }

    _combineTerms(terms) {
        const grouped = {};
        terms.forEach(t => {
            const key = (t.variable || '') + (t.power || 0);
            if (!grouped[key]) grouped[key] = { coeff: 0, variable: t.variable, power: t.power || 1 };
            grouped[key].coeff += t.coeff;
        });
        return Object.values(grouped).filter(t => t.coeff !== 0);
    }

    _expressionToString(terms) {
        if (!terms || terms.length === 0) return '0';
        terms = [...terms].sort((a, b) => {
            if (a.variable && !b.variable) return -1;
            if (!a.variable && b.variable) return 1;
            if (a.variable && b.variable) return (b.power || 1) - (a.power || 1);
            return 0;
        });

        let result = '';
        terms.forEach((term, i) => {
            const str = term.variable
                ? this._formatTerm(term.coeff, term.variable, term.power)
                : term.coeff.toString();

            if (i === 0) {
                result = str;
            } else if (term.coeff > 0) {
                result += ' + ' + str;
            } else if (term.coeff < 0) {
                result += ' - ' + this._formatTerm(Math.abs(term.coeff), term.variable, term.power);
            }
        });
        return result;
    }

    generateQuestion() {
        this._beginQuestion();
        const range       = this.difficultyRanges[this.currentDifficulty];
        const maxCoeff    = range.maxCoeff;
        const maxConstant = range.maxConstant;
        const numTerms    = range.terms;
        const variables   = range.variables;

        let expression, simplifiedTerms;

        if (this.currentLevel > 5 && Math.random() > 0.5) {
            // Distributive property: a(bx + c)
            const a   = Math.floor(Math.random() * 3) + 1;
            const b   = Math.floor(Math.random() * maxCoeff) + 1;
            const c   = Math.floor(Math.random() * maxConstant) + 1;
            const v   = variables[0];
            expression = `${a}(${b}${v} + ${c})`;
            simplifiedTerms = [
                { coeff: a * b, variable: v, power: 1 },
                { coeff: a * c, variable: null }
            ];
        } else {
            // Combine like terms
            const terms = [];
            for (let i = 0; i < numTerms; i++) {
                let coeff = Math.floor(Math.random() * maxCoeff * 2) - maxCoeff;
                if (coeff === 0) coeff = 1;
                const variable = variables[Math.floor(Math.random() * variables.length)];
                const power    = (this.currentDifficulty !== 'easy' && Math.random() > 0.7) ? 2 : 1;
                terms.push({ coeff, variable, power });
            }
            const numConst = Math.floor(numTerms / 2);
            for (let i = 0; i < numConst; i++) {
                let constant = Math.floor(Math.random() * maxConstant * 2) - maxConstant;
                if (constant === 0) constant = 1;
                terms.push({ coeff: constant, variable: null });
            }
            expression     = this._expressionToString(terms);
            simplifiedTerms = this._combineTerms(terms);
        }

        const correctAnswer = this._expressionToString(simplifiedTerms);
        this.elements.question.innerText = 'Simplify: ' + expression;

        // Generate wrong answers by shifting coefficients
        const shift = (delta) => this._expressionToString(
            simplifiedTerms
                .map(t => ({ ...t, coeff: t.coeff + delta }))
                .filter(t => t.coeff !== 0)
        );

        let wrong1 = shift(2);
        let wrong2 = shift(-1);
        if (wrong1 === correctAnswer) wrong1 = shift(3);
        if (wrong2 === correctAnswer || wrong2 === wrong1) wrong2 = shift(-3);

        this._setChoices(correctAnswer, wrong1, wrong2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Expert Simplifying Expressions Game Loaded');
    window.game = new SimplifyingExpressionsGame();
    window.game.init();
});