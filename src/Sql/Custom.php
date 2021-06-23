<?php

namespace Katrina\Sql;
use Katrina\Connection\DB as DB;
use Katrina\Exception\Exception;
use Katrina\Sql\Pagination as Pagination;
use PDO;

abstract class Custom extends Pagination
{
    /**
     * @param string $query
     * 
     * @return array|null
     */
    public function customQueryAll(string $query)
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
     * @param string $query
     * 
     * @return array
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
