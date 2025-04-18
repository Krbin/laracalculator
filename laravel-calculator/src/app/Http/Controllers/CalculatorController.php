<?php

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

    /**
     * Display the calculator page
     */
    public function index()
    {
        return view('calculator.index');
    }

    /**
     * Process the calculation
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'expression' => 'required|string|max:255',
        ]);

        try {
            $expression = $request->input('expression');
            $result = $this->calculatorService->evaluate($expression);

            if ($result === null) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['expression' => 'Invalid mathematical expression']);
            }

            return view('calculator.index', [
                'expression' => $expression,
                'result' => $result,
                'expressionTree' => $this->calculatorService->getExpressionTree()
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['expression' => $e->getMessage()]);
        }
    }
}
