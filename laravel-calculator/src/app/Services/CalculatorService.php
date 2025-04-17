<?php

namespace App\Services;

use App\Utils\MathExpressionValidator;
use App\Utils\ExpressionTreeBuilder;

class CalculatorService
{
    private string $expression;
    private array $expressionTree = [];
    private ExpressionTreeBuilder $treeBuilder;

    public function __construct()
    {
        $this->treeBuilder = new ExpressionTreeBuilder();
    }

    public function evaluate(string $expression): ?float
    {
        $this->expression = trim($expression);

        if (!MathExpressionValidator::isValid($this->expression)) {
            return null;
        }

        $this->expressionTree = $this->treeBuilder->build($this->expression);

        return $this->treeBuilder->evaluate($this->expressionTree);
    }

    public function getExpressionTree(): array
    {
        return $this->expressionTree;
    }
}