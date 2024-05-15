<?php

use Katrina\Functions\Functions;
use KatrinaTest\Users;
use PHPUnit\Framework\TestCase;

class MathFunctionsTest extends TestCase
{
    public function testAbs()
    {
        $sql = Users::select(Functions::abs('age', 'result'))->get();
        $this->assertEquals(45, $sql[0]->result);
        $this->assertEquals(25, $sql[1]->result);
        $this->assertEquals(33, $sql[2]->result);
        $this->assertEquals(69, $sql[3]->result);
        $this->assertEquals(53, $sql[4]->result);
        $this->assertEquals(47, $sql[5]->result);
        $this->assertEquals(28, $sql[6]->result);
        $this->assertEquals(19, $sql[7]->result);
        $this->assertEquals(60, $sql[8]->result);
        $this->assertEquals(23, $sql[9]->result);
    }

    public function testRound()
    {
        $sql = Users::select(Functions::round('AVG(age)', as: 'result'))->get();
        $this->assertEquals(40, $sql[0]->result);
    }

    public function testTruncate()
    {
        $sql = Users::select(Functions::truncate('age', 0, 'result'))->get();
        $this->assertEquals(45, $sql[0]->result);
        $this->assertEquals(25, $sql[1]->result);
        $this->assertEquals(33, $sql[2]->result);
        $this->assertEquals(69, $sql[3]->result);
        $this->assertEquals(53, $sql[4]->result);
        $this->assertEquals(47, $sql[5]->result);
        $this->assertEquals(28, $sql[6]->result);
        $this->assertEquals(19, $sql[7]->result);
        $this->assertEquals(60, $sql[8]->result);
        $this->assertEquals(23, $sql[9]->result);
    }
}
