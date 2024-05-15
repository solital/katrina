<?php

namespace Katrina\Sql\Traits;

use Katrina\Cache;
use Katrina\Connection\Connection;
use Katrina\Exceptions\KatrinaException;
use Katrina\Functions\Functions;
use Katrina\Sql\KatrinaStatement;

trait ReadSQLTrait
{
    use DataTypesTrait;

    /**
     * @var array
     */
    protected static array $config = [];

    /**
     * @var Cache
     */
    protected static Cache $cache_instance;

    /**
     * @var string
     */
    private static string $table_foreign;

    /**
     * @var null|string
     */
    private static ?string $id_foreign;

    /**
     * Find data with ID
     * 
     * @param int $id_table
     * 
     * @return mixed
     */
    public static function find(int $id_table): mixed
    {
        $class = get_called_class();
        $instance = new $class;

        $table = (is_null($instance->table) ? strtolower(self::getClassWithoutNamespace($class)) : $instance->table);
        $id = (is_null($instance->id) ? 'id' : $instance->id);

        $sql = 'SELECT * FROM ' . $table . ' WHERE ' . $id . " = {$id_table} ;";

        if (self::$config['cache'] == true) {
            $cache_values = self::$cache_instance->get($instance->table);

            if (!empty($cache_values) || $cache_values !== false) {
                return $cache_values;
            }
        } else if (self::$config['cache'] == false) {
            self::$cache_instance->delete($instance->table);
        }

        $result_values = KatrinaStatement::executeQuery($sql, false);

        if (self::$config['cache'] == true) {
            self::$cache_instance->set($instance->table, $result_values);
        }

        return $result_values;
    }

    /**
     * Find data with ID and throws exception if not exists
     *
     * @param int $id_table
     * 
     * @return mixed
     * @throws KatrinaException
     */
    public static function findwithException(int $id_table): mixed
    {
        $result = self::find($id_table);

        if ($result === false) {
            http_response_code(404);
            throw new KatrinaException("Data not found", 404);
        }

        return $result;
    }

    /**
     * Return all data in a table
     * 
     * @param string $filter
     * @param int $limit
     * @param int $offset
     * 
     * @return mixed
     * @throws PDOException
     */
    public static function all(string $filter = '', int $limit = 0, int $offset = 0): mixed
    {
        $class = get_called_class();
        $instance = new $class;

        $table = (is_null($instance->table) ? strtolower(self::getClassWithoutNamespace($class)) : $instance->table);

        $sql = 'SELECT * FROM ' . $table;
        $sql .= ($filter !== '') ? " WHERE {$filter}" : "";
        $sql .= ($limit > 0) ? " LIMIT {$limit}" : "";
        $sql .= ($offset > 0) ? " OFFSET {$offset}" : "";
        $sql .= ';';

        if (self::$config['cache'] == true) {
            $cache_values = self::$cache_instance->get($table);

            if (!empty($cache_values) || $cache_values !== false) {
                return $cache_values;
            }
        } else if (self::$config['cache'] == false) {
            self::$cache_instance->delete($table);
        }

        try {
            $result = Connection::getInstance()->query($sql);
            $result_values = $result->fetchAll(\PDO::FETCH_CLASS);

            if (self::$config['cache'] == true) {
                self::$cache_instance->set($table, $result_values);
            }

            return $result_values;
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Count all values on a table
     * 
     * @param string $field_name
     * @param string $where
     * 
     * @return int
     */
    public static function count(string $field_name = '*', string $where = ''): int
    {
        $class = get_called_class();
        $instance = new $class;

        $table = (is_null($instance->table) ? strtolower(self::getClassWithoutNamespace($class)) : $instance->table);

        $sql = "SELECT count($field_name) as total FROM " . $table;
        $sql .= ($where !== '') ? " WHERE {$where}" : "";
        $sql .= ';';

        $a = KatrinaStatement::executeQuery($sql, false);
        return (int) $a->total;
    }

    /**
     * Return first data in database
     * 
     * @param string $filter
     * 
     * @return mixed
     */
    public static function findFirst(string $filter = ''): mixed
    {
        return self::all($filter, 1);
    }

    /**
     * 'Select' command
     * 
     * @param string $columns
     * 
     * @return self
     */
    public static function select(string $columns = "*"): self
    {
        $class = get_called_class();
        $instance = new $class;

        self::$table_foreign = (is_null($instance->table) ? strtolower(self::getClassWithoutNamespace($class)) : $instance->table);
        self::$id_foreign = (is_null($instance->id) ? 'id' : $instance->id);

        self::$static_sql = "SELECT $columns FROM " . self::$table_foreign;
        self::$table_name = $instance->table;

        return new static;
    }

    /**
     * Return last data in a table
     * 
     * @param string $columns
     * 
     * @return self
     */
    public static function latest(string $column = "created_at"): self
    {
        $class = get_called_class();
        $instance = new $class;

        self::$table_foreign = (is_null($instance->table) ? strtolower(self::getClassWithoutNamespace($class)) : $instance->table);
        self::$id_foreign = (is_null($instance->id) ? 'id' : $instance->id);

        self::$static_sql = "SELECT * FROM " . self::$table_foreign . " ORDER BY " . $column . " DESC";
        self::$table_name = $instance->table;

        return new static;
    }

    /**
     * Returns the values of two tables that have a foreign key
     * 
     * @param string $table_foreign
     * @param string $foreign_key
     * 
     * @return self
     */
    public function innerJoin(string $table_foreign, string $foreign_key): self
    {
        self::$static_sql .= " INNER JOIN " . $table_foreign . " ON " . $table_foreign . "." . $foreign_key . "=" . self::$table_foreign . "." . self::$id_foreign . " ";

        return $this;
    }

    /**
     * Used to specify the number of records to return.
     * 
     * @param int $rows
     * @param int|null $row_count
     * 
     * @return self
     */
    public function limit(int $rows, ?int $row_count = null): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");

        if ($row_count !== null) {
            self::$static_sql .= " LIMIT $rows, $row_count";
        } else {
            self::$static_sql .= " LIMIT $rows";
        }

        return $this;
    }

    /**
     * Used in a WHERE clause to search for a specified pattern in a column.
     * 
     * @param string $like
     * 
     * @return self
     */
    public function like(string $like): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " LIKE '" . $like . "'";

        return $this;
    }

    /**
     * Used to sort the result-set in ascending or descending order.
     * 
     * @param string $column
     * @param bool $asc
     * 
     * @return self
     */
    public function order(string $column, bool $asc = true): self
    {
        if ($asc == false) {
            $this->growing = "DESC";
        }

        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " ORDER BY " . $column . " " . $this->growing;

        return $this;
    }

    /**
     * Selects values within a given range. The values can be numbers, text, or dates.
     * 
     * @param mixed $first_value
     * @param mixed $second_value
     * 
     * @return self
     */
    public function between(mixed $first_value, mixed $second_value): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " BETWEEN " . $first_value . " AND " . $second_value;

        return $this;
    }

    /**
     * Groups rows that have the same values into summary rows, like "find the number of customers in each country".
     * 
     * @param string $column
     * 
     * @return self
     */
    public function group(string $column): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " GROUP BY " . $column;

        return $this;
    }

    /**
     * @param string|array  $condition_1
     * @param null|mixed    $condition_2
     * @param string        $operator
     * 
     * @return self
     */
    public function where(string|array $condition_1, mixed $condition_2 = null, string $operator = "="): self
    {
        if (is_string($condition_1)) {
            self::$static_sql .= " WHERE $condition_1";

            if ($condition_2 != null) {
                if (\is_numeric($condition_2)) {
                    self::$static_sql .= " $operator $condition_2";
                } else if (Functions::getQuery() != "") {
                    self::$static_sql .= " $operator ($condition_2)";
                } else {
                    self::$static_sql .= " $operator '$condition_2'";
                }
            }
        } elseif (is_array($condition_1)) {
            $data = array_chunk($condition_1, 2);
            self::$static_sql .= " WHERE " . $data[0][0] . " = '" . $data[0][1] . "'";

            foreach ($data as $value) {
                self::$static_sql .= " AND " . $value[0] . " = '" . $value[1] . "'";
            }
        }

        return $this;
    }

    /**
     * Displays a record if all the conditions separated by AND are TRUE.
     * 
     * @param string $column
     * @param null $condition
     * @param string $operator
     * 
     * @return self
     */
    public function and(string $column, $condition = null, string $operator = "="): self
    {
        self::$static_sql .= " AND $column";

        if ($condition != null) {
            if (\is_numeric($condition)) {
                self::$static_sql .= " $operator $condition";
            } else if (Functions::getQuery() != "") {
                self::$static_sql .= " $operator ($condition)";
            } else {
                self::$static_sql .= " $operator '$condition'";
            }
        }

        return $this;
    }

    /**
     * Displays a record if any of the conditions separated by OR is TRUE.
     * 
     * @param string $column
     * @param null $condition
     * @param string $operator
     * 
     * @return self
     */
    public function or(string $column, $condition = null, string $operator = "="): self
    {
        self::$static_sql .= " OR $column";

        if ($condition != null) {
            if (\is_numeric($condition)) {
                self::$static_sql .= " $operator $condition";
            } else if (Functions::getQuery() != "") {
                self::$static_sql .= " $operator ($condition)";
            } else {
                self::$static_sql .= " $operator '$condition'";
            }
        }

        return $this;
    }
}
