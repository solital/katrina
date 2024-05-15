<?php

use Katrina\Functions\Functions;
use KatrinaTest\Users;
use PHPUnit\Framework\TestCase;

date_default_timezone_set('America/Fortaleza');

class DateFunctionsTest extends TestCase
{
    public function testNowCurrentTimestamp()
    {
        $sql = Users::select(Functions::now('result'))->get();
        $this->assertEquals(date('Y-m-d H:i:s'), $sql[0]->result);

        $sql = Users::select(Functions::currentTimestamp('result'))->get();
        $this->assertEquals(date('Y-m-d H:i:s'), $sql[0]->result);
    }

    public function testCurdate()
    {
        $sql = Users::select(Functions::curdate('result'))->get();
        $this->assertEquals(date('Y-m-d'), $sql[0]->result);
    }
}
