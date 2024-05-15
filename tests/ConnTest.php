<?php

namespace KatrinaTest;

use Katrina\Exceptions\ConnectionException;
use KatrinaTest\Models\ORMTest;
use PHPUnit\Framework\TestCase;

class ConnTest extends TestCase
{
    public function testExtensionNotFound()
    {
        $this->expectException(ConnectionException::class);
        ORMTest::connection('pgsql')::select()->get();
    }
}
