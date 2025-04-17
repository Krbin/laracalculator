@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Advanced Calculator</div>

                    <div class="card-body">
                        <div class="mb-4">
                            <label for="expression" class="form-label">Enter Mathematical Expression</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="expression" placeholder="e.g., (2+3)*4-5^2">
                                <button class="btn btn-primary" id="calculate-btn">Calculate</button>
                                <button class="btn btn-secondary" id="clear-btn">Clear</button>
                            </div>
                            <small class="form-text text-muted">
                                Supports: +, -, *, / and ^ (power) | parentheses () in progress
                            </small>
                        </div>

                        <div id="result-container" class="mb-4 d-none">
                            <div class="card">
                                <div class="card-header">Result</div>
                                <div class="card-body">
                                    <h2 id="result" class="text-center"></h2>
                                </div>
                            </div>
                        </div>

                        <div id="error-container" class="mb-4 d-none">
                            <div class="alert alert-danger" role="alert">
                                <strong>Error:</strong> <span id="error-message"></span>
                            </div>
                        </div>

                        <div id="tree-container" class="mb-4 d-none">
                            <div class="card">
                                <div class="card-header">Expression Tree</div>
                                <div class="card-body">
                                    <pre id="expression-tree" class="bg-light p-3 rounded"></pre>
                                </div>
                            </div>
                        </div>

                        <div class="calculator-keypad mt-4">
                            <div class="row g-2 mb-2">
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="7">7</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="8">8</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="9">9</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="/">/</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="(">(</button></div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="4">4</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="5">5</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="6">6</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="*">*</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value=")">)</button></div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="1">1</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="2">2</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="3">3</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="-">-</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="^">^</button></div>
                            </div>
                            <div class="row g-2">
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="0">0</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value=".">.</button></div>
                                <div class="col"><button class="btn btn-outline-danger w-100"
                                        id="backspace-btn">⌫</button></div>
                                <div class="col"><button class="btn btn-outline-secondary w-100 calc-btn"
                                        data-value="+">+</button></div>
                                <div class="col"><button class="btn btn-success w-100" id="equals-btn">=</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const expressionInput = document.getElementById('expression');
            const resultContainer = document.getElementById('result-container');
            const resultDisplay = document.getElementById('result');
            const errorContainer = document.getElementById('error-container');
            const errorMessage = document.getElementById('error-message');
            const treeContainer = document.getElementById('tree-container');
            const expressionTree = document.getElementById('expression-tree');
            const calculateBtn = document.getElementById('calculate-btn');
            const clearBtn = document.getElementById('clear-btn');
            const equalsBtn = document.getElementById('equals-btn');
            const backspaceBtn = document.getElementById('backspace-btn');
            const calcButtons = document.querySelectorAll('.calc-btn');

            function calculate() {
                const expression = expressionInput.value.trim();

                if (!expression) {
                    showError('Please enter an expression');
                    return;
                }

                // Changed to use GET method with URL parameters
                const url = `{{ route('calculator.evaluate') }}?expression=${encodeURIComponent(expression)}`;

                fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideError();

                        if (data.success) {
                            resultDisplay.textContent = data.result;
                            resultContainer.classList.remove('d-none');

                            // Display the expression tree
                            treeContainer.classList.remove('d-none');
                            expressionTree.textContent = JSON.stringify(data.expressionTree, null, 2);
                        } else {
                            showError(data.error);
                        }
                    })
                    .catch(error => {
                        showError('An error occurred while processing your request');
                        console.error(error);
                    });
            }

            function showError(message) {
                errorMessage.textContent = message;
                errorContainer.classList.remove('d-none');
                resultContainer.classList.add('d-none');
                treeContainer.classList.add('d-none');
            }

            function hideError() {
                errorContainer.classList.add('d-none');
            }

            function clearCalculator() {
                expressionInput.value = '';
                resultContainer.classList.add('d-none');
                errorContainer.classList.add('d-none');
                treeContainer.classList.add('d-none');
                expressionInput.focus();
            }

            function appendToExpression(value) {
                expressionInput.value += value;
                expressionInput.focus();
            }

            function backspace() {
                expressionInput.value = expressionInput.value.slice(0, -1);
                expressionInput.focus();
            }

            // Event listeners
            calculateBtn.addEventListener('click', calculate);
            equalsBtn.addEventListener('click', calculate);
            clearBtn.addEventListener('click', clearCalculator);
            backspaceBtn.addEventListener('click', backspace);

            calcButtons.forEach(button => {
                button.addEventListener('click', () => {
                    appendToExpression(button.dataset.value);
                });
            });

            // Allow keyboard input
            expressionInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    calculate();
                }
            });
        });
    </script>
@endsection
