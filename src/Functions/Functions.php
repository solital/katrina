<?php

namespace Katrina\Functions;

use SensitiveParameter;
use Symfony\Component\Uid\Uuid;
use Katrina\Functions\Traits\{
    DateFunctionsTrait,
    AggregateFunctionsTrait,
    MathFunctionsTrait,
    StringFunctionsTrait
};

class Functions
{
    use AggregateFunctionsTrait, DateFunctionsTrait, MathFunctionsTrait, StringFunctionsTrait;

    const NULL = 'NULL';

    /**
     * @var string
     */
    private static string $query = "";

    /**
     * Use an raw SQL inside another query
     * 
     * @param string $query
     * 
     * @return string
     */
    public static function subquery(#[SensitiveParameter] string $query): string
    {
        self::$query = $query;
        return self::$query;
    }

    /**
     * Create a custom database function
     *
     * @param string $function_name
     * @param string $query
     * @param string $as
     * 
     * @return string
     */
    public static function custom(
        string $function_name,
        #[SensitiveParameter] string $query,
        string $as = ""
    ): string {
        $function_name = strtoupper($function_name);
        $result = $function_name . "(" . $query . ")";
        return $result . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Get raw SQL query
     * 
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
