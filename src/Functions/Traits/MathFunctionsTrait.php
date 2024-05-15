<?php

namespace Katrina\Functions\Traits;

trait MathFunctionsTrait
{
    /**
     * Returns the absolute (no-negative) value of a numeric expression or a numeric column
     * 
     * @param string $value
     * @param string $as
     * 
     * @return string
     */
    public static function abs(string $value, string $as = ""): string
    {
        return "ABS({$value})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Allows you to round a number to a specified number of decimal places
     *
     * @param string $value
     * @param int $decimal_places
     * @param string $as
     * 
     * @return string 
     */
    public static function round(string $value, int $decimal_places = 0, string $as = ""): string
    {
        return "ROUND({$value}, {$decimal_places})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Truncates a number to a specified number of decimal places
     * 
     * @param mixed $number
     * @param mixed $decimal_places
     * 
     * @return string
     */
    public static function truncate(mixed $number, mixed $decimal_places, string $as = ""): string
    {
        return "TRUNCATE({$number}, {$decimal_places})" . ($as != "" ? " AS " . $as : "");
    }
}
