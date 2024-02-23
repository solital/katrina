<?php

namespace KatrinaTest;

use PHPUnit\Framework\TestCase;

class ConnTest extends TestCase
{
    public function testInsert()
    {
        dd(ORMTest::connection('pgsql')::select()->get());
    }
}
