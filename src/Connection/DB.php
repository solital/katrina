<?php

namespace Katrina\Connection;
use Katrina\Exception\Exception as Exception;
use PDO;

abstract class DB
{
    /**
     * @var DB
     */
    private static $pdo;

    /**
     * Creates an instance of the connection
     */
    public static function getInstance(): object
    {
        if (!isset(self::$pdo)) {
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

        return self::$pdo;
    }

    /**
     * PDO prepare command
     * @param DB $sql
     */
    public static function prepare($sql): object
    {
        return self::getInstance()->prepare($sql);
    }

    /**
     * PDO query command
     * @param DB $sql
     */
    public static function query($sql): object
    {
        return self::getInstance()->query($sql);
    }

    /**
     * PDO last insert id command
     * @param DB $sql
     */
    public static function lastInsertId()
    {
        return self::getInstance()->lastInsertId();
    }
}
