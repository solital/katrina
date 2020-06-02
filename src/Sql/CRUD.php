<?php

namespace Katrina\Sql;
use Katrina\Connection\DB as DB;
use Katrina\Sql\Custom as Custom;
use Katrina\Exception\Exception;
use PDO;

abstract class CRUD extends Custom
{
    protected $sql;

    public function verifyLogin(string $column_email, string $column_pass, string $email, string $password)
    {
        try {
            $this->sql = "SELECT * FROM $this->table WHERE $column_email = '$email';";
            $res = $this->customQueryOnly($this->sql);

            if (password_verify($password, $res[$column_pass])) {
                return $res;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            Exception::alertMessage($e, "'verifyLogin()' error");
        }
    }

    public function select(int $primarykey = null, string $where = null, string $columns = "*")
    {
        $this->sql = "SELECT $columns FROM $this->table ";

        if (isset($primarykey)) {
            $this->sql = "SELECT $columns FROM $this->table WHERE $this->columnPrimaryKey = $primarykey ";
        }

        if (isset($where)) {
            $this->sql .= "WHERE ". $where;
        }

        return $this;
    }

    public function innerJoin(string $columnForeign, string $columnForeignKey, int $primarykey = null, string $columns = "*")
    {
        $this->sql = "SELECT $columns FROM $this->table a INNER JOIN $columnForeign b
        ON a.$this->columnPrimaryKey=b.$columnForeignKey;";

        if ($primarykey != null) {
            $this->sql = rtrim($this->sql, ";");
            $this->sql .= " WHERE a.$this->columnPrimaryKey = $primarykey;";
        }

        return $this;
    }

    public function insert(array $values = [])
    {
        $countColumns = count($this->columns);
        $resColumns = implode($this->columns, ',');

        try {
            $this->sql = "INSERT INTO $this->table ($resColumns) VALUES ('". implode("','", $values) ."');";
            
            $stmt = DB::prepare($this->sql);
            for ($i=0; $i < $countColumns; $i++) { 
                $stmt->bindParam(':'.$this->columns[$i], $values[$i]);
            }
            $res = $stmt->execute();
            
            return $res;
        } catch (\PDOException $e) {
            Exception::alertMessage($e, "'insert()' error");
        }
    }

    public function update(array $values = [], int $primarykey)
    {
        $countColumns = count($this->columnsUpdate);

        try {
            $sql = "UPDATE $this->table SET ";

            for ($i=0; $i < $countColumns; $i++) {
                $sql .= $this->columnsUpdate[$i]." = '$values[$i]'". ", ";
            }

            $format = rtrim($sql, ", ");
            $where = " WHERE $this->columnPrimaryKey = $primarykey;";
            
            $sqlComplete = $format.$where;
            
            $stmt = DB::prepare($sqlComplete);
            for ($i=0; $i < $countColumns; $i++) { 
                $stmt->bindParam(':'.$this->columnsUpdate[$i], $values[$i]);
            }
            $res = $stmt->execute();
            
            return $res;
        } catch (\PDOException $e) {
            Exception::alertMessage($e, "'update()' error");
        }
    }

    public function delete(int $primarykey)
    {
        $this->sql = "DELETE FROM $this->table WHERE $this->columnPrimaryKey = $primarykey;";

        return $this;
    }

    public function call(string $procedure, array $params = null)
    {
        $values = null;
        if ($params) {
            $values = implode(", ", $params);
        }

        $this->sql = "CALL $procedure($values)";
        
        return $this;
    }
}
