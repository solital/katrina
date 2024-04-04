<?php

namespace Katrina\Functions;

use Katrina\Functions\Traits\{
    DateFunctionsTrait,
    AggregateFunctionsTrait,
    MathFunctionsTrait
};
use Symfony\Component\Uid\Uuid;

class Functions
{
    use AggregateFunctionsTrait;
    use DateFunctionsTrait;
    use MathFunctionsTrait;

    const NULL = 'NULL';

    /**
     * @var string
     */
    private static string $query = "";

    /**
     * @param string $query
     * 
     * @return string
     */
    public static function subquery(string $query): string
    {
        self::$query = $query;
        return self::$query;
    }

    /**
     * @return string
     */
    public static function getQuery(): string
    {
        return self::$query;
    }

    /**
     * Generate a raw binary string
     *
     * @return string
     */
    public static function uuidToBin(): string
    {
        return Uuid::v4()->toBinary();
    }

    /**
     * Returns the identifier as a RFC4122 case insensitive string
     *
     * @param string $binary_uuid
     * 
     * @return string
     */
    public static function binToUuid(string $binary_uuid): string
    {
        return Uuid::fromBinary($binary_uuid)->toRfc4122();
    }
}
