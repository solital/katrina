<?php

namespace Katrina\Sql;

use Katrina\Exceptions\KatrinaException;
use Katrina\Connection\Connection as Connection;
use Katrina\Katrina;

abstract class KatrinaStatement
{
    /**
     * @param string $sql
     * 
     * @return mixed
     */
    public static function generate(string $sql): mixed
    {
        $conn = self::checkConnection();

        try {
            $sql = rtrim($sql, ",");
            $stmt = Connection::getInstance($conn)->prepare($sql);
            $res = $stmt->execute();

            return $res;
        } catch (KatrinaException $e) {
            throw new KatrinaException($e->getMessage());
        }
    }

    /**
     * @param string $sql
     * @param bool $all
     * 
     * @return mixed
     */
    public static function executeQuery(string $sql, ?bool $all): mixed
    {
        $conn = self::checkConnection();

        try {
            $sql = rtrim($sql, ",");
            $stmt = Connection::getInstance($conn)->query($sql);
            $stmt->execute();

            if ($all == false) {
                $res = $stmt->fetch(\PDO::FETCH_OBJ);
            } else if ($all == true) {
                $res = $stmt->fetchAll(\PDO::FETCH_OBJ);
            }

            return $res;
        } catch (KatrinaException $e) {
            throw new KatrinaException($e->getMessage());
        }
    }

    /**
     * @param string $sql
     * 
     * @return mixed
     */
    public static function executePrepare(string $sql): mixed
    {
        $conn = self::checkConnection();

        try {
            $sql = rtrim($sql, ",");
            $stmt = Connection::getInstance($conn)->prepare($sql);
            $res = $stmt->execute();

            return $res;
        } catch (KatrinaException $e) {
            throw new KatrinaException($e->getMessage());
        }
    }

    /**
     * @param string $sql
     * 
     * @return mixed
     */
    public static function executeFetchAll(string $sql): mixed
    {
        $conn = self::checkConnection();

        try {
            $sql = rtrim($sql, ",");
            $stmt = Connection::getInstance($conn)->query($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $res;
        } catch (KatrinaException $e) {
            throw new KatrinaException($e->getMessage());
        }
    }

    /**
     * @return string|null
     */
    private static function checkConnection(): ?string
    {
        $reflection = new \ReflectionClass(Katrina::class);
        return $reflection->getProperty('conn')->getValue();
    }
}
