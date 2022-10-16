<?php

namespace Katrina\Connection;

use PDO;
use Katrina\Exceptions\ConnectionException;

abstract class Connection
{
    /**
     * @var PDO
     */
    private static PDO $pdo;

    /**
     * @var string
     */
    private static string $dns;

    /**
     * @var string|null
     */
    private static ?string $username = null;

    /**
     * @var string|null
     */
    private static ?string $password = null;

    /**
     * @var array|null
     */
    private static ?array $options = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Creates an instance of the connection
     * 
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        self::getConnection(DB_CONFIG['DRIVE']);

        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(self::$dns, self::$username, self::$password, self::$options);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

                return self::$pdo;
            } catch (\PDOException $e) {
                throw new \PDOException("Database connection error: " . $e->getMessage());
                die();
            }
        }

        return self::$pdo;
    }

    /**
     * @return void
     */
    private static function verifyExtensions(string $drive): void
    {
        if ($drive == "mysql" && !extension_loaded('pdo_mysql')) {
            ConnectionException::driveNotFound($drive);
        } elseif ($drive == "sqlite" && !extension_loaded('pdo_sqlite')) {
            ConnectionException::driveNotFound($drive);
        } elseif ($drive == "pgsql" && !extension_loaded('pdo_pgsql')) {
            ConnectionException::driveNotFound($drive);
        } elseif ($drive == "oci" && !extension_loaded('pdo_oci')) {
            ConnectionException::driveNotFound($drive);
        }
    }

    /**
     * @param string $drive
     * 
     * @return void
     */
    private static function getConnection(string $drive): void
    {
        self::verifyExtensions($drive);

        if ($drive == "mysql") {
            self::connectionMySql($drive);
        } elseif ($drive == "pgsql") {
            self::connectionPgSql($drive);
        } elseif ($drive == "oci") {
            self::connectionOracle($drive);
        } elseif ($drive == "sqlite") {
            self::connectionSqlite();
        }
    }

    /**
     * @param string $drive
     * 
     * @return void
     */
    private static function connectionMySql(string $drive): void
    {
        self::$dns = $drive . ":host=" . DB_CONFIG['HOST'] .
            ";dbname=" . DB_CONFIG['DBNAME'] . ";charset=utf8";
        self::$username = DB_CONFIG['USER'];
        self::$password = DB_CONFIG['PASS'];
        self::$options = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"];
    }

    /**
     * @param string $drive
     * 
     * @return void
     */
    private static function connectionPgSql(string $drive): void
    {
        self::$dns = $drive . ":host=" . DB_CONFIG['HOST'] . ";dbname=" . DB_CONFIG['DBNAME'];
        self::$username = DB_CONFIG['USER'];
        self::$password = DB_CONFIG['PASS'];
    }

    /**
     * @param string $drive
     * 
     * @return void
     */
    private static function connectionOracle(string $drive): void
    {
        self::$dns = $drive . ":dbname=" . DB_CONFIG['DBNAME'];
        self::$username = DB_CONFIG['USER'];
        self::$password = DB_CONFIG['PASS'];
    }

    /**
     * @return void
     */
    private static function connectionSqlite(): void
    {
        self::$dns = "sqlite:" . DB_CONFIG['SQLITE_DIR'] . DIRECTORY_SEPARATOR . DB_CONFIG['DBNAME'];
    }
}
