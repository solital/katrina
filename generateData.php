<?php

declare(strict_types=1);

require_once "vendor/autoload.php";
require_once 'tests/configTest.php';

use KatrinaTest\Users;

$faker = Faker\Factory::create();

for ($i = 0; $i < 10; $i++) {
    Users::insert([
        'first_name' => $faker->firstName(),
        'last_name' => $faker->lastName(),
        'address' => $faker->streetAddress(),
        'phone' => $faker->phoneNumber(),
        'email' => $faker->email()
    ]);
}