<?php

namespace Katrina\Sql;
use Katrina\Sql\Create;
use Katrina\Connection\DB as DB;
use Katrina\Exception\Exception;
use PDO;

abstract class Pagination extends Create
{
    /**
     * @param string $table
     * @param int $limit
     * @param array|null $innerjoin
     * @param string|null $where
     * @param string $previous_name
     * @param string $next_name
     * 
     * @return array
     */
    public function pagination(string $table, int $limit, array $innerjoin = null, string $where = null, string $previous_name = "<<", string $next_name = ">>"): array
    {

        if ($limit == 0 || $limit <= 0) {
            Exception::alertMessage(null, "'pagination()' error - Division by zero");
            die;
        }

        $pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
        $start = ($pg * $limit) - $limit;

        try {
            $sql = "SELECT * FROM $table";

            if ($innerjoin != null) {
                $sql = "SELECT * FROM $table a INNER JOIN ". $innerjoin[0] ." b ON a.". $innerjoin[1] ."=b.".$innerjoin[2];
            }

            if ($where != null) {
                $sql .= " WHERE $where LIMIT $start, $limit";
            } else {
                $sql .= " LIMIT $start, $limit";
            }
            
            $stmt = DB::query($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sql = "SELECT * FROM $table";
            if (isset($innerjoin)) {
                $sql = "SELECT * FROM $table a INNER JOIN ". $innerjoin[0] ." b ON a.". $innerjoin[1] ."=b.".$innerjoin[2].";";
            }
            $stmt = DB::query($sql);
            $stmt->execute();
            $count = $stmt->rowCount();

            $qtdPag = ceil($count/$limit);

            $html = "<a href='?page=1' class='pagination_first_item'>$previous_name</a> ";

            if($qtdPag > 1 && $pg<= $qtdPag){
                for($i=1; $i <= $qtdPag; $i++){
                    if($i == $pg){
                        $html .= " <span class='pagination_atual_item'>".$i."</span> ";
                    } else {
                        $html .= " <a href='?page=$i' class='pagination_others_itens'>".$i."</a> ";
                    }
                }
            }

            $html .= " <a href=\"?page=$qtdPag\" class='pagination_last_item'>$next_name</a>";
            
            if ($rows == NULL || empty($rows)) {
                return [
                    "rows" => '',
                    "arrows" => ''
                ];
            }
            
            if ($qtdPag == 1) {
                return [
                    "rows" => $rows,
                    "arrows" => ''
                ];
            }

            return [
                "rows" => $rows, 
                "arrows" => $html
            ];
        } catch (\PDOException $e) {
            Exception::alertMessage($e, "'pagination()' error");
        }
    }

    /**
     * @param string $query
     * @param int $limit
     * @param string $previous_name
     * @param string $next_name
     * 
     * @return array
     */
    public function customPagination(string $query, int $limit, string $previous_name = "<<", string $next_name = ">>"): array
    {

        if ($limit == 0 || $limit <= 0) {
            Exception::alertMessage(null, "'customPagination()' error - Division by zero");
            die;
        }

        $pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
        $start = ($pg * $limit) - $limit;

        try {
            $sql = $query." LIMIT $start, $limit";
            $stmt = DB::query($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sql = $query;
            $stmt = DB::query($sql);
            $stmt->execute();
            $count = $stmt->rowCount();

            $qtdPag = ceil($count/$limit);

            $html = "<a href='?page=1' class='pagination_first_item'>$previous_name</a> ";

            if($qtdPag > 1 && $pg<= $qtdPag){
                for($i=1; $i <= $qtdPag; $i++){
                    if($i == $pg){
                        $html .= " <span class='pagination_atual_item'>".$i."</span> ";
                    } else {
                        $html .= " <a href='?page=$i' class='pagination_others_itens'>".$i."</a> ";
                    }
                }
            }

            $html .= " <a href=\"?page=$qtdPag\" class='pagination_last_item'>$next_name</a>";
            
            if ($rows == NULL || empty($rows)) {
                return [
                    "rows" => '',
                    "arrows" => ''
                ];
            }
            
            if ($qtdPag == 1) {
                return [
                    "rows" => $rows,
                    "arrows" => ''
                ];
            }

            return [
                "rows" => $rows, 
                "arrows" => $html
            ];
        } catch (\PDOException $e) {
            Exception::alertMessage($e, "'customPagination()' error");
        }
    }
}