<?php

declare(strict_types=1);

use KatrinaTest\Models\Users;
use PHPUnit\Framework\TestCase;

class CreateUpdateTest extends TestCase
{
    public function testInsertMethod()
    {
        Users::insert([
            'first_name' => 'Harvey',
            'last_name' => 'Specter',
            'address' => 'Wall Street',
            'phone' => '+123456789',
            'email' => 'harvey@email.com',
            'age' => '40'
        ]);

        $result = Users::select()->where('email', 'harvey@email.com')->getUnique();
        $this->assertIsObject($result);
    }

    public function testInsertActiveRecord()
    {
        $orm = new Users();
        $orm->first_name = 'Louis';
        $orm->last_name = 'Litt';
        $orm->address = 'Wall Street';
        $orm->phone = '+987654321';
        $orm->email = 'louis@email.com';
        $orm->age = '38';
        $orm->save();

        $result = Users::select()->where('email', 'louis@email.com')->getUnique();
        $this->assertIsObject($result);
    }

    public function testLastIdInsert()
    {
        $result = Users::insert([
            'first_name' => 'Mike',
            'last_name' => 'Ross',
            'address' => 'Wall Street',
            'phone' => '+12397456',
            'email' => 'mike@email.com',
            'age' => '25'
        ])->lastId();
        
        $this->assertIsInt($result);
    }

    public function testUpdateMethod()
    {
        Users::update([
            'address' => '000 Wall Street',
        ])->where('email', 'harvey@email.com')->saveUpdate();
        
        $result = Users::select()->where('email', 'harvey@email.com')->getUnique();
        $this->assertEquals('000 Wall Street', $result->address);
    }

    public function testUpdateActiveRecord()
    {
        $orm = new Users;
        $orm->id = 2;
        $orm->first_name = "Bernita Edit";
        $orm->age = 30;
        $orm->save();

        $result1 = Users::select()->where('first_name', 'Bernita Edit')->getUnique();
        $this->assertIsObject($result1);

        $orm = new Users;
        $orm->id = 2;
        $orm->first_name = "Bernita";
        $orm->age = 30;
        $orm->save();

        $result2 = Users::select()->where('first_name', 'Bernita')->getUnique();
        $this->assertIsObject($result2);
    }

    public function testDelete()
    {
        $result1 = Users::delete('first_name', 'Harvey');
        $this->assertTrue($result1);

        $result2 = Users::delete('first_name', 'Louis');
        $this->assertTrue($result2);

        $result3 = Users::delete('first_name', 'Mike');
        $this->assertTrue($result3);
    }
}
