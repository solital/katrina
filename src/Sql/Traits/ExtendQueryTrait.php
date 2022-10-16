<?php

namespace Katrina\Sql\Traits;

use Katrina\Functions\Functions;
use Katrina\Sql\Traits\DDLTrait;
use Katrina\Sql\KatrinaStatement;
use Katrina\Exceptions\KatrinaException;

trait ExtendQueryTrait
{
    use DDLTrait;

    /**
     * @var null|string
     */
    protected ?string $sql;

    /**
     * @var string
     */
    private string $growing = "ASC";

    /**
     * @var string
     */
    private static string $table_foreign;

    /**
     * @var null|string
     */
    private static ?string $id_foreign;

    /**
     * @var array
     */
    private static array $array_columns;

    /**
     * @var array
     */
    private static array $array_values;

    /**
     * @param string $column_email
     * @param string $column_pass
     * @param string $email
     * @param string $password
     * 
     * @return mixed
     */
    public function verifyLogin(string $column_email, string $column_pass, string $email, string $password)
    {
        try {
            self::$static_sql = "SELECT * FROM $this->table WHERE $column_email = '$email';";
            $res = KatrinaStatement::executeFetchAll(self::$static_sql);

            if (password_verify($password, $res[$column_pass])) {
                return $res;
            } else {
                return null;
            }
        } catch (KatrinaException $e) {
            throw new KatrinaException("Error in 'verifyLogin()': " . $e->getMessage());
        }
    }

    /**
     * @return string
     */
    public function rawQuery(): string
    {
        return self::$static_sql;
    }

    /**
     * @param string $table_foreign
     * @param string $foreign_key
     * 
     * @return self
     */
    public function innerJoin(string $table_foreign, string $foreign_key): self
    {
        self::$static_sql .= " INNER JOIN " . $table_foreign . " ON " . $table_foreign . "." . $foreign_key . "=" . self::$table_foreign . "." . self::$id_foreign . " ";

        return $this;
    }

    /**
     * @param int $rows
     * @param int|null $row_count
     * 
     * @return self
     */
    public function limit(int $rows, ?int $row_count = null): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");

        if ($row_count !== null) {
            self::$static_sql .= " LIMIT $rows, $row_count";
        } else {
            self::$static_sql .= " LIMIT $rows";
        }

        return $this;
    }

    /**
     * @param string $like
     * 
     * @return self
     */
    public function like(string $like): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " LIKE '" . $like . "'";

        return $this;
    }

    /**
     * @param string $column
     * @param bool $asc
     * 
     * @return self
     */
    public function order(string $column, bool $asc = true): self
    {
        if ($asc == false) {
            $this->growing = "DESC";
        }

        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " ORDER BY " . $column . " " . $this->growing;

        return $this;
    }

    /**
     * @param mixed $first_date
     * @param mixed $second_date
     * 
     * @return self
     */
    public function between($first_date, $second_date): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " BETWEEN " . $first_date . " AND " . $second_date;

        return $this;
    }

    /**
     * @param string $column
     * 
     * @return self
     */
    public function group(string $column): self
    {
        self::$static_sql = rtrim(self::$static_sql, ",");
        self::$static_sql .= " GROUP BY " . $column;

        return $this;
    }

    /**
     * @param string $column
     * @param null|mixed $condition
     * @param string $operator
     * 
     * @return self
     */
    public function where(string $column, $condition = null, string $operator = "="): self
    {
        self::$static_sql .= " WHERE $column";

        if ($condition != null) {
            if (\is_numeric($condition)) {
                self::$static_sql .= " $operator $condition";
            } else if (Functions::getQuery() != "") {
                self::$static_sql .= " $operator ($condition)";
            } else {
                self::$static_sql .= " $operator '$condition'";
            }
        }

        return $this;
    }

    /**
     * @param string $column
     * @param null $condition
     * @param string $operator
     * 
     * @return self
     */
    public function and(string $column, $condition = null, string $operator = "="): self
    {
        self::$static_sql .= " AND $column";

        if ($condition != null) {
            if (\is_numeric($condition)) {
                self::$static_sql .= " $operator $condition";
            } else if (Functions::getQuery() != "") {
                self::$static_sql .= " $operator ($condition)";
            } else {
                self::$static_sql .= " $operator '$condition'";
            }
        }

        return $this;
    }

    /**
     * @param string $column
     * @param null $condition
     * @param string $operator
     * 
     * @return self
     */
    public function or(string $column, $condition = null, string $operator = "="): self
    {
        self::$static_sql .= " OR $column";

        if ($condition != null) {
            if (\is_numeric($condition)) {
                self::$static_sql .= " $operator $condition";
            } else if (Functions::getQuery() != "") {
                self::$static_sql .= " $operator ($condition)";
            } else {
                self::$static_sql .= " $operator '$condition'";
            }
        }

        return $this;
    }

    /**
     * @param string $query
     * 
     * @return mixed
     */
    public static function customQuery(string $query, bool $all): mixed
    {
        return KatrinaStatement::executeQuery($query, $all);
    }

    /**
     * @param string $procedure
     * @param array|null $params
     * 
     * @return self
     */
    public function call(string $procedure, array $params = null): self
    {
        $values = null;

        if ($params) {
            $values = implode(", ", $params);
        }

        self::$static_sql = "CALL $procedure($values)";

        $this->build();

        return $this;
    }

    /**
     * @return mixed
     */
    public function get(): mixed
    {
        return KatrinaStatement::executeQuery(self::$static_sql, true);
    }

    /**
     * @return mixed
     */
    public function getUnique(): mixed
    {
        return KatrinaStatement::executeQuery(self::$static_sql, false);
    }
}
