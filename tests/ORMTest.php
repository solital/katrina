<?php

namespace KatrinaTest;

use Katrina\Katrina;

class ORMTest
{
    public function instance()
    {
        $katrina = new Katrina("usuarios", "idUsu", ["nome", "idade"]);
        return $katrina;
    }

    public function list()
    {
        return $this->instance()->select()->build("ALL");
    }

    public function limit(int $first, int $second)
    {
        return $this->instance()->select()->limit($first, $second)->build("ALL");
    }

    public function like(string $like)
    {
        return $this->instance()->select(null, "nome")->like($like)->build("ALL");
    }

    public function order(string $order, bool $grow)
    {
        return $this->instance()->select()->order($order, $grow)->build("ALL");
    }

    public function between($column, $first, $second)
    {
        return $this->instance()->select(null, $column)->between($first, $second)->build("ALL");
    }
}
