<?php

namespace App\Utils;

class ExpressionTreeBuilder
{
    public function build(string $expression): array
    {
        $expression = trim($expression);


        if ($this->isWrappedInParentheses($expression)) {
            print_r("\n\nWrapped\n\n $expression");
            return $this->build(substr($expression, 1, -1));
        }

        $position = $this->findOperatorPosition($expression, ['+', '-']);
        if ($position !== false) {
            return $this->branchTree($expression, $position);
        }

        $position = $this->findOperatorPosition($expression, ['*', '/']);
        if ($position !== false) {
            return $this->branchTree($expression, $position);
        }

        $position = $this->findOperatorPosition($expression, ['^']);
        if ($position !== false) {
            return $this->branchTree($expression, $position);
        }

        return ['value' => (float) $expression];
    }

    public function evaluate(array $node): float
    {
        if (isset($node['value'])) {
            return $node['value'];
        }

        $left = $this->evaluate($node['left']);
        $right = $this->evaluate($node['right']);

        return match ($node['operator']) {
            '+' => $left + $right,
            '-' => $left - $right,
            '*' => $left * $right,
            '/' => $right == 0 ? throw new \Exception('Division by zero') : $left / $right,
            '^' => pow($left, $right),
            default => throw new \Exception('Unknown operator: ' . $node['operator']),
        };
    }

    private function branchTree(string $expression, int $position): array
    {
        $operator = $expression[$position];
        $leftExpr = substr($expression, 0, $position);
        $rightExpr = substr($expression, $position + 1);

        return [
            'operator' => $operator,
            'left' => $this->build($leftExpr),
            'right' => $this->build($rightExpr),
        ];
    }

    private function findOperatorPosition(string $expression, array $operators): int|bool
    {
        $openParentheses = 0;

        if (in_array('+', $operators) || in_array('-', $operators)) {
            for ($i = strlen($expression) - 1; $i >= 0; $i--) {
                $char = $expression[$i];

                if (preg_match("/^[\)\]\}]$/", $char)) {
                    $openParentheses++;
                } elseif (preg_match("/^[\(\[\{]$/", $char)) {
                    $openParentheses--;
                } elseif ($openParentheses === 0 && in_array($char, $operators)) {
                    if (($char === '+' || $char === '-') && ($i === 0 || in_array($expression[$i - 1], ['+', '-', '*', '/', '(', '[', '{', '^']))) {
                        continue;
                    }
                    return $i;
                }
            }
        } else {
            for ($i = 0; $i < strlen($expression); $i++) {
                $char = $expression[$i];

                if (preg_match("/^[\(\[\{]$/", $char)) {
                    $openParentheses++;
                } elseif (preg_match("/^[\)\]\}]$/", $char)) {
                    $openParentheses--;
                } elseif ($openParentheses === 0 && in_array($char, $operators)) {
                    return $i;
                }
            }
        }

        return false;
    }

    private function isWrappedInParentheses(string $expression): bool
    {
        $pattern = '/^(?x)                           
    (?(DEFINE)                            
        (?<BAL>                           
            \(                            
               (?: [^(){}\[\]]           
                | (?&BAL)                 
               )*
            \)                            
          | \[                            
               (?: [^(){}\[\]] | (?&BAL) )*
            \]
          | \{                            
               (?: [^(){}\[\]] | (?&BAL) )*
            \}
        )
    )
    (?&BAL)                                
$/x';

        return preg_match($pattern, $expression) === 1;
    }

    public function displayTree(array $node, int $depth = 0): string
    {
        $pad = str_repeat('  ', $depth);
        if (isset($node['value'])) {
            return $pad . 'Value: ' . $node['value'] . PHP_EOL;
        }

        $out  = $pad . 'Operator: ' . $node['operator'] . PHP_EOL;
        $out .= $this->displayTree($node['left'],  $depth + 1);
        $out .= $this->displayTree($node['right'], $depth + 1);
        return $out;
    }
}