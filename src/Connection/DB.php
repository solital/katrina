<?php

namespace Katrina\Connection;
use Katrina\Exception\Exception as Exception;
use Katrina\Version;
use PDO;

abstract class DB extends Version
{
    private static $pdo;

    public static function getInstance()
    {
        try {
            self::$pdo = new \PDO(DB_CONFIG['DRIVE'].":host=".DB_CONFIG['HOST'].
            ";dbname=".DB_CONFIG['DBNAME'].";charset=utf8", DB_CONFIG['USER'], DB_CONFIG['PASS'], 
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"]);

            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
            return self::$pdo; 
        } catch (\PDOException $e) {
            Exception::alertMessage($e, "Database connection error");
            die();
        }
    }

    public static function prepare($sql)
    {
        return self::getInstance()->prepare($sql);
    }

    public static function query($sql)
    {
        return self::getInstance()->query($sql);
    }
}
