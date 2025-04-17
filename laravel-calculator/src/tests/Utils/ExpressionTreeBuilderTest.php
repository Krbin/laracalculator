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
        // (1+2) -3
        $this->assertEquals(0.0, $this->builder->evaluate($tree));
    }

    public function testPrecedence()
    {
        $tree = $this->builder->build('2+3*4');
        // 2 + (3*4) = 14
        $this->assertEquals(14.0, $this->builder->evaluate($tree));
    }

    public function testParenthesesAndMixedBrackets()
    {
        $expr = '[1+{2* (3+4)}]';
        print_r("start");
        $tree = $this->builder->build($expr);
        print_r("end");
        print_r($this->builder->displayTree($tree));
        $this->assertEquals(1 + (2 * (3 + 4)), $this->builder->evaluate($tree));
    }

    public function testPowerOperator()
    {
        $tree = $this->builder->build('2^3^2');
        // right‑associative: 2^(3^2) = 2^9 = 512
        $this->assertEquals(512.0, $this->builder->evaluate($tree));
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
}