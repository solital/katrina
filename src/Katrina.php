<?php

namespace Katrina;

use Katrina\Connection\Connection;
use Katrina\Sql\KatrinaStatement;
use Katrina\Sql\Traits\{PaginationTrait, ExtendQueryTrait, DDLTrait};
use Katrina\Exceptions\{KatrinaException, ConnectionException};

class Katrina
{
    use PaginationTrait;
    use ExtendQueryTrait;
    use DDLTrait;

    /**
     * @var const
     */
    public const KATRINA_VERSION = "2.4.0";

    /**
     * @var array
     */
    private array $content;

    /**
     * @var string|null
     */
    protected ?string $table = null;

    /**
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * @var bool
     */
    protected bool $timestamp = true;

    /**
     * @var null|bool
     */
    protected ?bool $cache = null;

    /**
     * @var string|null
     */
    protected static ?string $conn = null;

    /**
     * @var string
     */
    protected string $created_at = 'created_at';

    /**
     * @var string
     */
    protected string $updated_at = 'updated_at';

    /**
     * Construct
     */
    public function __construct()
    {
        if ($this->table == null) {
            $this->table = strtolower(get_class($this));
        }
        if ($this->id == null) {
            $this->id = 'id';
        }

        $this->config();
        self::$cache_instance = new Cache(self::$config['cache']);
        self::$is_cache_active = $this->cache;
    }

    /**
     * @param mixed $parameter
     * @param mixed $value
     */
    public function __set($parameter, $value)
    {
        $this->content[$parameter] = $value;
    }

    /**
     * @param mixed $parameter
     */
    public function __get($parameter)
    {
        return $this->content[$parameter];
    }

    /**
     * @param mixed $parameter
     */
    public function __isset($parameter)
    {
        return isset($this->content[$parameter]);
    }

    /**
     * @param mixed $parameter
     * 
     * @return bool
     */
    public function __unset($parameter)
    {
        if (isset($parameter)) {
            unset($this->content[$parameter]);
            return true;
        }

        return false;
    }

    /**
     * Clone
     */
    private function __clone()
    {
        if (isset($this->content[$this->id])) {
            unset($this->content[$this->id]);
        }
    }

    /**
     * @return void
     */
    private function config(): void
    {
        self::$config = [
            'cache' => $this->cache
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->content;
    }

    /**
     * @param array $array
     */
    public function fromArray(array $array)
    {
        $this->content = $array;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->content);
    }

    /**
     * @param string $json
     */
    public function fromJson(string $json)
    {
        $this->content = json_decode($json);
    }

    /**
     * Save data in database with Actived Record
     * 
     * @return mixed
     * @throws ConnectionException
     */
    public function save(): mixed
    {
        $newContent = $this->convertContent();

        if (isset($this->content[$this->id])) {

            $sets = [];

            foreach ($newContent as $key => $value) {
                if ($key === $this->id || $key == $this->created_at || $key == $this->updated_at)
                    continue;

                $sets[] = "{$key} = {$value}";
            }

            if ($this->timestamp === true) {

                $sets[] = $this->updated_at . " = '" . date('Y-m-d H:i:s') . "'";
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->id} = {$this->content[$this->id]};";
        } else {
            if ($this->timestamp === true) {
                $newContent[$this->created_at] = "'" . date('Y-m-d H:i:s') . "'";
                $newContent[$this->updated_at] = "'" . date('Y-m-d H:i:s') . "'";
            }

            $sql = "INSERT INTO {$this->table} (" . implode(', ', array_keys($newContent)) . ') VALUES (' . implode(',', array_values($newContent)) . ');';
        }

        return KatrinaStatement::executePrepare($sql);
    }

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
        $id = (new $class())->id;
        $table = (new $class())->table;

        $sql = 'SELECT * FROM ' . (is_null($table) ? strtolower($class) : $table);
        $sql .= ' WHERE ' . (is_null($id) ? 'id' : $id);
        $sql .= " = {$id_table} ;";

        if (self::$config['cache'] == true) {
            $cache_values = self::$cache_instance->get($table);

            if (!empty($cache_values) || $cache_values !== false) {
                return $cache_values;
            }
        } else if (self::$config['cache'] == false) {
            self::$cache_instance->delete($table);
        }

        $result_values = KatrinaStatement::executeQuery($sql, false);

        if (self::$config['cache'] == true) {
            self::$cache_instance->set($table, $result_values);
        }

        return $result_values;
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
        $table = (new $class())->table;
        $sql = 'SELECT * FROM ' . (is_null($table) ? strtolower($class) : $table);
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
     * 'Select' command
     * 
     * @param string $columns
     * 
     * @return self
     * @throws KatrinaException
     */
    public static function select(string $columns = "*"): self
    {
        $class = get_called_class();
        $instance = new $class();

        if ($instance->table == null || empty($instance->table)) {
            throw new KatrinaException($class . ': Table name in database not defined');
        }

        self::$table_foreign = $instance->table;
        self::$id_foreign = $instance->id;

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
     * @throws KatrinaException
     */
    public static function latest(string $column = "created_at"): self
    {
        $class = get_called_class();
        $instance = new $class();

        if ($instance->table == null || empty($instance->table)) {
            throw new KatrinaException($class . ': Table name in database not defined');
        }

        self::$table_foreign = $instance->table;
        self::$id_foreign = $instance->id;

        self::$static_sql = "SELECT * FROM " . self::$table_foreign . " ORDER BY " . $column . " DESC";
        self::$table_name = $instance->table;

        return new static;
    }

    /**
     * Insert data in database
     * 
     * @param array $table_columns
     * 
     * @return self
     */
    public static function insert(array $table_columns): self
    {
        $class = get_called_class();
        $instance = new $class();

        $array_columns = array_keys($table_columns);
        $columns = implode(",", $array_columns);

        $array_values = array_values($table_columns);
        $array_values2 = str_repeat("?,", count($array_values));
        $values = rtrim($array_values2, ",");

        try {
            $sql = "INSERT INTO " . $instance->table . " (" . ($columns) . ") VALUES (" . $values . ")";

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
            echo $e->getMessage();
        }
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
    public static function update(array $table_columns): self
    {
        $class = get_called_class();
        $instance = new $class();

        self::$array_columns = array_keys($table_columns);
        self::$array_values = array_values($table_columns);

        self::$static_sql = "UPDATE $instance->table SET ";

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
    public static function delete(string $column, mixed $value, bool $safe_mode = true): mixed
    {
        $class = get_called_class();
        $instance = new $class();
        self::$table_foreign = $instance->table;

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
     */
    public function saveUpdate()
    {
        try {
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
     * @throws ConnectionException
     */
    public static function count(string $field_name = '*', string $where = ''): int
    {
        $class = get_called_class();
        $table = (new $class())->table;
        $sql = "SELECT count($field_name) as total FROM " . (is_null($table) ? strtolower($class) : $table);
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
     * @return self
     */
    public static function findFisrt(string $filter = ''): self
    {
        return self::all($filter, 1);
    }

    /**
     * Check if a table exist in database
     * 
     * @param string $table
     */
    public static function checkTableExists(string $table)
    {
        try {
            $result = Connection::getInstance()->prepare("SHOW TABLES LIKE '$table'");
            return $result->fetchAll(\PDO::FETCH_CLASS);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Define SQL drive for current query
     *
     * @param string $conn
     * 
     * @return self
     */
    public static function connection(string $conn): self
    {
        self::$conn = $conn;
        return new static;
    }

    /**
     * @param mixed $value
     * 
     * @return string
     */
    private function format(mixed $value): string
    {
        if (is_string($value) && !empty($value)) {
            return "'" . addslashes($value) . "'";
        }

        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        if ($value !== '') {
            return $value;
        }

        return "NULL";
    }

    /**
     * @return array
     */
    private function convertContent(): array
    {
        $newContent = [];

        foreach ($this->content as $key => $value) {
            if (is_scalar($value)) {
                $newContent[$key] = $this->format($value);
            }
        }

        return $newContent;
    }
}
