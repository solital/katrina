<?php

namespace Katrina;

use Katrina\Sql\KatrinaStatement;
use Katrina\Sql\Traits\{PaginationTrait, ExtendQueryTrait, DDLTrait};
use Katrina\Connection\Connection;
use Katrina\Exceptions\{KatrinaException, ConnectionException};

class Katrina
{
    use PaginationTrait;
    use ExtendQueryTrait;
    use DDLTrait;

    /**
     * @var const
     */
    public const KATRINA_VERSION = "2.0.0";

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
     * @return mixed
     * @throws ConnectionException
     */
    public function save(): mixed
    {
        $newContent = $this->convertContent();

        if (isset($this->content[$this->id])) {

            $sets = [];

            foreach ($newContent as $key => $value) {
                if ($key === $this->id || $key == 'created_at' || $key == 'updated_at')
                    continue;

                $sets[] = "{$key} = {$value}";
            }

            if ($this->timestamp === true) {

                $sets[] = "updated_at = '" . date('Y-m-d H:i:s') . "'";
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE {$this->id} = {$this->content[$this->id]};";
        } else {
            if ($this->timestamp === true) {
                $newContent['created_at'] = "'" . date('Y-m-d H:i:s') . "'";
                $newContent['updated_at'] = "'" . date('Y-m-d H:i:s') . "'";
            }

            $sql = "INSERT INTO {$this->table} (" . implode(', ', array_keys($newContent)) . ') VALUES (' . implode(',', array_values($newContent)) . ');';
        }

        return KatrinaStatement::executePrepare($sql);
    }

    /**
     * @param int $id_table
     * 
     * @return mixed
     */
    /* public static function find(int $id_table): mixed
    {
        $class = get_called_class();
        $id = (new $class())->id;
        $table = (new $class())->table;

        $sql = 'SELECT * FROM ' . (is_null($table) ? strtolower($class) : $table);
        $sql .= ' WHERE ' . (is_null($id) ? 'id' : $id);
        $sql .= " = {$id_table} ;";

        $result = KatrinaStatement::executeQuery($sql, false);

        if ($result) {
            $newObject = $result->fetchObject(get_called_class());
        }

        return $result;
    } */

    /**
     * @param string $where
     * 
     * @return mixed
     * @throws ConnectionException
     */
    /* public function delete(string $where = ""): mixed
    {
        if ($where != "" || !empty($where)) {
            $sql = "DELETE FROM {$this->table} WHERE {$where};";

            return KatrinaStatement::executePrepare($sql);
        }

        if (isset($this->content[$this->id])) {

            $sql = "DELETE FROM {$this->table} WHERE {$this->id} = {$this->content[$this->id]};";

            return KatrinaStatement::executePrepare($sql);
        }
    } */

    /**
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

        try {
            $result = Connection::getInstance()->query($sql);
            return $result->fetchAll(\PDO::FETCH_CLASS);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param int|null $primary_key
     * @param string|null $where
     * @param string $columns
     * 
     * @return self
     * @throws KatrinaException
     */
    public static function select(int $primary_key = null, string $columns = "*"): self
    {
        $class = get_called_class();
        $instance = new $class();

        if ($instance->table == null || empty($instance->table)) {
            throw new KatrinaException($class . ': Table name in database not defined');
        }

        self::$table_foreign = $instance->table;
        self::$id_foreign = $instance->id;

        self::$static_sql = "SELECT $columns FROM " . self::$table_foreign;

        if (isset($primary_key)) {
            self::$static_sql = "SELECT $columns FROM " . self::$table_foreign . " WHERE " . self::$id_foreign . " = " . $primary_key;
        }

        return new static;
    }

    /**
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
                throw new KatrinaException($res);
            }

            return new static;
        } catch (KatrinaException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return int
     */
    public function lastId(): int
    {
        $last_id = Connection::getInstance()->lastInsertId();
        return (int)$last_id;
    }

    /**
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
     * @param string $where
     * 
     * @return mixed
     */
    public static function delete(string $where): mixed
    {
        $class = get_called_class();
        $instance = new $class();
        self::$table_foreign = $instance->table;

        $sql = "DELETE FROM {$instance->table} WHERE {$where};";

        return KatrinaStatement::executePrepare($sql);
    }

    /**
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
                throw new KatrinaException($res);
            }

            return $res;
        } catch (KatrinaException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param string $fieldName
     * @param string $filter
     * 
     * @return int
     * @throws ConnectionException
     */
    public static function count(string $fieldName = '*', string $filter = ''): int
    {
        $class = get_called_class();
        $table = (new $class())->table;
        $sql = "SELECT count($fieldName) as t FROM " . (is_null($table) ? strtolower($class) : $table);
        $sql .= ($filter !== '') ? " WHERE {$filter}" : "";
        $sql .= ';';

        $a = KatrinaStatement::executeQuery($sql, false);

        return (int) $a['t'];
    }

    /**
     * @param string $filter
     * 
     * @return self
     */
    public static function findFisrt(string $filter = ''): self
    {
        return self::all($filter, 1);
    }

    /**
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
     * @param mixed $value
     * 
     * @return string
     */
    private function format(mixed $value): string
    {
        if (is_string($value) && !empty($value)) {
            return "'" . addslashes($value) . "'";
        } else if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        } else if ($value !== '') {
            return $value;
        } else {
            return "NULL";
        }
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
