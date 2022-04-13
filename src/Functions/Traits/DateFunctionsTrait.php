<?php

namespace Katrina\Functions\Traits;

trait DateFunctionsTrait
{
    /**
     * @return string
     */
    public static function now(): string
    {
        return "NOW()";
    }

    /**
     * @return string
     */
    public static function curdate(): string
    {
        return "CURDATE()";
    }

    /**
     * @param string $value
     * 
     * @return string
     */
    public static function date(string $value): string
    {
        return "DATE({$value})";
    }

    /**
     * @param string $value
     * 
     * @return string
     */
    public static function hour(string $value): string
    {
        return "HOUR({$value})";
    }

    /**
     * @param string $value
     * 
     * @return string
     */
    public static function month(string $value): string
    {
        return "MONTH({$value})";
    }

    /**
     * @param string $first_date
     * @param string $second_date
     * 
     * @return string
     */
    public static function datediff(string $first_date, string $second_date): string
    {
        return "DATEDIFF({$first_date}, {$second_date})";
    }

    /**
     * @param string $date
     * 
     * @return string
     */
    public static function day(string $date = null): string
    {
        if ($date != null) {
            return "DAY({$date})";
        } else {
            return "DAY()";
        }
    }

    /**
     * @return string
     */
    public static function currentTimestamp(): string
    {
        return "CURRENT_TIMESTAMP()";
    }
}
