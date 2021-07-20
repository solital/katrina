<?php

namespace Katrina\Connection;

use PDO;

abstract class DB
{
    /**
     * @var DB
     */
    private static $pdo;

    /**
     * @var string
     */
    private static $dns;

    /**
     * @var string
     */
    private static $username;

    /**
     * @var string
     */
    private static $password;

    /**
     * @var string
     */
    private static $options;

    /**
     * @return string
     */
    private static function verifyExtensions(): string
    {
        $extension = DB_CONFIG['DRIVE'];

        if ($extension == "mysql" && !extension_loaded('pdo_mysql')) {
            throw new \PDOException("Extension $extension not installed or not enabled");
        } elseif ($extension == "sqlite" && !extension_loaded('pdo_sqlite')) {
            throw new \PDOException("Extension $extension not installed or not enabled");
        } elseif ($extension == "pgsql" && !extension_loaded('pdo_pgsql')) {
            throw new \PDOException("Extension $extension not installed or not enabled");
        } elseif ($extension == "oci" && !extension_loaded('pdo_oci')) {
            throw new \PDOException("Extension $extension not installed or not enabled");
        }

        return __CLASS__;
    }

    /**
     * @return string
     */
    public static function getConnection(): string
    {
        self::verifyExtensions();

        $extension = DB_CONFIG['DRIVE'];

        if ($extension == "mysql") {
            self::$dns = DB_CONFIG['DRIVE'] . ":host=" . DB_CONFIG['HOST'] .
                ";dbname=" . DB_CONFIG['DBNAME'] . ";charset=utf8";
            self::$username = DB_CONFIG['USER'];
            self::$password = DB_CONFIG['PASS'];
            self::$options = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"];
        } elseif ($extension == "pgsql") {
            self::$dns = DB_CONFIG['DRIVE'] . ":host=" . DB_CONFIG['HOST'] .
                ";dbname=" . DB_CONFIG['DBNAME'] . ";charset=utf8;user=" . DB_CONFIG['USER'] .
                ";password=" . DB_CONFIG['PASS'];
            self::$username = null;
            self::$password = null;
            self::$options = null;
        } elseif ($extension == "oci") {
            self::$dns = DB_CONFIG['DRIVE'] . ":dbname=" . DB_CONFIG['DBNAME'];
            self::$username = DB_CONFIG['USER'];
            self::$password = DB_CONFIG['PASS'];
            self::$options = null;
        }

        return __CLASS__;
    }

    /**
     * Creates an instance of the connection
     * 
     * @return object
     */
    public static function getInstance(): object
    {
        self::getConnection();

        $drive = DB_CONFIG['DRIVE'];

        if (!isset(self::$pdo)) {
            try {
                if ($drive != "sqlite") {
                    self::$pdo = new \PDO(self::$dns, self::$username, self::$password, self::$options);
                } else {
                    self::$pdo = new \PDO("sqlite:" . DB_CONFIG['SQLITE_DIR'] . DIRECTORY_SEPARATOR . DB_CONFIG['DBNAME']);
                }

                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
                return self::$pdo;
            } catch (\PDOException $e) {
                throw new \PDOException("Database connection error: " . $e->getMessage());
                die();
            }
        }

        return self::$pdo;
    }

    /**
     * PDO prepare command
     * @param DB $sql
     * 
     * @return object
     */
    public static function prepare($sql): object
    {
        return self::getInstance()->prepare($sql);
    }

    /**
     * PDO query command
     * 
     * @param DB $sql
     * 
     * @return object
     */
    public static function query($sql): object
    {
        return self::getInstance()->query($sql);
    }

    /**
     * PDO last insert id command
     * 
     * @param DB $sql
     * 
     * @return array
     */
    public static function lastInsertId()
    {
        return self::getInstance()->lastInsertId();
    }
}
