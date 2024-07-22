<?php

namespace Katrina\Sql\Traits;

use Katrina\Sql\Traits\DataTypesTrait;
use Katrina\Sql\KatrinaStatement;

trait ExtendQueryTrait
{
    use DataTypesTrait, CreateUpdateSQLTrait, ReadSQLTrait;

    /**
     * @var null|string
     */
    protected ?string $sql;

    /**
     * @var string
     */
    private string $growing = "ASC";

    /**
     * Get an SQL string
     * 
     * @return string
     */
    public function rawQuery(): string
    {
        return self::$static_sql;
    }

    /**
     * Call a procedure
     * 
     * @param string $procedure
     * @param array|null $params
     * 
     * @return self
     */
    public static function call(string $procedure, array $params = null): self
    {
        $values = null;
        if ($params) $values = implode(", ", $params);
        $sql = "CALL $procedure($values)";
        return KatrinaStatement::executePrepare($sql);
    }

    /**
     * Get all results from a query
     * 
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->getAllOrUnique(true);
    }

    /**
     * Get a unique result from a query
     * 
     * @return mixed
     */
    public function getUnique(): mixed
    {
        return $this->getAllOrUnique(false);
    }

    private function getAllOrUnique(bool $all): mixed
    {
        if (self::$config['cache'] == true) {
            $cache_values = self::$cache_instance->get(self::$table_name);
            if (!empty($cache_values) || $cache_values !== false) return $cache_values;
        } else if (self::$config['cache'] == false) {
            self::$cache_instance->delete(self::$table_name);
        }

        $result_values = KatrinaStatement::executeQuery(self::$static_sql, $all);

        if (self::$config['cache'] == true) self::$cache_instance->set(self::$table_name, $result_values);
        return $result_values;
    }
}
