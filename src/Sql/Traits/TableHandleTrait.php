<?php

namespace Katrina\Sql\Traits;

use Katrina\Exceptions\KatrinaException;
use Katrina\Sql\KatrinaStatement;

trait TableHandleTrait
{
    use DataTypesTrait;

    /**
     * Open the script to create a table on database
     * 
     * @param string $table
     * 
     * @return self
     */
    public static function createTable(string $table): self
    {
        self::$static_sql = "CREATE TABLE IF NOT EXISTS $table (";
        return new static;
    }

    /**
     * Execute the script to create a table on database
     * 
     * @return mixed
     */
    public function closeTable(): mixed
    {
        self::$static_sql = rtrim(self::$static_sql, ", ");
        self::$static_sql .= ");";
        return KatrinaStatement::executePrepare(self::$static_sql);
    }

    /**
     * The alter() method performs the procedures of adding, changing and deleting a field from the database table.
     * 
     * @return static
     */
    public static function alter(): static
    {
        self::getBacktips();

        $class = get_called_class();
        $instance = new $class;

        $table = (is_null($instance->table) ? strtolower(self::getClassWithoutNamespace($class)) : $instance->table);

        self::$static_sql = "ALTER TABLE " . self::$backtips . $table . self::$backtips . " KATRINA_STR_REPLACE ";
        return new static;
    }

    /**
     * Rename a database table without `ALTER`
     * 
     * @deprecated
     * @param string $new_name
     * 
     * @return mixed
     */
    public static function renameTable(string $new_name): mixed
    {
        self::getBacktips();

        $class = get_called_class();
        $instance = new $class;

        $table = (is_null($instance->table) ? strtolower(self::getClassWithoutNamespace($class)) : $instance->table);

        self::$static_sql = "RENAME TABLE " . self::$backtips . $table . self::$backtips . " TO " . self::$backtips . $new_name . self::$backtips . ";";
        return KatrinaStatement::executePrepare(self::$static_sql);
    }

    /**
     * Truncate a table on database
     * 
     * @param bool $check_foreign_key
     * 
     * @return mixed
     */
    public static function truncate(bool $check_foreign_key = false): mixed
    {
        $class = get_called_class();
        $instance = new $class;

        $table = (is_null($instance->table) ? strtolower(self::getClassWithoutNamespace($class)) : $instance->table);

        if ($check_foreign_key == true) {
            self::$static_sql = "SET FOREIGN_KEY_CHECKS=0;";
            self::$static_sql .= "TRUNCATE TABLE $table;";
            self::$static_sql .= "SET FOREIGN_KEY_CHECKS=1;";
        } else {
            self::$static_sql = "TRUNCATE TABLE $table;";
        }

        return KatrinaStatement::executePrepare(self::$static_sql);
    }

    /**
     * Describe a table on database
     * 
     * 
     * @return mixed
     */
    public static function describeTable(): mixed
    {
        $class = get_called_class();
        $instance = new $class;

        $table = (is_null($instance->table) ? strtolower(self::getClassWithoutNamespace($class)) : $instance->table);

        self::$static_sql = "DESCRIBE " . $table;
        return KatrinaStatement::executeQuery(self::$static_sql, true);
    }

    /**
     * List all tables on database
     * 
     * @return mixed
     * @throws KatrinaException
     */
    public static function listTables(): mixed
    {
        try {
            self::$static_sql = match (DB_CONFIG['DRIVE']) {
                'mysql' => "SHOW TABLES",
                'pgsql' => "SELECT table_name FROM information_schema.tables WHERE table_schema='public'",
                default => throw new KatrinaException("Database drive " . DB_CONFIG['DRIVE'] . " not found or not valid")
            };

            return KatrinaStatement::executeQuery(self::$static_sql, true);
        } catch (KatrinaException $e) {
            die(KatrinaException::class . ": " . $e->getMessage());
        }
    }

    /**
     * Drop a table on database
     * 
     * @param string $table
     * 
     * @return mixed
     */
    public static function dropTable(string $table): mixed
    {
        self::$static_sql = "DROP TABLE IF EXISTS $table;";
        return KatrinaStatement::executePrepare(self::$static_sql);
    }
}
