<?php

namespace KatrinaTest;

use Katrina\Functions\Functions;
use Katrina\Connection\Connection;
use Katrina\Katrina;

class ORMTest extends Katrina
{
    protected ?string $table = "users";
    //protected ?string $id = "id_orm";
    protected bool $timestamp = true;

    //protected ?bool $cache = true;
    protected string $created_at = 'created_date';
    protected string $updated_at = 'updated_date';

    public function create()
    {
        $res = self::createTable("table_test")
            ->int('id_orm')->primary()->increment()
            ->varchar("name", 20)->notNull()
            ->varchar("email", 100)->notNull()
            #->constraint("dev_cons_fk")->foreign("id_usu")->references("usuarios", "idUsu")->onDelete('cascade')
            ->createdUpdatedAt()
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
