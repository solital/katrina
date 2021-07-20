<?php

namespace Katrina\Sql;

use Katrina\Connection\DB as DB;
use Katrina\Sql\Custom as Custom;

abstract class CRUD extends Custom
{
    /**
     * @var string
     */
    protected string $sql;

    /**
     * @param string $column_email
     * @param string $column_pass
     * @param string $email
     * @param string $password
     * 
     * @return array
     */
    public function verifyLogin(string $column_email, string $column_pass, string $email, string $password): array
    {
        try {
            $this->sql = "SELECT * FROM $this->table WHERE $column_email = '$email';";
            $res = $this->customQueryOnly($this->sql);

            if (password_verify($password, $res[$column_pass])) {
                return $res;
            } else {
                return null;
            }
        } catch (\PDOException $e) {
            throw new \PDOException("Error in 'verifyLogin()': " . $e->getMessage());
        }
    }

    /**
     * @param int|null $primaryKey
     * @param string|null $where
     * @param string $columns
     * 
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
     * @param string $tableForeign
     * @param array $columnForeignKey
     * @param string|null $where
     * @param string $columns
     * 
     * @return CRUD
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
     * @param array $values
     * @param bool $lastId
     * 
     * @return mixed
     */
    public function insert(array $values = [], bool $lastId = false)
    {
        $countColumns = count($this->columns);
        $resColumns = implode(",", $this->columns);
        $values = implode("','", $values);

        $this->sql = "INSERT INTO " . $this->table . " (" . ($resColumns) . ") VALUES ( '" . $values . "' )";

        if (strpos($values, "NOW()") !== false) {
            $this->sql = str_replace("'NOW()'", "NOW()", $this->sql);
        } elseif (strpos($values, "CURRENT_TIMESTAMP()") !== false) {
            $this->sql = str_replace("'CURRENT_TIMESTAMP()'", "CURRENT_TIMESTAMP()", $this->sql);
        } elseif (stripos($values, "NULL") !== false) {
            $this->sql = str_replace("'NULL'", "NULL", $this->sql);
        }

        try {
            $stmt = DB::prepare($this->sql);
            for ($i = 0; $i < $countColumns; $i++) {
                $stmt->bindValue(':' . $this->columns[$i], $values[$i]);
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
            throw new \PDOException("Error in 'insert()': " . $e->getMessage());
        }
    }

    /**
     * @param array $columns
     * @param array $values
     * @param mixed $where
     * @param bool $intNumbers
     * 
     * @return bool
     */
    public function update(array $columns, array $values, $where, bool $intNumbers = false): bool
    {
        try {
            $this->sql = "UPDATE $this->table SET ";

            for ($i = 0; $i < \count($columns); $i++) {
                if ($intNumbers == true) {
                    $this->sql .= "$columns[$i] = $values[$i],";
                } else {
                    $this->sql .= "$columns[$i] = '$values[$i]',";
                }
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
            for ($i = 0; $i < \count($columns); $i++) {
                $stmt->bindParam(':' . $columns[$i], $values[$i]);
            }
            $res = $stmt->execute();

            return $res;
        } catch (\PDOException $e) {
            throw new \PDOException("Error in 'update()': " . $e->getMessage());
        }
    }

    /**
     * @param mixed $value
     * @param string $column
     * @param bool $check_foreign_key
     * 
     * @return CRUD
     */
    public function delete($value, string $column = NULL, bool $check_foreign_key = false): CRUD
    {
        $this->sql = "DELETE FROM $this->table WHERE $this->columnPrimaryKey = $value;";

        if ($column != NULL) {
            $this->sql = "DELETE FROM $this->table WHERE $column = $value;";
        }

        if (is_string($value)) {
            $this->sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->columnPrimaryKey . ' = "' . $value . '";';

            if ($column != NULL) {
                $this->sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $column . ' = "' . $value . '";';
            }
        }

        if ($check_foreign_key == true) {
            $this->sql = "SET FOREIGN_KEY_CHECKS=0;" . $this->sql . "SET FOREIGN_KEY_CHECKS=1;";
        }

        return $this;
    }

    /**
     * @param string $procedure
     * @param array|null $params
     * 
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
