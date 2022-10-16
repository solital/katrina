<?php

namespace Katrina\Functions\Traits;

trait MathFunctionsTrait
{
    /**
     * @param string $value
     * @param string $as
     * 
     * @return string
     */
    public static function abs(string $value, string $as = ''): string
    {
        if ($as != '') {
            return "ABS({$value}) AS {$as}";
        } else {
            return "ABS({$value})";
        }
    }

    /**
     * @param string $value
     * @param string $as
     * 
     * @return string
     */
    public static function sum(string $value, string $as = ''): string
    {
        if ($as != '') {
            return "SUM({$value}) AS {$as}";
        } else {
            return "SUM({$value})";
        }
    }

    /**
     * @param mixed $number
     * @param mixed $decimal_places
     * 
     * @return string
     */
    public static function truncate(mixed $number, mixed $decimal_places): string
    {
        return "TRUNCATE({$number}, {$decimal_places})";
    }
}
