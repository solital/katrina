<?php

namespace Katrina\Sql\Traits;

use Katrina\Exceptions\KatrinaException;

trait UuidTrait
{
    use DataTypesTrait;

    /**
     * @param string $field
     * 
     * @return self
     */
    public function uuid(string $field): self
    {
        self::getBacktips();

        $type = match (DB_CONFIG['DRIVE']) {
            "mysql" => "BINARY(16)",
            default => throw new KatrinaException("uuid is available only in MySQL")
        };

        self::$static_sql .= self::$backtips . $field . self::$backtips . " $type,";
        return $this;
    }
}
