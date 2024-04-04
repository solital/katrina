<?php

namespace Katrina\Sql\Traits;

use Katrina\Cache;
use Katrina\Exceptions\KatrinaException;
use Katrina\Sql\KatrinaStatement;

trait DDLTrait
{
    /**
     * @var bool
     */
    protected bool $created_update_at = false;

    /**
     * @var string
     */
    protected static string $static_sql;

    /**
     * @var null|string
     */
    protected static ?string $backtips = null;

    /**
     * @var array
     */
    protected static array $config = [];

    /**
     * @var Cache
     */
    protected static Cache $cache_instance;

    /**
     * @var bool|null
     */
    protected static ?bool $is_cache_active;

    /**
     * @var string
     */
    protected static string $table_name;

    /**
     * @return void
     */
    private static function getBacktips(): void
    {
        self::$backtips = match (DB_CONFIG['DRIVE']) {
            'mysql' => "`",
            default => ''
        };
    }

    /**
     * @param string $table
     * 
     * @return mixed
     */
    public static function dropTable(string $table): mixed
    {
        self::$static_sql = "DROP TABLE IF EXISTS $table;";
        return KatrinaStatement::executePrepare(self::$static_sql);
    }

    /**
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
     * @return mixed
     */
    public function closeTable(): mixed
    {
        self::$static_sql = rtrim(self::$static_sql, ", ");
        self::$static_sql .= ");";

        return KatrinaStatement::executePrepare(self::$static_sql);
    }

    /**
     * @return self
     */
    public function createdUpdatedAt(string $created_at_name = 'created_at', string $updated_at_name = 'updated_at'): self
    {
        self::getBacktips();

        $this->created_update_at = true;
        self::$static_sql .= self::$backtips . $created_at_name . self::$backtips . " TIMESTAMP DEFAULT CURRENT_TIMESTAMP, " . self::$backtips . $updated_at_name . self::$backtips . " DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";

        return $this;
    }

    /**
     * @param string $table
     * 
     * @return mixed
     */
    public static function describeTable(string $table): mixed
    {
        self::$static_sql = "DESCRIBE " . $table;
        return KatrinaStatement::executeQuery(self::$static_sql, true);
    }

    /**
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
     * @param string $table
     * @param bool $check_foreign_key
     * 
     * @return mixed
     */
    public function truncate(string $table, bool $check_foreign_key = false): mixed
    {
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
     * @param string $table
     * 
     * @return self
     */
    public function alter(string $table): self
    {
        self::getBacktips();

        self::$static_sql = "ALTER TABLE " . self::$backtips . $table . self::$backtips . " KATRINA_STR_REPLACE ";
        return $this;
    }

    /**
     * @return mixed
     */
    public function add(): mixed
    {
        self::$static_sql = str_replace("KATRINA_STR_REPLACE", "ADD COLUMN", self::$static_sql);
        self::$static_sql = rtrim(self::$static_sql, ",");

        return KatrinaStatement::executePrepare(self::$static_sql);
    }

    /**
     * @return mixed
     */
    public function modify(): mixed
    {
        self::$static_sql = str_replace("KATRINA_STR_REPLACE", "MODIFY COLUMN", self::$static_sql);
        return KatrinaStatement::executePrepare(self::$static_sql);
    }

    /**
     * @param string $new_name
     * 
     * @return mixed
     */
    public function rename(string $new_name): mixed
    {
        self::$static_sql = str_replace("KATRINA_STR_REPLACE", "RENAME TO", self::$static_sql);
        self::$static_sql .= $new_name . ";";

        return KatrinaStatement::executePrepare(self::$static_sql);
    }

    /**
     * @param string $old_table
     * @param string $new_name
     * 
     * @return mixed
     */
    public function renameTable(string $old_table, string $new_name): mixed
    {
        self::getBacktips();

        self::$static_sql = "RENAME TABLE " . self::$backtips . $old_table . self::$backtips . " TO " . self::$backtips . $new_name . self::$backtips . ";";
        return KatrinaStatement::executePrepare(self::$static_sql);
    }

    /**
     * @param string $column
     * 
     * @return mixed
     */
    public function drop(string $column): mixed
    {
        self::$static_sql = str_replace("KATRINA_STR_REPLACE ", "", self::$static_sql);
        self::$static_sql .= "DROP COLUMN $column;";

        return KatrinaStatement::executePrepare(self::$static_sql);
    }

    /**
     * @param string $foreign_key
     * 
     * @return self
     */
    public function foreign(string $foreign_key): self
    {
        self::getBacktips();

        self::$static_sql .= "FOREIGN KEY (" . self::$backtips . $foreign_key . self::$backtips . ") ";
        return $this;
    }

    /**
     * @param string $constraint
     * 
     * @return self
     */
    public function constraint(string $constraint): self
    {
        self::getBacktips();

        if (str_contains(self::$static_sql, "KATRINA_STR_REPLACE")) {
            self::$static_sql = str_replace("KATRINA_STR_REPLACE ", "", self::$static_sql);
            self::$static_sql .= "ADD CONSTRAINT " . self::$backtips . $constraint . self::$backtips . " ";
        } else {
            self::$static_sql .= "CONSTRAINT " . self::$backtips . $constraint . self::$backtips . " ";
        }

        return $this;
    }

    /**
     * @param string $references
     * @param string $id
     * 
     * @return mixed
     */
    public function references(string $references, string $id): mixed
    {
        self::getBacktips();

        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= "REFERENCES " . self::$backtips . $references . self::$backtips . "(" . self::$backtips . $id . self::$backtips . "),";
        self::$static_sql = rtrim(self::$static_sql, ",");

        if (str_contains(self::$static_sql, "ALTER TABLE")) {
            return KatrinaStatement::executePrepare(self::$static_sql);
        } else {
            return $this;
        }
    }

    /**
     * @return self
     */
    public function primary(): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " PRIMARY KEY,";

        return $this;
    }

    /**
     * @return self
     */
    public function notNull(): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " NOT NULL,";

        return $this;
    }

    /**
     * @param string $action
     * 
     * @return self
     */
    public function onDelete(string $action): self
    {
        $action = strtoupper($action);
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " ON DELETE $action,";

        return $this;
    }

    /**
     * @param string $action
     * 
     * @return self
     */
    public function onUpdate(string $action): self
    {
        $action = strtoupper($action);
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " ON UPDATE $action,";

        return $this;
    }

    /**
     * @param string $default
     * 
     * @return self
     */
    public function default(string $default): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");

        if ($default == 'CURRENT_TIMESTAMP' || $default == 'current_timestamp') {
            $default = strtoupper($default);
            self::$static_sql .= " DEFAULT $default,";
        } else {
            self::$static_sql .= " DEFAULT '$default',";
        }

        return $this;
    }

    /**
     * @return self
     */
    public function unique(): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " UNIQUE,";

        return $this;
    }

    /**
     * @return self
     */
    public function unsigned(): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " UNSIGNED,";

        return $this;
    }

    /**
     * @param string $column
     * 
     * @return self
     */
    public function after(string $column): self
    {
        $comma = substr(self::$static_sql, -1);

        if ($comma == ",") {
            self::$static_sql = str_replace(",", "", self::$static_sql);
        }

        self::$static_sql .= " AFTER $column;";

        return $this;
    }

    /**
     * @return self
     */
    public function first(): self
    {
        $comma = substr(self::$static_sql, -1);

        if ($comma == ",") {
            self::$static_sql = str_replace(",", "", self::$static_sql);
        }

        self::$static_sql .= " FIRST";

        return $this;
    }

    /**
     * MYSQL
     * 
     * @return self
     */
    public function increment(): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " AUTO_INCREMENT,";

        return $this;
    }

    /**
     * POSTGRESQL
     * 
     * @return self
     */
    public function serial(string $field): self
    {
        self::getBacktips();

        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= self::$backtips . $field . self::$backtips . " SERIAL,";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function boolean(string $field): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " BOOLEAN,";
        return $this;
    }

    /**
     * @param string $field
     * @param int $value1
     * @param int $value2
     * 
     * @return self
     */
    public function decimal(string $field, int $value1, int $value2): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " DECIMAL($value1, $value2),";
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function char(string $field, int $size): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " CHAR($size),";
        return $this;
    }

    /**
     * @param string $field
     * @param string $size
     * 
     * @return self
     */
    public function varchar(string $field, int $size): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " VARCHAR($size),";
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function tinytext(string $field): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " TINYTEXT,";
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function mediumtext(string $field): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " MEDIUMTEXT,";
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function longtext(string $field): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " LONGTEXT,";
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function text(string $field): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " TEXT,";
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function tinyint(string $field, int $size): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " TINYINT($size),";
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function smallint(string $field, int $size): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " SMALLINT($size),";
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function mediumint(string $field, int $size): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " MEDIUMINT($size),";
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function bigint(string $field, int $size): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " BIGINT($size),";
        return $this;
    }

    /**
     * @param string $field
     * @param null|int $size
     * 
     * @return self
     */
    public function int(string $field, ?int $size = null): self
    {
        self::getBacktips();

        if ($size == null) {
            self::$static_sql .= self::$backtips . $field . self::$backtips . " INT,";
        } else {
            self::$static_sql .= self::$backtips . $field . self::$backtips . " INT($size),";
        }

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function date(string $field): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " DATE,";
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function year(string $field): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " YEAR,";
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function time(string $field): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " TIME,";
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function datetime(string $field): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " DATETIME,";
        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function timestamp(string $field): self
    {
        self::getBacktips();

        self::$static_sql .= self::$backtips . $field . self::$backtips . " TIMESTAMP,";
        return $this;
    }
}
