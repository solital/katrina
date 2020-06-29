<?php

namespace Katrina\Sql;
use Katrina\Connection\DB as DB;
use Katrina\Exception\Exception;
use PDO;

abstract class Create extends Types
{   
    /**
     * Compiles the SQL code
     * @param string $fetch "ONLY" for one result; "ALL" for all results
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
            Exception::alertMessage($e, "'build()' error");
        }
    }

    /**
     * Closes the table after it is created
     */
    public function closeTable()
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= ");";
        return $this;
    }

    /**
     * Starts creating a new table
     * @param string $table table name
     */
    public function createTable(string $table)
    {
        $this->sql = "CREATE TABLE IF NOT EXISTS $table (";

        return $this;
    }

    /**
     * List all tables
     */
    public function listTables()
    {
        $this->sql = "SHOW TABLES";

        return $this;
    }

    /**
     * Describe a table
     * @param string $table table name
     */
    public function describeTable(string $table)
    {
        $this->sql = "DESCRIBE ".$table;

        return $this;
    }

    /**
     * Drop a table
     * @param string $table table name
     */
    public function dropTable(string $table)
    {
        $this->sql = "DROP TABLE IF EXISTS ".$table;

        return $this;
    }

    /**
     * Truncate a table
     * @param string $table table name
     */
    public function truncate($check_foreign_key = false)
    {
        $this->sql = "TRUNCATE TABLE $this->table;";
        if ($check_foreign_key == true) {
            $this->sql = "SET FOREIGN_KEY_CHECKS=0;";
            $this->sql .= "TRUNCATE TABLE $this->table;";
            $this->sql .= "SET FOREIGN_KEY_CHECKS=1;";
        }

        return $this;
    }

    /**
     * Dump a database
     * @param string $database database name
     * @param string $dump_dir dump directory
     */
    public function dump(string $database, string $dump_dir)
    {
        \exec("mysqldump -u ".DB_CONFIG['USER']." -p".DB_CONFIG['PASS']." $database --routines > $dump_dir;");
    }
}
