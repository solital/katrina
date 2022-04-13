<?php

namespace Katrina\Functions;

use Katrina\Functions\Traits\DateFunctionsTrait;
use Katrina\Functions\Traits\AggregateFunctionsTrait;
use Katrina\Functions\Traits\MathFunctionsTrait;

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
}
