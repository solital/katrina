<?php

declare(strict_types=1);

use KatrinaTest\Models\Users;
use PHPUnit\Framework\TestCase;
use Katrina\Functions\Functions;

class ReadTest extends TestCase
{
    public function testSelectActiveRecord()
    {
        $result = Users::all();
        $this->assertIsArray($result);
    }

    public function testSelectPrimaryKey()
    {
        $result = Users::find(1);
        $this->assertEquals('Francesco', $result->first_name);

        $result = Users::findFirst();
        $this->assertEquals('Francesco', $result[0]->first_name);
    }

    public function testSelectWithException()
    {
        $this->expectException('Exception');
        Users::findwithException(100);
    }

    public function testListTables()
    {
        $result = Users::listTables();
        $this->assertIsArray($result);
    }

    public function testLimit()
    {
        $result = Users::select()->limit(0, 3)->get();
        $this->assertIsArray($result);
    }

    public function testLike()
    {
        $result = Users::select()->where("email")->like("%yahoo%")->get();
        $this->assertIsArray($result);
    }

    public function testOrder()
    {
        $result = Users::select()->order("first_name", false)->get();
        $this->assertIsArray($result);
    }

    public function testBetween()
    {
        $result = Users::select()->where("id")->between(3, 8)->get();
        
        foreach ($result as $res) {
            $this->assertEquals((3 || 4 || 5 || 6 || 7 || 8), $res->id);
        }
    }

    public function testAndOr()
    {
        $result1 = Users::select()->where("first_name", "Francesco")->and("email", "ihamill@yahoo.com")->get();
        $this->assertIsArray($result1);

        $result2 = Users::select()->where("first_name", "Francesco")->or("email", "kshlerin.laila@gmail.com")->get();
        $this->assertIsArray($result2);
    }

    /* public function testJoin()
    {
        $res = Users::select()
            ->innerJoin("sobrenome", "id_nome")
            ->innerJoin("cpf", "idUsu")
            ->where("cpf_number", 123123123)
            ->get();

        $this->assertIsArray($res);
    } */

    public function testSelectInSelect()
    {
        $sql = Users::select("first_name")->where("first_name", "Baby")->rawQuery();
        $result =  Users::select("first_name, age")->where("first_name", Functions::subquery($sql))->getUnique();

        $this->assertEquals('Baby', $result->first_name);
        $this->assertEquals(33, $result->age);
    }

    public function testGroup()
    {
        $res = Users::select("first_name, " . Functions::count(as: 'qtd'))->group("first_name")->get();
        $this->assertIsArray($res);
    }

    public function testDescribe()
    {
        $result = Users::describeTable();
        $this->assertIsArray($result);
    }

    public function testLatest()
    {
        $result = Users::latest('id')->get();
        $this->assertEquals('Clemens', $result[0]->first_name);
    }

    public function testWhereAsArray()
    {
        $result = Users::select()->where([
            "first_name", "Jayde",
            "age", 69
        ])->getUnique();

        $this->assertEquals('Jayde', $result->first_name);
    }

    public function testCount()
    {
        $result = Users::count();
        $this->assertIsInt($result);
    }
}
