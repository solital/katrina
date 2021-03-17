<?php

namespace Katrina\Exception;

abstract class Exception
{
    /**
     * @param PDOException $e
     * @param string $msg
     * 
     * @return void
     */
    public static function alertMessage($e, string $msg): void
    {
        include_once 'error-view.php';
        die;
    }

    /**
     * @param string $extension
     * 
     * @return void
     */
    public static function extensionNotFound(string $extension): void
    {
        throw new \Exception("Extension $extension not installed or not enabled");
        die;
    }
}
