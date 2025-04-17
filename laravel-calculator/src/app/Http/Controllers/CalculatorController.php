<?php

namespace App\Http\Controllers;

use App\Services\CalculatorService;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    private $calculatorService;

    public function __construct(CalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    public function index()
    {
        return view('calculator.index');
    }

    public function calculate(Request $request)
    {
        $expression = $request->input('expression');

        try {
            $result = $this->calculatorService->evaluate($expression);
            $expressionTree = $this->calculatorService->getExpressionTree();

            return response()->json([
                'success' => true,
                'result' => $result,
                'expressionTree' => $expressionTree
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }
}