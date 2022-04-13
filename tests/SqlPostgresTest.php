<?php

use KatrinaTest\ORMTest;
use PHPUnit\Framework\TestCase;

class SqlPostgresTest extends TestCase
{
    /* public function testCreate()
    {
        $res = (new ORMTest())->create();
        $this->assertTrue($res);
    } */

    public function testInsertActiveRecord()
    {
        $orm = new ORMTest();
        $orm->id_usu = 1;
        $orm->name = "Active Record Update";
        $orm->save();
    }

    /* public function testSelectActiveRecord()
    {
        $res = ORMTest::all();
        $this->assertIsArray($res);
    }

    public function testFindActiveRecord()
    {
        $res = ORMTest::find(20);
        $this->assertEquals('brenno', $res->nome);
    } */
}
