<?php

namespace Katrina\Sql;
use Katrina\Connection\DB as DB;
use Katrina\Sql\Custom as Custom;
use Katrina\Exception\Exception;
use PDO;

abstract class CRUD extends Custom
{
    /**
     * @var mixed
     */
    protected $sql;

    /**
     * Checks email and password in the database
     * @param string $column_email database email column
     * @param string $column_pass database password column
     * @param string $email email to be verified
     * @param string $password password to be verified
     */
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

    /**
     * SQL SELECT command
     * @param int    $primaryKey Optional. Primary key of the table
     * @param string $where Optional. SQL WHERE command
     * @param string $columns Name of columns to be returned. "*" by default
     */
    public function select(int $primaryKey = null, string $where = null, string $columns = "*")
    {
        $this->sql = "SELECT $columns FROM $this->table ";

        if (isset($primaryKey)) {
            $this->sql = "SELECT $columns FROM $this->table WHERE $this->columnPrimaryKey = $primaryKey ";
        }

        if (isset($where)) {
            $this->sql .= "WHERE ". $where;
        }

        return $this;
    }

    /**
     * SQL INNER JOIN command
     * @param string $columnForeign Column name with foreign key
     * @param string $columnForeignKey Column name with the primary key where the foreign key is
     * @param int    $primaryKey Optional. Primary key of the table
     * @param string $columns Name of columns to be returned. "*" by default
     */
    public function innerJoin(string $columnForeign, string $columnForeignKey, int $primaryKey = null, string $columns = "*")
    {
        $this->sql = "SELECT $columns FROM $this->table a INNER JOIN $columnForeign b
        ON a.$this->columnPrimaryKey=b.$columnForeignKey;";

        if ($primaryKey != null) {
            $this->sql = rtrim($this->sql, ";");
            $this->sql .= " WHERE a.$this->columnPrimaryKey = $primaryKey;";
        }

        return $this;
    }

    /**
     * SQL INSERT command
     * @param array $values The data that will be inserted in the table
     */
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

    /**
     * SQL UPDATE command
     * @param array $columns The columns that will be updated
     * @param array $values The data that will be updated in the table
     * @param int   $primaryKey Primary key of the table
     */
    public function update(array $columns, array $values, int $primaryKey)
    {
        try {
            $this->sql = "UPDATE $this->table SET ";

            for ($i=0; $i < \count($columns); $i++) {
                $this->sql .= "$columns[$i] = '$values[$i]',";
            }

            $this->sql = rtrim($this->sql, ", ");
            $this->sql .= " WHERE $this->columnPrimaryKey = $primaryKey;";
            
            $stmt = DB::prepare($this->sql);
            for ($i=0; $i < \count($columns); $i++) { 
                $stmt->bindParam(':'.$columns[$i], $values[$i]);
            }
            $res = $stmt->execute();
            
            return $res;
        } catch (\PDOException $e) {
            Exception::alertMessage($e, "'update()' error");
        }
    }

    /**
     * SQL DELETE command
     * @param int $primaryKey Primary key of the table
     */
    public function delete(int $primarykey)
    {
        $this->sql = "DELETE FROM $this->table WHERE $this->columnPrimaryKey = $primarykey;";

        return $this;
    }

    /**
     * Call a database procedure
     * @param string $procedure Procedure name
     * @param array  $params Procedure params. Null by default
     */
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
