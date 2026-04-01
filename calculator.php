<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Calculator - Math Quest</title>
    <style>
        * {
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            margin: 0;
            padding: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .calculator {
            background: rgba(255,255,255,0.95);
            border-radius: 30px;
            padding: 20px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .score {
            background: #2196f3;
            color: white;
            padding: 15px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .display {
            background: #f0f0f0;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: right;
            font-size: 2rem;
            font-family: monospace;
            min-height: 80px;
            word-wrap: break-word;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .buttons {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }
        
        button {
            background: white;
            border: 1px solid #ddd;
            border-radius: 15px;
            padding: 15px 5px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        button:active {
            transform: scale(0.95);
            background: #e0e0e0;
        }
        
        button.special {
            background: #ff9800;
            color: white;
            border: none;
        }
        
        button.equals {
            background: #4caf50;
            color: white;
            border: none;
        }
        
        button.clear {
            background: #f44336;
            color: white;
            border: none;
        }
        
        .mode {
            margin-top: 20px;
            padding: 12px;
            background: #e0e0e0;
            border-radius: 15px;
            text-align: center;
            font-weight: bold;
        }
        
        @media (max-width: 480px) {
            .calculator {
                padding: 15px;
            }
            
            .buttons {
                gap: 8px;
            }
            
            button {
                padding: 12px 3px;
                font-size: 1rem;
            }
            
            .display {
                font-size: 1.5rem;
                padding: 15px;
            }
            
            .score {
                font-size: 1rem;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="calculator">
        <div class="score">
            🧮 Score: 0 | ⭐ Stars: 0 | 💰 Coins: 0
        </div>
        
        <div class="display" id="display">0</div>
        
        <div class="buttons">
            <button class="special">sin</button>
            <button class="special">cos</button>
            <button class="special">tan</button>
            <button class="special">log</button>
            <button class="special">ln</button>
            <button class="special">√</button>
            <button class="special">π</button>
            <button class="special">xⁿ</button>
            <button class="clear">C</button>
            <button class="clear">CE</button>
            <button>7</button>
            <button>8</button>
            <button>9</button>
            <button>×</button>
            <button>÷</button>
            <button>4</button>
            <button>5</button>
            <button>6</button>
            <button>+</button>
            <button>-</button>
            <button>1</button>
            <button>2</button>
            <button>3</button>
            <button class="equals">=</button>
            <button>.</button>
            <button>0</button>
            <button>00</button>
            <button>(</button>
            <button>)</button>
        </div>
        
        <div class="mode">
            Mode: DEG | Answer: 0
        </div>
    </div>
    
    <script>
        let display = document.getElementById('display');
        let currentInput = '';
        
        document.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                let value = btn.textContent;
                
                if (value === 'C') {
                    currentInput = '';
                    display.textContent = '0';
                } else if (value === 'CE') {
                    currentInput = currentInput.slice(0, -1);
                    display.textContent = currentInput || '0';
                } else if (value === '=') {
                    try {
                        let result = eval(currentInput.replace('×', '*').replace('÷', '/'));
                        display.textContent = result;
                        currentInput = result.toString();
                    } catch(e) {
                        display.textContent = 'Error';
                        currentInput = '';
                    }
                } else {
                    if (currentInput === '0' && value !== '.') {
                        currentInput = value;
                    } else {
                        currentInput += value;
                    }
                    display.textContent = currentInput;
                }
            });
        });
    </script>
</body>
</html>