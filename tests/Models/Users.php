<?php

namespace KatrinaTest\Models;

use Katrina\Katrina;

class Users extends Katrina
{
    protected ?string $table = "users_test";
    //protected ?string $id = "id";
    protected bool $timestamp = false;
    //protected ?bool $uuid_increment = true;
}
