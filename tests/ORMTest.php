<?php

namespace KatrinaTest;

use Katrina\Functions\Functions;
use Katrina\Connection\Connection;
use Katrina\Katrina;

class ORMTest extends Katrina
{
    protected ?string $table = "auth_users";
    //protected ?string $id = "id_orm";
    protected bool $timestamp = false;

    //protected ?bool $cache = true;

    public function create()
    {
        $res = self::createTable("table_test")
            ->int('id_orm')->primary()->increment()
            ->varchar("name", 20)->notNull()
            ->varchar("email", 100)->notNull()
            #->constraint("dev_cons_fk")->foreign("id_usu")->references("usuarios", "idUsu")->onDelete('cascade')
            #->createdUpdateAt()
            ->closeTable();

        return $res;
    }

    public function listAllTables()
    {
        return self::listTables();
    }

    public function describe()
    {
        return self::describeTable('usuarios');
    }

    public function transaction()
    {
        try {
            $pdo = Connection::getInstance();
            $pdo->beginTransaction();

            // code...

            $pdo->commit();

            return true;
        } catch (\PDOException $e) {
            $pdo->rollback();
            #echo $e->getMessage();

            return false;
        }
    }
}
