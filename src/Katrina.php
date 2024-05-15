<?php

namespace Katrina;

use Symfony\Component\Uid\Uuid;
use Katrina\Connection\Connection;
use Katrina\Exceptions\KatrinaException;
use Katrina\Sql\KatrinaStatement;
use Katrina\Sql\Traits\{PaginationTrait, ExtendQueryTrait, DataTypesTrait, TableHandleTrait, UuidTrait};

class Katrina
{
    use PaginationTrait, ExtendQueryTrait, DataTypesTrait, TableHandleTrait, UuidTrait;

    /**
     * @var const
     */
    public const KATRINA_VERSION = "2.6.0";

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
     * @var bool|null
     */
    protected ?bool $uuid_increment = false;

    /**
     * Construct
     */
    public function __construct()
    {
        if ($this->table == null) {
            $this->table = strtolower(self::getClassWithoutNamespace($this));
        }

        if ($this->id == null) {
            $this->id = 'id';
        }

        if ($this->uuid_increment === true && DB_CONFIG['DRIVE'] != "mysql") {
            throw new KatrinaException("uuid is available only in MySQL");
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
     * @return array
     */
    public function toArray(): array
    {
        return $this->content;
    }

    /**
     * @param array $array
     * 
     * @return void
     */
    public function fromArray(array $array): void
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
     * 
     * @return void
     */
    public function fromJson(string $json): void
    {
        $this->content = json_decode($json);
    }

    /**
     * Save data in database with Actived Record
     * 
     * @return mixed
     */
    public function save(): mixed
    {
        $new_content = $this->convertContent();

        if (isset($this->content[$this->id])) {
            $sets = [];

            foreach ($new_content as $key => $value) {
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
                $new_content[$this->created_at] = "'" . date('Y-m-d H:i:s') . "'";
                $new_content[$this->updated_at] = "'" . date('Y-m-d H:i:s') . "'";
            }

            if ($this->uuid_increment === true) {
                $new_content[$this->id] = "'" . Uuid::v4()->toBinary() . "'";
            }

            $sql = "INSERT INTO {$this->table} (" . implode(', ', array_keys($new_content)) . ') VALUES (' . implode(',', array_values($new_content)) . ');';
        }

        return KatrinaStatement::executePrepare($sql);
    }

    /**
     * Check if a table exist in database
     * 
     * @param string $table
     * @return mixed
     * 
     * @throws PDOException
     */
    public static function checkTableExists(string $table): mixed
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
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    private static function getClassWithoutNamespace(string|object $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}
