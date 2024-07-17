<?php

namespace Katrina\Connection;

use Katrina\Exceptions\ConnectionException;

class Connection
{
    /**
     * @var \PDO
     */
    private static \PDO $pdo;

    /**
     * @var string
     */
    private static string $dns = "";

    /**
     * @var string|null
     */
    private static ?string $db_drive = null;

    /**
     * @var string|null
     */
    private static ?string $db_host = null;

    /**
     * @var string|null
     */
    private static ?string $db_name = null;

    /**
     * @var string|null
     */
    private static ?string $db_user = null;

    /**
     * @var string|null
     */
    private static ?string $db_pass = null;

    /**
     * @var string|null
     */
    private static ?string $sqlite_dir = null;

    /**
     * @var array|null
     */
    private static ?array $options = null;

    /**
     * Creates connection with database and return PDO instance
     * 
     * @param null|string $drive Set database drive
     * 
     * @return \PDO
     * @throws ConnectionException
     */
    public static function getInstance(?string $drive = null): \PDO
    {
        self::defineConfigConstants($drive);

        if (!is_null($drive)) {
            self::getConnection($drive);
        } else {
            self::getConnection(self::$db_drive);
        }

        if (empty(self::$dns)) {
            throw new ConnectionException("Main database not configured. Check your '.env' file or constants");
        }

        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new \PDO(self::$dns, self::$db_user, self::$db_pass, self::$options);
                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

                return self::$pdo;
            } catch (\PDOException $e) {
                throw new \PDOException("Database connection error: " . $e->getMessage());
            }
        }

        return self::$pdo;
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
        }

        if ($drive == "pgsql") {
            self::connectionPgSql($drive);
        }

        if ($drive == "oci") {
            self::connectionOracle($drive);
        }

        if ($drive == "sqlite") {
            self::connectionSqlite();
        }
    }

    /**
     * Set configurations using constants 'DB_CONFIG' and 'DB_CONFIG_SECONDARY'
     * 
     * @param null|string $drive Set database drive
     *
     * @return void
     */
    private static function defineConfigConstants(?string $drive): void
    {
        if (!is_null($drive)) {
            if (!defined('DB_CONFIG_SECONDARY')) {
                throw new ConnectionException("Second database not configured. Check your '.env' file or constants");
            }

            self::$db_host = DB_CONFIG_SECONDARY['HOST'];
            self::$db_name = DB_CONFIG_SECONDARY['DBNAME'];
            self::$db_user = DB_CONFIG_SECONDARY['USER'];
            self::$db_pass = DB_CONFIG_SECONDARY['PASS'];

            if (isset(DB_CONFIG_SECONDARY['SQLITE_DIR'])) {
                self::$sqlite_dir = DB_CONFIG_SECONDARY['SQLITE_DIR'];
            }
        } else {
            if (!defined('DB_CONFIG')) {
                throw new ConnectionException("Main database not configured. Check your '.env' file or constants");
            }

            self::$db_drive = DB_CONFIG['DRIVE'];
            self::$db_host = DB_CONFIG['HOST'];
            self::$db_name = DB_CONFIG['DBNAME'];
            self::$db_user = DB_CONFIG['USER'];
            self::$db_pass = DB_CONFIG['PASS'];

            if (isset(DB_CONFIG['SQLITE_DIR'])) {
                self::$sqlite_dir = DB_CONFIG['SQLITE_DIR'];
            }
        }
    }

    /**
     * @param string $drive
     * 
     * @return void
     */
    private static function connectionMySql(string $drive): void
    {
        self::$dns = $drive . ":host=" . self::$db_host . ";dbname=" . self::$db_name . ";charset=utf8";
        self::$options = [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"];
    }

    /**
     * @param string $drive
     * 
     * @return void
     */
    private static function connectionPgSql(string $drive): void
    {
        self::$dns = $drive . ":host=" . self::$db_host . ";dbname=" . self::$db_name;
    }

    /**
     * @param string $drive
     * 
     * @return void
     */
    private static function connectionOracle(string $drive): void
    {
        self::$dns = $drive . ":dbname=" . self::$db_name;
    }

    /**
     * @return void
     */
    private static function connectionSqlite(): void
    {
        self::$dns = "sqlite:" . self::$sqlite_dir . DIRECTORY_SEPARATOR . self::$db_name;
    }

    /**
     * @param string $drive
     * 
     * @return void
     * @throws ConnectionException
     */
    private static function verifyExtensions(string $drive): void
    {
        try {
            if ($drive == "mysql" && !extension_loaded('pdo_mysql')) {
                throw new ConnectionException("Extension for MySQL not installed or not enabled");
            }

            if ($drive == "pgsql" && !extension_loaded('pdo_pgsql')) {
                throw new ConnectionException("Extension for PostgreSQL not installed or not enabled");
            }

            if ($drive == "sqlite" && !extension_loaded('pdo_sqlite')) {
                throw new ConnectionException("Extension for SQLite not installed or not enabled");
            }

            if ($drive == "oci" && !extension_loaded('pdo_oci')) {
                throw new ConnectionException("Extension for Oracle not installed or not enabled");
            }
        } catch (ConnectionException $e) {
            die(ConnectionException::class . ": " . $e->getMessage());
        }
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
