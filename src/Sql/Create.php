<?php

namespace Katrina\Sql;

use PDO;
use Katrina\Sql\TypesTrait;
use Katrina\Connection\DB as DB;

abstract class Create
{
    use TypesTrait;

    /**
     * @param string $fetch
     * 
     * @return mixed
     */
    public function build(string $fetch = "")
    {
        $fetch = strtoupper($fetch);

        try {
            $this->sql = rtrim($this->sql, ",");
            $stmt = DB::prepare($this->sql);
            $res = $stmt->execute();

            if ($fetch == "ONLY") {
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
            } else if ($fetch == "ALL") {
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $res;
        } catch (\PDOException $e) {
            throw new \PDOException("Error in 'build()': " . $e->getMessage());
        }
    }

    /**
     * @return Create
     */
    public function closeTable(): Create
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= ");";

        return $this;
    }

    /**
     * @param string $table
     * 
     * @return Create
     */
    public function createTable(string $table): Create
    {
        $this->sql = "CREATE TABLE IF NOT EXISTS $table (";

        return $this;
    }

    /**
     * @return Create
     */
    public function listTables(): Create
    {
        $this->sql = "SHOW TABLES";

        return $this;
    }

    /**
     * @param string $table
     * 
     * @return Create
     */
    public function describeTable(string $table): Create
    {
        $this->sql = "DESCRIBE " . $table;

        return $this;
    }

    /**
     * @param string $table
     * 
     * @return Create
     */
    public function dropTable(string $table): Create
    {
        $this->sql = "DROP TABLE IF EXISTS " . $table;

        return $this;
    }

    /**
     * @param bool $check_foreign_key
     * 
     * @return Create
     */
    public function truncate(bool $check_foreign_key = false): Create
    {
        $this->sql = "TRUNCATE TABLE $this->table;";
        if ($check_foreign_key == true) {
            $this->sql = "SET FOREIGN_KEY_CHECKS=0;";
            $this->sql .= "TRUNCATE TABLE $this->table;";
            $this->sql .= "SET FOREIGN_KEY_CHECKS=1;";
        }

        return $this;
    }
}
