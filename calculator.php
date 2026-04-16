<?php
// calculator.php - Standalone colorful calculator
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator - Math Quest</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .calculator-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 25px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .calculator {
            background: #1a1a2e;
            border-radius: 25px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
        }

        .calculator-title {
            text-align: center;
            color: #ffd700;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .display {
            background: #0f0f1a;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,215,0,0.3);
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.3);
        }

        .calc-expr {
            color: #888;
            font-size: 0.9rem;
            min-height: 25px;
            word-break: break-all;
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .calc-val {
            color: #ffd700;
            font-size: 2rem;
            font-weight: bold;
            text-align: right;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            min-height: 50px;
        }

        .calc-buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .calc-btn {
            padding: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Segoe UI', sans-serif;
        }

        .calc-btn:active {
            transform: scale(0.95);
        }

        .calc-num {
            background: linear-gradient(135deg, #2d3561, #1a1f3a);
            color: white;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .calc-num:hover {
            background: linear-gradient(135deg, #3d4571, #2a2f4a);
            transform: translateY(-2px);
        }

        .calc-op {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }

        .calc-op:hover {
            background: linear-gradient(135deg, #f5a623, #f39c12);
            transform: translateY(-2px);
        }

        .calc-fn {
            background: linear-gradient(135deg, #8e44ad, #6c3483);
            color: white;
            font-size: 0.9rem;
        }

        .calc-fn:hover {
            background: linear-gradient(135deg, #9b59b6, #7d3c98);
            transform: translateY(-2px);
        }

        .calc-clr {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .calc-clr:hover {
            background: linear-gradient(135deg, #ec7063, #e74c3c);
            transform: translateY(-2px);
        }

        .calc-del {
            background: linear-gradient(135deg, #f1c40f, #d4ac0d);
            color: #1a1a2e;
        }

        .calc-del:hover {
            background: linear-gradient(135deg, #f4d03f, #f1c40f);
            transform: translateY(-2px);
        }

        .calc-eq {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            grid-column: span 2;
        }

        .calc-eq:hover {
            background: linear-gradient(135deg, #58d68d, #2ecc71);
            transform: translateY(-2px);
        }

        .calc-zero {
            grid-column: span 2;
        }

        .angle-toggle {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            font-size: 0.8rem;
            margin-top: 10px;
            width: 100%;
            padding: 10px;
        }

        .angle-toggle:hover {
            background: linear-gradient(135deg, #5dade2, #3498db);
            transform: translateY(-2px);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #ffd700;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }

        .back-link a:hover {
            color: #fff;
            text-shadow: 0 0 10px rgba(255,215,0,0.5);
        }

        @media (max-width: 480px) {
            .calculator {
                padding: 15px;
            }
            .calc-btn {
                padding: 12px;
                font-size: 1rem;
            }
            .calc-val {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="calculator-container">
        <div class="calculator">
            <div class="calculator-title">
                🧮 Scientific Calculator
            </div>
            
            <div class="display">
                <div class="calc-expr" id="calcExpr"></div>
                <div class="calc-val" id="calcVal">0</div>
            </div>

            <div class="calc-buttons">
                <!-- Row 1: Functions -->
                <button class="calc-btn calc-fn" onclick="calcFn('sin')">sin</button>
                <button class="calc-btn calc-fn" onclick="calcFn('cos')">cos</button>
                <button class="calc-btn calc-fn" onclick="calcFn('tan')">tan</button>
                <button class="calc-btn calc-fn" onclick="calcFn('log')">log</button>
                
                <!-- Row 2: More functions -->
                <button class="calc-btn calc-fn" onclick="calcFn('ln')">ln</button>
                <button class="calc-btn calc-fn" onclick="calcFn('√')">√</button>
                <button class="calc-btn calc-fn" onclick="calcInput('π')">π</button>
                <button class="calc-btn calc-fn" onclick="calcInput('^')">xʸ</button>
                
                <!-- Row 3: Clear and parentheses -->
                <button class="calc-btn calc-clr" onclick="calcClear()">C</button>
                <button class="calc-btn calc-del" onclick="calcDel()">⌫</button>
                <button class="calc-btn calc-op" onclick="calcInput('(')">(</button>
                <button class="calc-btn calc-op" onclick="calcInput(')')">)</button>
                
                <!-- Row 4: Numbers 7-9 and division -->
                <button class="calc-btn calc-num" onclick="calcInput('7')">7</button>
                <button class="calc-btn calc-num" onclick="calcInput('8')">8</button>
                <button class="calc-btn calc-num" onclick="calcInput('9')">9</button>
                <button class="calc-btn calc-op" onclick="calcInput('÷')">÷</button>
                
                <!-- Row 5: Numbers 4-6 and multiplication -->
                <button class="calc-btn calc-num" onclick="calcInput('4')">4</button>
                <button class="calc-btn calc-num" onclick="calcInput('5')">5</button>
                <button class="calc-btn calc-num" onclick="calcInput('6')">6</button>
                <button class="calc-btn calc-op" onclick="calcInput('×')">×</button>
                
                <!-- Row 6: Numbers 1-3 and subtraction -->
                <button class="calc-btn calc-num" onclick="calcInput('1')">1</button>
                <button class="calc-btn calc-num" onclick="calcInput('2')">2</button>
                <button class="calc-btn calc-num" onclick="calcInput('3')">3</button>
                <button class="calc-btn calc-op" onclick="calcInput('-')">−</button>
                
                <!-- Row 7: Zero, decimal, equals, addition -->
                <button class="calc-btn calc-num calc-zero" onclick="calcInput('0')">0</button>
                <button class="calc-btn calc-num" onclick="calcInput('.')">.</button>
                <button class="calc-btn calc-eq" onclick="calcEquals()">=</button>
                <button class="calc-btn calc-op" onclick="calcInput('+')">+</button>
            </div>

            <button class="calc-btn angle-toggle" id="angleToggle" onclick="calcToggleAngle()">
                📐 Mode: DEG
            </button>
        </div>
        
        <div class="back-link">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>

    <script>
        let calcExpression = '';
        let calcAngleDeg = true;

        function calcUpdateDisplay() {
            const exprEl = document.getElementById('calcExpr');
            const valEl = document.getElementById('calcVal');
            if (exprEl) exprEl.textContent = calcExpression || '';
            if (valEl) valEl.textContent = calcExpression || '0';
        }

        function calcInput(val) {
            calcExpression += val;
            calcUpdateDisplay();
        }

        function calcClear() {
            calcExpression = '';
            calcUpdateDisplay();
        }

        function calcDel() {
            calcExpression = calcExpression.slice(0, -1);
            calcUpdateDisplay();
        }

        function calcFn(fn) {
            calcExpression += fn + '(';
            calcUpdateDisplay();
        }

        function calcToggleAngle() {
            calcAngleDeg = !calcAngleDeg;
            const toggle = document.getElementById('angleToggle');
            if (toggle) toggle.textContent = '📐 Mode: ' + (calcAngleDeg ? 'DEG' : 'RAD');
        }

        function safeMathParse(raw) {
            let src = raw
                .replace(/×/g, '*')
                .replace(/÷/g, '/')
                .replace(/π/g, '3.14159265358979')
                .replace(/\^/g, '**')
                .trim();

            const toRad = v => calcAngleDeg ? v * Math.PI / 180 : v;
            let pos = 0;

            function peek() { return src[pos]; }
            function consume(ch) { if (src[pos] !== ch) throw new Error('Expected ' + ch); pos++; }

            function parseExpr() { return parseAddSub(); }

            function parseAddSub() {
                let v = parseMulDiv();
                while (pos < src.length && (peek() === '+' || peek() === '-')) {
                    const op = src[pos++];
                    const r = parseMulDiv();
                    v = op === '+' ? v + r : v - r;
                }
                return v;
            }

            function parseMulDiv() {
                let v = parsePow();
                while (pos < src.length && (peek() === '*' || peek() === '/')) {
                    const op = src[pos++];
                    if (op === '*' && peek() === '*') { pos++; v = Math.pow(v, parsePow()); continue; }
                    const r = parsePow();
                    v = op === '*' ? v * r : v / r;
                }
                return v;
            }

            function parsePow() { return parseUnary(); }

            function parseUnary() {
                if (peek() === '-') { pos++; return -parsePrimary(); }
                if (peek() === '+') { pos++; return parsePrimary(); }
                return parsePrimary();
            }

            function parsePrimary() {
                if (/[\d.]/.test(peek() || '')) {
                    let num = '';
                    while (pos < src.length && /[\d.]/.test(src[pos])) num += src[pos++];
                    return parseFloat(num);
                }
                if (peek() === '(') {
                    consume('(');
                    const v = parseExpr();
                    consume(')');
                    return v;
                }
                const rest = src.slice(pos);
                const fnMatch = rest.match(/^(sin|cos|tan|log|ln|sqrt|√|abs)/);
                if (fnMatch) {
                    const fn = fnMatch[1]; pos += fn.length;
                    consume('(');
                    const arg = parseExpr();
                    consume(')');
                    switch (fn) {
                        case 'sin': return Math.sin(toRad(arg));
                        case 'cos': return Math.cos(toRad(arg));
                        case 'tan': return Math.tan(toRad(arg));
                        case 'log': return Math.log10(arg);
                        case 'ln': return Math.log(arg);
                        case 'sqrt': case '√': return Math.sqrt(arg);
                        case 'abs': return Math.abs(arg);
                    }
                }
                throw new Error('Unexpected character: ' + peek());
            }

            const result = parseExpr();
            if (pos !== src.length) throw new Error('Unexpected token at pos ' + pos);
            return result;
        }

        function calcEquals() {
            if (!calcExpression) return;
            try {
                const result = safeMathParse(calcExpression);
                const rounded = Math.round(result * 1e10) / 1e10;
                const exprEl = document.getElementById('calcExpr');
                const valEl = document.getElementById('calcVal');
                if (exprEl) exprEl.textContent = calcExpression + ' =';
                if (valEl) valEl.textContent = isNaN(rounded) ? 'Error' : rounded;
                calcExpression = isNaN(rounded) ? '' : String(rounded);
            } catch(e) {
                const valEl = document.getElementById('calcVal');
                if (valEl) valEl.textContent = 'Error';
                calcExpression = '';
            }
        }
    </script>
</body>
</html>