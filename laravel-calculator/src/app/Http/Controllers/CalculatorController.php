<?php
// app/Http/Controllers/CalculatorController.php

namespace App\Http\Controllers;

use App\Services\CalculatorService;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    protected $calculatorService;

    public function __construct(CalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    public function index()
    {
        return view('calculator.index');
    }

    // Updated to handle GET requests
    public function evaluate(Request $request)
    {
        $expression = $request->query('expression');

        if (empty($expression)) {
            return response()->json([
                'success' => false,
                'error' => 'Expression is required'
            ]);
        }

        try {
            $result = $this->calculatorService->evaluate($expression);

            if ($result === null) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid mathematical expression'
                ]);
            }

            return response()->json([
                'success' => true,
                'result' => $result,
                'expressionTree' => $this->calculatorService->getExpressionTree()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
