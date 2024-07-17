<?php

namespace Katrina\Sql\Traits;

use Katrina\Connection\Connection as Connection;
use Katrina\Exceptions\PaginationException;
use PDO;
use SensitiveParameter;

trait PaginationTrait
{
    /**
     * @var int
     */
    protected int $qtdPag;

    /**
     * @var mixed
     */
    protected mixed $rows;

    /**
     * @var int
     */
    protected int $pg;

    /**
     * Creates a system for paging results
     * 
     * @param string $table
     * @param int $limit
     * @param array|null $innerjoin
     * @param string|null $where
     * 
     * @return self
     */
    public function pagination(
        #[SensitiveParameter] string $table,
        int $limit,
        #[SensitiveParameter] array $innerjoin = null,
        #[SensitiveParameter] string $where = null
    ): self {
        if ($limit == 0 || $limit <= 0) {
            throw new PaginationException("Error in 'pagination(): Division by zero'");
        }

        $this->pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
        $start = ($this->pg * $limit) - $limit;

        try {
            $sql = "SELECT * FROM $table";

            if ($innerjoin != null) {
                $sql = "SELECT * FROM $table a INNER JOIN " . $innerjoin[0] . " b ON a." . $innerjoin[1] . "=b." . $innerjoin[2];
            }

            if ($where != null) {
                $sql .= " WHERE $where LIMIT $start, $limit";
            } else {
                $sql .= " LIMIT $start, $limit";
            }

            $stmt = Connection::getInstance()->query($sql);
            $stmt->execute();
            $this->rows = $stmt->fetchAll(PDO::FETCH_OBJ);

            $sql = "SELECT * FROM $table";

            if (isset($innerjoin)) {
                $sql = "SELECT * FROM $table a INNER JOIN " . $innerjoin[0] . " b ON a." . $innerjoin[1] . "=b." . $innerjoin[2] . ";";
            }

            $stmt = Connection::getInstance()->query($sql);
            $stmt->execute();
            $count = $stmt->rowCount();

            $this->qtdPag = ceil($count / $limit);

            return $this;
        } catch (PaginationException $e) {
            throw new PaginationException("Error in 'pagination()': " . $e->getMessage());
        }
    }

    /**
     * Creates a system for paging results with custom SQL query
     * 
     * @param string $query
     * @param int $limit
     * @param string $previous_name
     * @param string $next_name
     * 
     * @return self
     */
    public function customPagination(#[SensitiveParameter] string $query, int $limit): self
    {
        if ($limit == 0 || $limit <= 0) {
            throw new PaginationException("Error in 'pagination(): Division by zero'");
        }

        $this->pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
        $start = ($this->pg * $limit) - $limit;

        try {
            $sql = $query . " LIMIT $start, $limit";
            $stmt = Connection::getInstance()->query($sql);
            $stmt->execute();
            $this->rows = $stmt->fetchAll(PDO::FETCH_OBJ);

            $sql = $query;
            $stmt = Connection::getInstance()->query($sql);
            $stmt->execute();
            $count = $stmt->rowCount();

            $this->qtdPag = ceil($count / $limit);

            return $this;
        } catch (PaginationException $e) {
            throw new PaginationException($e->getMessage());
        }
    }

    /**
     * Gets rows from pagination
     * 
     * @return mixed
     */
    public function getRows(): mixed
    {
        return $this->rows;
    }

    /**
     * Gets arrows from pagination
     * 
     * @return string
     */
    public function getArrows(string $previous_name = "<<", string $next_name = ">>"): string
    {
        $html = "<a href='?page=1' class='pagination_first_item'>" . $previous_name . "</a> ";

        if ($this->qtdPag > 1 && $this->pg <= $this->qtdPag) {
            for ($i = 1; $i <= $this->qtdPag; $i++) {
                if ($i == $this->pg) {
                    $html .= " <span class='pagination_atual_item'>" . $i . "</span> ";
                } else {
                    $html .= " <a href='?page=$i' class='pagination_others_itens'>" . $i . "</a> ";
                }
            }
        }

        $html .= " <a href=\"?page=" . $this->qtdPag . "\" class='pagination_last_item'>" . $next_name . "</a>";

        return $html;
    }
}
