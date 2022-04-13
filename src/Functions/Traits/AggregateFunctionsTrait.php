<?php

namespace Katrina\Functions\Traits;

trait AggregateFunctionsTrait
{
    /**
     * @param string $value
     * 
     * @return string
     */
    public static function avg(string $value): string
    {
        return "AVG({$value})";
    }

    /**
     * @param string $expression
     * 
     * @return string
     */
    public static function count(string $expression = "*", string $as = ""): string
    {
        if ($as != "") {
            return "COUNT({$expression}) AS {$as}";
        } else {
            return "COUNT({$expression})";
        }
    }

    /**
     * @param string $value
     * 
     * @return string
     */
    public static function max(string $value): string
    {
        return "MAX({$value})";
    }

    /**
     * @param string $value
     * 
     * @return string
     */
    public static function min(string $value): string
    {
        return "MIN({$value})";
    }
}
