<?php

declare(strict_types=1);

use KatrinaTest\Users;
use PHPUnit\Framework\TestCase;
use Katrina\Functions\Functions;

class MySqlTest extends TestCase
{
    public function testSelectActiveRecord()
    {
        $res = Users::all();
        $this->assertIsArray($res);
    }

    public function testSelectPrimaryKey()
    {
        $res = Users::find(6);
        $this->assertEquals('Juana', $res->first_name);
    }

    public function testSelectWithException()
    {
        $this->expectException('Exception');

        Users::findwithException(100);
    }

    /* public function testListTables()
    {
        $res = Users::listTables();
        $this->assertIsArray($res);
    }

    public function testLimit()
    {
        $res = Users::select()->limit(0, 3)->get();
        $this->assertIsArray($res);
    }

    public function testLike()
    {
        $res = Users::select()->where("email")->like("%hotmail%")->get();
        $this->assertIsArray($res);
    }

    public function testOrder()
    {
        $res = Users::select()->order("first_name", false)->get();
        $this->assertIsArray($res);
    }

    public function testBetween()
    {
        $res = Users::select()->where("id")->between(3, 8)->get();
        $this->assertIsArray($res);
    }

    public function testAndOr()
    {
        $res = Users::select()->where("first_name", "Francesco")->and("email", "ihamill@yahoo.com")->get();
        $this->assertIsArray($res);
    }

    public function testJoin()
    {
        $res = Users::select()
            ->innerJoin("sobrenome", "id_nome")
            ->innerJoin("cpf", "idUsu")
            ->where("cpf_number", 123123123)
            ->get();

        $this->assertIsArray($res);
    }

    public function testCustom()
    {
        $res = Users::customQuery("SELECT * FROM users_test", true);
        $this->assertIsArray($res);
    }

    public function testSelectInSelect()
    {
        $sql = Users::select("nome")->where("nome", "brenno")->rawQuery();
        $res =  Users::select("nome, idade")->where("nome", Functions::subquery($sql))->get();

        $this->assertIsArray($res);
    }

    public function testGroup()
    {
        $res = Users::select("nome, " . Functions::count(as: 'qtd'))->group("nome")->get();
        $this->assertIsArray($res);
    }

    public function testDescribe()
    {
        $res = Users::describeTable('usuarios');
        $this->assertIsArray($res);
    }

    public function testLatest()
    {
        $res = Users::latest('id')->get();
        $this->assertIsArray($res);
    }

    public function testWhereAsArray()
    {
        $res = Users::select()->where([
            "email", "harvey@email.com",
            "username", "Harvey"
        ])->get();
        $this->assertIsArray($res);
    }

    public function testCount()
    {
        $res = Users::count();
        $this->assertIsInt($res);
    } */
}
