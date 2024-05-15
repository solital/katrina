<?php

declare(strict_types=1);

use KatrinaTest\Users;
use PHPUnit\Framework\TestCase;
use Katrina\Functions\Functions;

class StringsFunctionTest extends TestCase
{
    public function testConcat()
    {
        $sql = Users::select(Functions::concat(['Francesco', 'Baby'], 'result'))->get();
        $this->assertEquals('FrancescoBaby', $sql[0]->result);
    }

    public function testLtrim()
    {
        $sql = Users::select(Functions::ltrim('Francesco', 'result'))->get();
        $this->assertEquals('Francesco', $sql[0]->result);
    }

    public function testRtrim()
    {
        $sql = Users::select(Functions::rtrim('Francesco', 'result'))->get();
        $this->assertEquals('Francesco', $sql[0]->result);
    }

    public function testTrim()
    {
        $sql = Users::select(Functions::trim('Francesco', 'result'))->get();
        $this->assertEquals('Francesco', $sql[0]->result);
    }
}
