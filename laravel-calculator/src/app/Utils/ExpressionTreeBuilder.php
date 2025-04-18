<?php

namespace App\Utils;

class ExpressionTreeBuilder
{
    public function build(string $expression): array
    {
        $expression = trim($expression);

        // handle unary functions sin, cos, tan, log, ln

        $function_pattern = '/^(?x)
        (?(DEFINE)
            (?<EXPR>
                (?: [^(){}\[\]]+ | \( (?&EXPR) \) | \[ (?&EXPR) \] | \{ (?&EXPR) \} )*
            )
        )
        (?P<operator>sin|cos|tan|log|ln)
        \s* [\(\[\{]        
            (?P<inner> (?&EXPR) )
        [\)\]\}]             
        $/x';

        if (preg_match($function_pattern, $expression, $m)) {
            return [
                'operator' => strtolower($m['operator']),
                'left'     => $this->build($m['inner']),
                'right'    => null,
            ];
        }

        if ($this->isWrappedInParentheses($expression)) {
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

        // unary function if right is null
        if (array_key_exists('right', $node) && $node['right'] === null) {
            $arg = $this->evaluate($node['left']);
            return match ($node['operator']) {
                'sin' => sin($arg),
                'cos' => cos($arg),
                'tan' => tan($arg),
                'ln'  => log($arg),
                'log' => log10($arg),
                default => throw new \Exception('Unknown function: ' . $node['operator']),
            };
        }

        // binary operators
        $left  = $this->evaluate($node['left']);
        $right = $this->evaluate($node['right']);

        return match ($node['operator']) {
            '+' => $left + $right,
            '-' => $left - $right,
            '*' => $left * $right,
            '/' => $right == 0
                ? throw new \Exception('Division by zero')
                : $left / $right,
            '^' => pow($left, $right),
            default => throw new \Exception('Unknown operator: ' . $node['operator']),
        };
    }

    private function branchTree(string $expression, int $position): array
    {
        $operator  = $expression[$position];
        $leftExpr  = substr($expression, 0, $position);
        $rightExpr = substr($expression, $position + 1);

        return [
            'operator' => $operator,
            'left'     => $this->build($leftExpr),
            'right'    => $this->build($rightExpr),
        ];
    }

    private function findOperatorPosition(string $expression, array $operators): int|bool
    {
        $open_parentheses = 0;

        if (in_array('+', $operators) || in_array('-', $operators)) {
            for ($i = strlen($expression) - 1; $i >= 0; $i--) {
                $char = $expression[$i];

                if (preg_match("/^[\)\]\}]$/", $char)) {
                    $open_parentheses++;
                } elseif (preg_match("/^[\(\[\{]$/", $char)) {
                    $open_parentheses--;
                } elseif ($open_parentheses === 0 && in_array($char, $operators)) {
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
                    $open_parentheses++;
                } elseif (preg_match("/^[\)\]\}]$/", $char)) {
                    $open_parentheses--;
                } elseif ($open_parentheses === 0 && in_array($char, $operators)) {
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
                   (?: [^(){}\[\]] | (?&BAL) )*
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
            return "{$pad}Value: {$node['value']}\n";
        }

        $out  = "{$pad}Operator: {$node['operator']}\n";
        $out .= $this->displayTree($node['left'],  $depth + 1);

        if ($node['right'] !== null) {
            $out .= $this->displayTree($node['right'], $depth + 1);
        }

        return $out;
    }
}
