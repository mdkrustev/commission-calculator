<?php

use App\CommissionTask\CommissionCalculator;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    public function testAdd()
    {
        $commissionCalculator = new CommissionCalculator();
        $correctValues = [
            0.6, 3.00, 0.00, 0.06, 1.50, 0.00, 0.70, 0.30, 0.30, 3.00, 0.00, 0.00, 8612
        ];

        foreach ($commissionCalculator->calculateTest() as $i => $value) {
            $this->assertEquals($correctValues[$i], $value);
        }
    }
}
