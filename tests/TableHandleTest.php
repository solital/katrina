<?php

declare(strict_types=1);

use KatrinaTest\Models\ORMTest;
use PHPUnit\Framework\TestCase;

class TableHandleTest extends TestCase
{
    public function testCreate()
    {
        $res = ORMTest::create();
        $this->assertTrue($res);
    }

    public function testAlterAdd()
    {
        $res = ORMTest::alter()->varchar("username", 20)->add();
        $this->assertTrue($res);
    }

    public function testAlterDrop()
    {
        $res = ORMTest::alter()->drop("username");
        $this->assertTrue($res);
    }

    public function testAlterModify()
    {
        $res = ORMTest::alter()->varchar("name", 100)->modify();
        $this->assertTrue($res);
    }

    /* public function testAddForeignKey()
    {
        $res = ORMTest::alter()->constraint("dev_cons_fk")->foreign("type")->references("dev", "iddev");
        $this->assertTrue($res);
    } */
    
    public function testTruncateTable()
    {
        $res = ORMTest::truncate();
        $this->assertTrue($res);
    }

    public function testTransaction()
    {
        $res = ORMTest::transaction();
        $this->assertTrue($res);
    }

    public function testAlterRename()
    {
        $res = ORMTest::alter()->rename("tb_test");
        $this->assertTrue($res);
    }

    public function testDrop()
    {
        $res = ORMTest::dropTable("tb_test");
        $this->assertTrue($res);
    }
}
