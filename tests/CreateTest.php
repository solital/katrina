<?php

use Katrina\Functions\Functions;
use KatrinaTest\ORMTest;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testInsert()
    {
        $res = ORMTest::insert([
            'name' => Functions::now(),
            'email' => 'katrina@email.com'
        ]);
        
        $this->assertIsObject($res);
    }

    public function testLastIdInsert()
    {
        $res = ORMTest::insert([
            'name' => 'Katrina ORM',
            'email' => 'katrina@email.com'
        ])->lastId();
        
        $this->assertIsInt($res);
    }

    public function testUpdate()
    {
        $res = ORMTest::update([
            'name' => 'Katrina ORM',
            'email' => 'katrina@email.com'
        ])->where('email', 'katrina@email.com')->saveUpdate();
        
        $this->assertTrue($res);
    }

    public function testCreate()
    {
        $res = (new ORMTest())->create();
        $this->assertTrue($res);
    }

    public function testAlterAdd()
    {
        $res = (new ORMTest)->alter("table_test")->varchar("username", 20)->add();
        $this->assertTrue($res);
    }

    public function testAlterDrop()
    {
        $res = (new ORMTest)->alter("table_test")->drop("username");
        $this->assertTrue($res);
    }

    public function testAlterModify()
    {
        $res = (new ORMTest)->alter("table_test")->varchar("name", 100)->modify();
        $this->assertTrue($res);
    }

    public function testAlterRename()
    {
        $res = (new ORMTest)->alter("table_test")->rename("tb_test");
        $this->assertTrue($res);
    }

    public function testAddForeignKey()
    {
        $res = (new ORMTest)->alter("table_test")->constraint("dev_cons_fk")->foreign("type")->references("dev", "iddev");
        $this->assertTrue($res);
    }
    
    public function testRenameTable()
    {
        $res = (new ORMTest)->renameTable("tb_test", "table_test");
        $this->assertTrue($res);
    }
    
    public function testForceTruncateTable()
    {
        $res = (new ORMTest)->truncate("usuarios", true);
        $this->assertTrue($res);
    }

    public function testTransaction()
    {
        $res = (new ORMTest())->transaction();
        $this->assertTrue($res);
    }

    public function testInsertActiveRecord()
    {
        $orm = new ORMTest();
        $orm->nome = "Active Record Update";
        $orm->idade = 10;
        $orm->save();
    }

    public function testUpdateActiveRecord()
    {
        $orm = ORMTest::find(41);
        $orm->nome = "Active Record Update";
        $orm->idade = 10;
        $orm->save();
    }

    public function testDelete()
    {
        $orm = ORMTest::delete('id', 2);
        $this->assertTrue($orm);
    }
}
