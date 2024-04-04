<?php

namespace Katrina\Exceptions;

class ConnectionException extends \Exception
{
    /**
     * @deprecated
     * @param string $drive
     * 
     * @throws ConnectionException
     */
    public static function driveNotFound(string $drive)
    {
        throw new ConnectionException("Extension " . $drive . " not installed or not enabled");
    }
}
