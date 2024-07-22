<?php

use Katrina\Functions\Functions;
use KatrinaTest\Models\Users;
use PHPUnit\Framework\TestCase;

class AggregateFunctionsTest extends TestCase
{
    public function testAvg()
    {
        $sql = Users::select(Functions::avg('age', 'result'))->get();
        $this->assertEquals(40.7000, $sql[0]->result);
    }

    public function testCount()
    {
        $sql = Users::select(Functions::count(as: 'result'))->get();
        $this->assertEquals(10, $sql[0]->result);
    }

    public function testMax()
    {
        $sql = Users::select(Functions::max('age', 'result'))->get();
        $this->assertEquals(69, $sql[0]->result);
    }

    public function testMin()
    {
        $sql = Users::select(Functions::min('age', 'result'))->get();
        $this->assertEquals(19, $sql[0]->result);
    }

    public function testGroupConcat()
    {
        $sql = Users::select(Functions::groupConcat('DISTINCT first_name ORDER BY first_name', 'result'))->get();
        $this->assertEquals('Baby,Bernita,Clemens,Francesco,Jayde,Juana,Otilia,Tristian,Zoey', $sql[0]->result);
    }

    public function testSum()
    {
        $sql = Users::select(Functions::sum('age + age', 'result'))->group('age')->get();
        $this->assertEquals(90, $sql[0]->result);
        $this->assertEquals(60, $sql[1]->result);
        $this->assertEquals(66, $sql[2]->result);
        $this->assertEquals(138, $sql[3]->result);
        $this->assertEquals(106, $sql[4]->result);
        $this->assertEquals(94, $sql[5]->result);
        $this->assertEquals(56, $sql[6]->result);
        $this->assertEquals(38, $sql[7]->result);
        $this->assertEquals(120, $sql[8]->result);
        $this->assertEquals(46, $sql[9]->result);
    }
}
