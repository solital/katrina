<?php

namespace Katrina\Functions\Traits;

trait MathFunctionsTrait
{
    /**
     * @param string $value
     * 
     * @return string
     */
    public static function abs(string $value): string
    {
        return "ABS({$value})";
    }

    /**
     * @param string $value
     * 
     * @return string
     */
    public static function sum(string $value): string
    {
        return "SUM({$value})";
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
