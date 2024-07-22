<?php

namespace Katrina\Sql\Traits;

use Katrina\Connection\Connection;
use Katrina\Exceptions\KatrinaException;
use Katrina\Sql\KatrinaStatement;
use SensitiveParameter;

trait CreateUpdateSQLTrait
{
    use DataTypesTrait;

    /**
     * @var array
     */
    private static array $array_columns;

    /**
     * @var array
     */
    private static array $array_values;

    /**
     * @var string
     */
    private static string $table_foreign;

    /**
     * Insert data in database
     * 
     * @param array $table_columns
     * 
     * @return self
     * @throws KatrinaException
     */
    public static function insert(#[SensitiveParameter] array $table_columns): self
    {
        $class = get_called_class();
        $instance = new $class;

        $table = (
            is_null($instance->table) ?
            strtolower(self::getClassWithoutNamespace($class)) :
            $instance->table
        );

        $array_columns = array_keys($table_columns);
        $columns = implode(",", $array_columns);

        $array_values = array_values($table_columns);
        $array_values2 = str_repeat("?,", count($array_values));
        $values = rtrim($array_values2, ",");

        try {
            $sql = "INSERT INTO " . $table . " (" . ($columns) . ") VALUES (" . $values . ")";
            $stmt = Connection::getInstance()->prepare($sql);

            for ($i = 0, $b = 1; $i < count($array_columns), $b <= count($array_columns); $i++, $b++) {
                $stmt->bindParam($b, $array_values[$i]);
            }

            $res = $stmt->execute();

            if ($res != true) {
                $error = $stmt->errorInfo();
                throw new KatrinaException($error[2]);
            }

            return new static;
        } catch (KatrinaException $e) {
            $e->getMessage() . ": " . self::$static_sql;
        }

        return new static;
    }

    /**
     * Return last ID
     * 
     * @return int
     */
    public function lastId(): int
    {
        $last_id = Connection::getInstance()->lastInsertId();
        return (int)$last_id;
    }

    /**
     * Update data in database
     * 
     * @param array $table_columns
     * 
     * @return self
     */
    public static function update(#[SensitiveParameter] array $table_columns): self
    {
        $class = get_called_class();
        $instance = new $class;

        $table = (
            is_null($instance->table) ?
            strtolower(self::getClassWithoutNamespace($class)) :
            $instance->table
        );

        self::$array_columns = array_keys($table_columns);
        self::$array_values = array_values($table_columns);
        self::$static_sql = "UPDATE $table SET ";

        for ($i = 0, $b = 1; $i < count(self::$array_columns), $b <= count(self::$array_columns); $i++, $b++) {
            self::$static_sql .= self::$array_columns[$i] . " = ?,";
        }

        self::$static_sql = rtrim(self::$static_sql, ",");
        return new static;
    }

    /**
     * Delete data in database
     * 
     * @param string $column
     * @param mixed $value
     * @param bool $safe_mode
     * 
     * @return mixed
     */
    public static function delete(
        #[SensitiveParameter] string $column,
        #[SensitiveParameter] mixed $value,
        bool $safe_mode = true
    ): mixed {
        $class = get_called_class();
        $instance = new $class;

        self::$table_foreign = (
            is_null($instance->table) ?
            strtolower(self::getClassWithoutNamespace($class)) :
            $instance->table
        );

        if (is_string($value)) {
            $quote = "'";
        } elseif (is_int($value)) {
            $quote = "";
        }

        if ($safe_mode == false) {
            $sql = "SET FOREIGN_KEY_CHECKS=0;";
            $sql .= "DELETE FROM {$instance->table} WHERE {$column} = " . $quote . $value . $quote . ";";
            $sql .= "SET FOREIGN_KEY_CHECKS=1;";
        } else {
            $sql = "DELETE FROM {$instance->table} WHERE {$column} = " . $quote . $value . $quote . ";";
        }

        return KatrinaStatement::executePrepare($sql);
    }

    /**
     * Save data with 'update' method
     * 
     * @return mixed
     * @throws KatrinaException
     */
    public function saveUpdate(): mixed
    {
        try {
            if (!str_contains(self::$static_sql, "WHERE")) throw new KatrinaException("Update method must have `where()` method");

            $stmt = Connection::getInstance()->prepare(self::$static_sql);

            for ($i = 0, $b = 1; $i < count(self::$array_columns), $b <= count(self::$array_columns); $i++, $b++) {
                $stmt->bindParam($b, self::$array_values[$i]);
            }

            $res = $stmt->execute();

            if ($res != true) {
                $error = $stmt->errorInfo();
                throw new KatrinaException($error[2]);
            }

            return $res;
        } catch (KatrinaException $e) {
            $e->getMessage() . ": " . self::$static_sql;
        }

        return null;
    }
}
