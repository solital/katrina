<?php

namespace Katrina\Sql;
use Katrina\Connection\DB as DB;
use Katrina\Sql\Custom as Custom;
use Katrina\Exception\Exception;

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
     * @return CRUD
     */
    public function select(int $primaryKey = null, string $where = null, string $columns = "*"): CRUD
    {
        $this->sql = "SELECT $columns FROM $this->table ";

        if (isset($primaryKey)) {
            $this->sql = "SELECT $columns FROM $this->table WHERE $this->columnPrimaryKey = $primaryKey ";
        }

        if (isset($where)) {
            if ($primaryKey != null) {
                $this->sql .= "AND " . $where;
            } else {
                $this->sql .= "WHERE " . $where;
            }
        }

        return $this;
    }

    /**
     * SQL INNER JOIN command
     * @param string $tableForeign Column name with foreign key
     * @param array  $columnForeignKey Column name with the primary key and column where the foreign key is
     * @param int    $where Optional. WHERE clause
     * @param string $columns Name of columns to be returned. "*" by default
     */
    public function innerJoin(string $tableForeign, array $columnForeignKey, string $where = null, string $columns = "*"): CRUD
    {
        $this->sql = "SELECT $columns FROM $this->table a INNER JOIN $tableForeign b
        ON a.$columnForeignKey[0]=b.$columnForeignKey[1];";

        if ($where != null) {
            $this->sql = rtrim($this->sql, ";");
            $this->sql .= " WHERE $where";
        }

        return $this;
    }

     /**
     * SQL INSERT command
     * @param array $values The data that will be inserted in the table
     * @param bool $lastId  Lat insert id. Default is "false"
     */
    public function insert(array $values = [], bool $lastId = false)
    {
        $countColumns = count($this->columns);
        $resColumns = implode(",", $this->columns);
        $values = implode("','", $values);

        $this->sql = "INSERT INTO ".$this->table." (".($resColumns).") VALUES ( '".$values."' )";

        if (strpos($values, "NOW()") !== false) {
            $this->sql = str_replace("'NOW()'", "NOW()", $this->sql);
        } elseif (strpos($values, "CURRENT_TIMESTAMP()") !== false) {
            $this->sql = str_replace("'CURRENT_TIMESTAMP()'", "CURRENT_TIMESTAMP()", $this->sql);
        } elseif (stripos($values, "NULL") !== false) {
            $this->sql = str_replace("'NULL'", "NULL", $this->sql);
        }

        try {
            $stmt = DB::prepare($this->sql);
            for ($i=0; $i < $countColumns; $i++) { 
                $stmt->bindValue(':'.$this->columns[$i], $values[$i]);
            }
            $res = $stmt->execute();
            
            if ($lastId == true) {
                $lastId = DB::lastInsertId();
            
                return [
                    'res' => $res,
                    'lastId' => $lastId
                ];
            }
            
            return $res;
        } catch (\PDOException $e) {
            Exception::alertMessage($e, "'insert()' error");
        }
    }

    /**
     * SQL UPDATE command
     * @param array $columns The columns that will be updated
     * @param array $values The data that will be updated in the table
     * @param mixed $where WHERE clause
     * @return bool
     */
    public function update(array $columns, array $values, $where): bool
    {
        try {
            $this->sql = "UPDATE $this->table SET ";

            for ($i=0; $i < \count($columns); $i++) {
                $this->sql .= "$columns[$i] = '$values[$i]',";
            }

            $this->sql = rtrim($this->sql, ", ");

            if (strpos($this->sql, "+")) {
                $this->sql = str_replace("'", "", $this->sql);
            }

            if (is_int($where)) {
                $this->sql .= " WHERE $this->columnPrimaryKey = $where;";
            } elseif (is_string($where)) {
                $this->sql .= " WHERE $where;";
            }

            if (strpos($this->sql, "NOW()") !== false) {
                $this->sql = str_replace("'NOW()'", "NOW()", $this->sql);
            } elseif (strpos($this->sql, "CURRENT_TIMESTAMP()") !== false) {
                $this->sql = str_replace("'CURRENT_TIMESTAMP()'", "CURRENT_TIMESTAMP()", $this->sql);
            } elseif (stripos($this->sql, "NULL") !== false) {
                $this->sql = str_replace("'NULL'", "NULL", $this->sql);
            }
            
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
     * @param mixed  $value Primary key of the table
     * @param string $column Column name
     * @return CRUD
     */
    public function delete($value, string $column = NULL, bool $check_foreign_key = false): CRUD
    {
        $this->sql = "DELETE FROM $this->table WHERE $this->columnPrimaryKey = $value;";

        if ($column != NULL) {
            $this->sql = "DELETE FROM $this->table WHERE $column = $value;";
        }

        if (is_string($value)) {
            $this->sql = 'DELETE FROM '.$this->table.' WHERE '.$this->columnPrimaryKey.' = "'.$value.'";';

            if ($column != NULL) {
                $this->sql = 'DELETE FROM '.$this->table.' WHERE '.$column.' = "'.$value.'";';
            }
        }

        if ($check_foreign_key == true) {
            $this->sql = "SET FOREIGN_KEY_CHECKS=0;".$this->sql."SET FOREIGN_KEY_CHECKS=1;";
        }

        return $this;
    }

    /**
     * Call a database procedure
     * @param string $procedure Procedure name
     * @param array  $params Procedure params. Null by default
     * @return CRUD
     */
    public function call(string $procedure, array $params = null): CRUD
    {
        $values = null;
        if ($params) {
            $values = implode(", ", $params);
        }

        $this->sql = "CALL $procedure($values)";
        
        return $this;
    }
}
