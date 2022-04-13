<?php

namespace Katrina\Sql;

use Katrina\Exceptions\KatrinaException;
use Katrina\Connection\Connection as Connection;

abstract class KatrinaStatement
{
    /**
     * @param string $sql
     * 
     * @return mixed
     */
    public static function generate(string $sql): mixed
    {
        try {
            $sql = rtrim($sql, ",");
            $stmt = Connection::getInstance()->prepare($sql);
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
        try {
            $sql = rtrim($sql, ",");
            $stmt = Connection::getInstance()->query($sql);
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
        try {
            $sql = rtrim($sql, ",");
            $stmt = Connection::getInstance()->prepare($sql);
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
        try {
            $sql = rtrim($sql, ",");
            $stmt = Connection::getInstance()->query($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $res;
        } catch (KatrinaException $e) {
            throw new KatrinaException($e->getMessage());
        }
    }
}
