<?php

declare(strict_types=1);

use KatrinaTest\ORMTest;
use PHPUnit\Framework\TestCase;
use Katrina\Functions\Functions;

class SqlTest extends TestCase
{
    public function testSelectActiveRecord()
    {
        $res = ORMTest::all();
        $this->assertIsArray($res);
    }

    public function testSelectPrimaryKey()
    {
        $res = ORMTest::find(20);
        $this->assertEquals('brenno', $res->nome);
    }

    public function testListTables()
    {
        $res = ORMTest::listTables();
        $this->assertIsArray($res);
    }

    public function testLimit()
    {
        $res = ORMTest::select()->limit(0, 3)->get();
        $this->assertIsArray($res);
    }

    public function testLike()
    {
        $res = ORMTest::select()->where("nome")->like("%bre%")->get();
        $this->assertIsArray($res);
    }

    public function testOrder()
    {
        $res = ORMTest::select()->order("nome", false)->get();
        $this->assertIsArray($res);
    }

    public function testBetween()
    {
        $res = ORMTest::select()->where("idade")->between(10, 22)->get();
        $this->assertIsArray($res);
    }

    public function testAndOr()
    {
        $res = ORMTest::select()->where("brand", 'visa')->and("cvv", '502')->get();
        #$res = ORMTest::select()->where("brand", 'visa')->or("cvv", '502')->get();
        $this->assertIsArray($res);
    }

    public function testJoin()
    {
        $res = ORMTest::select()
            ->innerJoin("sobrenome", "id_nome")
            ->innerJoin("cpf", "idUsu")
            ->where("cpf_number", 123123123)
            ->get();

        $this->assertIsArray($res);
    }

    public function testCustom()
    {
        $res = ORMTest::customQuery("SELECT * FROM usuarios", true);
        $this->assertIsArray($res);
    }

    public function testSelectInSelect()
    {
        $sql = ORMTest::select("nome")->where("nome", "brenno")->rawQuery();
        $res =  ORMTest::select("nome, idade")->where("nome", Functions::subquery($sql))->get();

        $this->assertIsArray($res);
    }

    public function testGroup()
    {
        $res = ORMTest::select("nome, " . Functions::count(as: 'qtd'))->group("nome")->get();
        $this->assertIsArray($res);
    }

    public function testDescribe()
    {
        $res = ORMTest::describeTable('usuarios');
        $this->assertIsArray($res);
    }

    public function testLatest()
    {
        $res = ORMTest::latest('id')->get();
        $this->assertIsArray($res);
    }

    public function testWhereAsArray()
    {
        $res = ORMTest::select()->where([
            "email", "harvey@email.com",
            "username", "Harvey"
        ])->get();
        $this->assertIsArray($res);
    }
}
