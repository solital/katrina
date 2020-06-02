<?php

namespace Katrina\Sql;
use Katrina\Connection\DB as DB;
use Katrina\Exception\Exception;
use Katrina\Pagination\Pagination;
use PDO;

abstract class Custom extends Pagination
{
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
