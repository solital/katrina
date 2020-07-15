<?php

namespace Katrina\Sql;
use Katrina\Connection\DB as DB;
use Katrina\Exception\Exception;
use Katrina\Sql\Pagination as Pagination;
use PDO;

abstract class Custom extends Pagination
{
    /**
     * Create custom SELECT command statement returning all data
     * @param string $query custom select command
     */
    public function customQueryAll(string $query): ?array
    {
        try {
            $stmt = DB::query($query);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            return $res;
        } catch (\PDOException $e) {
            Exception::alertMessage($e, "'customQueryAll()' error");
        }
    }

    /**
     * Create custom SELECT command statement returning a single data
     * @param string $query custom select command
     */
    public function customQueryOnly(string $query)
    {
        try {
            $stmt = DB::query($query);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
            return $res;
        } catch (\PDOException $e) {
            Exception::alertMessage($e, "'customQueryOnly()' error");
        }
    }
}
