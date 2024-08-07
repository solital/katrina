<?php

namespace Katrina\Sql;

use Katrina\Exceptions\KatrinaException;
use Katrina\Connection\Connection;
use Katrina\Katrina;
use SensitiveParameter;

abstract class KatrinaStatement
{
    /**
     * Get one or many data from table
     * 
     * @param string $sql
     * @param bool $all
     * 
     * @return mixed
     * @throws KatrinaException
     */
    public static function executeQuery(
        #[SensitiveParameter] string $sql,
        #[SensitiveParameter] ?bool $all
    ): mixed {
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
     * Execute SQL command with PDO `prepare` method
     * 
     * @param string $sql
     * 
     * @return mixed
     * @throws KatrinaException
     */
    public static function executePrepare(#[SensitiveParameter] string $sql): mixed
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
     * @return string|null
     */
    private static function checkConnection(): ?string
    {
        $reflection = new \ReflectionClass(Katrina::class);
        return $reflection->getProperty('conn')->getValue();
    }
}
