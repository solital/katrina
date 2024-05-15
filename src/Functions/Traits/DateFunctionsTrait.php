<?php

namespace Katrina\Functions\Traits;

trait DateFunctionsTrait
{
    /**
     * Return the current date and time
     *
     * @param string $as
     * 
     * @return string 
     */
    public static function now(string $as = ''): string
    {
        return "NOW()" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Return the current date and time
     *
     * @param string $as
     * 
     * @return string 
     */
    public static function currentTimestamp(string $as = ''): string
    {
        return "CURRENT_TIMESTAMP()" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Return the current date
     *
     * @param string $as
     * 
     * @return string 
     */
    public static function curdate(string $as = ''): string
    {
        return "CURDATE()" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Extract the date component from a date
     * 
     * @param string $value
     * @param string $as
     * 
     * @return string
     */
    public static function date(string $value, string $as = ''): string
    {
        return "DATE({$value})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Return the hour for a time
     * 
     * @param string $hour
     * @param string $as
     * 
     * @return string
     */
    public static function hour(string $hour, string $as = ''): string
    {
        return "HOUR({$hour})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Return the day of the month for a specific date (1-31)
     * 
     * @param string $day
     * @param string $as
     * 
     * @return string
     */
    public static function day(string $day = '', string $as = ''): string
    {
        return "DAY({$day})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Return the month component of a date
     * 
     * @param string $month
     * @param string $as
     * 
     * @return string
     */
    public static function month(string $month = '', string $as = ''): string
    {
        return "MONTH({$month})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Return the year component of a date
     * 
     * @param string $year
     * @param string $as
     * 
     * @return string
     */
    public static function year(string $year = '', string $as = ''): string
    {
        return "YEAR({$year})" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Return the difference in days of two date values
     * 
     * @param string $first_date
     * @param string $second_date
     * 
     * @return string
     */
    public static function datediff(string $first_date, string $second_date): string
    {
        return "DATEDIFF({$first_date}, {$second_date})";
    }
}
