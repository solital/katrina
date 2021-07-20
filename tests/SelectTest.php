<?php

declare(strict_types=1);

use KatrinaTest\ORMTest;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function testSelect()
    {
        $res = (new ORMTest())->list();
        $this->assertIsArray($res);
    }

    public function testLimit()
    {
        $res = (new ORMTest())->limit(2, 3);
        $this->assertIsArray($res);
    }

    public function testLike()
    {
        $res = (new ORMTest())->like("%bre%");
        $this->assertIsArray($res);
    }

    public function testOrder()
    {
        $res = (new ORMTest())->order("nome", false);
        $this->assertIsArray($res);
    }

    public function testBetween()
    {
        $res = (new ORMTest())->between("idade", 10, 22);
        $this->assertIsArray($res);
    }
}
