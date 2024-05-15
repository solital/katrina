<?php

namespace Katrina\Functions\Traits;

trait StringFunctionsTrait
{
    /**
     * Concatenate two or more strings into a single string
     *
     * @param array $strings
     * @param string $as
     * 
     * @return string
     */
    public static function concat(array $strings, string $as = ""): string
    {
        $strings = implode("', '", $strings);
        return "CONCAT('{$strings}')" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Remove all leading spaces from a string
     *
     * @param string $string
     * @param string $as
     * 
     * @return string
     */
    public static function ltrim(string $string, string $as = ""): string
    {
        return "LTRIM('{$string}')" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Remove all trailing spaces from a string
     *
     * @param string $string
     * @param string $as
     * 
     * @return string
     */
    public static function rtrim(string $string, string $as = ""): string
    {
        return "RTRIM('{$string}')" . ($as != "" ? " AS " . $as : "");
    }

    /**
     * Remove unwanted characters from a string
     *
     * @param string $string
     * @param string $as
     * 
     * @return string
     */
    public static function trim(string $string, string $as = ""): string
    {
        return "TRIM('{$string}')" . ($as != "" ? " AS " . $as : "");
    }
}
