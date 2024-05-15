<?php

namespace Katrina\Functions\Traits;

trait AggregateFunctionsTrait
{
    /**
     * Calculates the average value of a set of values
     * 
     * @param string $value
     * @param string $as
     * 
     * @return string
     */
    public static function avg(string $value, string $as = ""): string
    {
        return "AVG({$value})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Returns the number of the values in a set
     * 
     * @param string $expression
     * @param string $as
     * 
     * @return string
     */
    public static function count(string $expression = "*", string $as = ""): string
    {
        return "COUNT({$expression})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Returns the maximum value in a set
     * 
     * @param string $value
     * @param string $as
     * 
     * @return string
     */
    public static function max(string $value, string $as = ""): string
    {
        return "MAX({$value})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Returns the minimum value in a set of values
     * 
     * @param string $value
     * @param string $as
     * 
     * @return string
     */
    public static function min(string $value, string $as = ""): string
    {
        return "MIN({$value})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Returns the sum of values in a set
     * 
     * @param string $value
     * @param string $as
     * 
     * @return string
     */
    public static function sum(string $value, string $as = ""): string
    {
        return "SUM({$value})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Concatenates a set of strings and returns the concatenated string
     * 
     * @param string $sql
     * @param string $as
     * 
     * @return string
     */
    public static function groupConcat(string $sql, string $as = ""): string
    {
        return "GROUP_CONCAT({$sql})" . ($as != "" ? " AS " . $as : "");
    }
}
