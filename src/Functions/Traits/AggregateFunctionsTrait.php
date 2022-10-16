<?php

namespace Katrina\Functions\Traits;

trait AggregateFunctionsTrait
{
    /**
     * @param string $value
     * @param string $as
     * 
     * @return string
     */
    public static function avg(string $value, string $as = ""): string
    {
        if ($as != "") {
            return "AVG({$value}) AS {$as}";
        } else {
            return "AVG({$value})";
        }
    }

    /**
     * @param string $expression
     * @param string $as
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
     * @param string $as
     * 
     * @return string
     */
    public static function max(string $value, string $as = ""): string
    {
        if ($as != "") {
            return "MAX({$value}) AS {$as}";
        } else {
            return "MAX({$value})";
        }
    }

    /**
     * @param string $value
     * @param string $as
     * 
     * @return string
     */
    public static function min(string $value, string $as = ""): string
    {
        if ($as != "") {
            return "MIN({$value}) AS {$as}";
        } else {
            return "MIN({$value})";
        }
    }
}
