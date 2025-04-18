<?php
// tests/Utils/ExpressionTreeBuilderTest.php

use PHPUnit\Framework\TestCase;
use App\Utils\ExpressionTreeBuilder;

class ExpressionTreeBuilderTest extends TestCase
{
    private ExpressionTreeBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new ExpressionTreeBuilder();
    }

    public function testSingleNumber()
    {
        $tree = $this->builder->build('42');
        $this->assertSame(['value' => 42.0], $tree);
        $this->assertEquals(42.0, $this->builder->evaluate($tree));
    }

    public function testBasicAdditionAndSubtraction()
    {
        $tree = $this->builder->build('1+2-3');
        // (1+2) - 3
        $this->assertEquals(0.0, $this->builder->evaluate($tree));
    }

    public function testPrecedence()
    {
        $tree = $this->builder->build('2+3*4');
        // 2 + (3*4) = 14
        $this->assertEquals(14.0, $this->builder->evaluate($tree));
    }

    public function testPowerOperator()
    {
        $tree = $this->builder->build('2^3^2');
        // right-associative: 2^(3^2) = 512
        $this->assertEquals(512.0, $this->builder->evaluate($tree));
    }

    public function testParenthesesAndMixedBrackets()
    {
        $expr = '[1+{2* (3+4)}]';
        $tree = $this->builder->build($expr);
        $this->assertEquals(1 + (2 * (3 + 4)), $this->builder->evaluate($tree));
    }

    public function testDisplayTree()
    {
        $expr = '(1+2)*3';
        $tree = $this->builder->build($expr);
        $output = $this->builder->displayTree($tree);

        $this->assertStringContainsString('Operator: *', $output);
        $this->assertStringContainsString('Operator: +', $output);
        $this->assertStringContainsString('Value: 1', $output);
        $this->assertStringContainsString('Value: 2', $output);
        $this->assertStringContainsString('Value: 3', $output);
    }

    // ---------- New tests for unary functions ----------

    public function testSinFunction()
    {
        $tree = $this->builder->build('sin(0)');
        $this->assertSame(['operator' => 'sin', 'left' => ['value' => 0.0], 'right' => null], $tree);
        $this->assertEqualsWithDelta(0.0, $this->builder->evaluate($tree), 1e-8);
    }

    public function testCosFunction()
    {
        $tree = $this->builder->build('cos(3.14159265)');
        $this->assertEqualsWithDelta(-1.0, $this->builder->evaluate($tree), 1e-7);
    }

    public function testTanFunction()
    {
        $tree = $this->builder->build('tan(0.785398163)'); // approx pi/4
        $this->assertEqualsWithDelta(1.0, $this->builder->evaluate($tree), 1e-7);
    }

    public function testLogFunction()
    {
        $tree = $this->builder->build('log(1000)');
        $this->assertSame('log', $tree['operator']);
        $this->assertNull($tree['right']);
        $this->assertEqualsWithDelta(3.0, $this->builder->evaluate($tree), 1e-8);
    }

    public function testLnFunction()
    {
        $tree = $this->builder->build('ln(1)');
        $this->assertSame('ln', $tree['operator']);
        $this->assertNull($tree['right']);
        $this->assertEqualsWithDelta(0.0, $this->builder->evaluate($tree), 1e-8);
    }

    public function testNestedFunctions()
    {
        $expr = 'sin(ln(1))';
        $tree = $this->builder->build($expr);
        // ln(1) = 0, sin(0) = 0
        $this->assertEqualsWithDelta(0.0, $this->builder->evaluate($tree), 1e-8);
    }

    public function testFunctionAndBinaryMix()
    {
        $expr = 'sin(0)+cos(0)';
        $tree = $this->builder->build($expr);
        // sin(0)=0, cos(0)=1 => 1
        print_r($this->builder->displayTree($tree));
        $this->assertEqualsWithDelta(1.0, $this->builder->evaluate($tree), 1e-8);
    }
}
