<?php

namespace Katrina\Exception;

abstract class Exception 
{
    /**
     * @param PDOException $e
     * @param string $msg
     */
    public static function alertMessage($e, string $msg) 
    {
        include_once 'error-view.php';
        die;
    }    
}
