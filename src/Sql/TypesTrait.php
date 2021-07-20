<?php

namespace Katrina\Sql;

trait TypesTrait
{
    /**
     * @var string
     */
    private string $growing = "ASC";
    
    /**
     * @return self
     */
    public function primary(): self
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " PRIMARY KEY ";

        return $this;
    }

    /**
     * @return self
     */
    public function notNull(): self
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " NOT NULL,";

        return $this;
    }

    /**
     * @param string $table
     * 
     * @return self
     */
    public function alter(string $table): self
    {
        $this->sql = "ALTER TABLE `$table` ";

        return $this;
    }

    /**
     * @return self
     */
    public function add(): self
    {
        $this->sql .= "ADD COLUMN ";

        return $this;
    }

    /**
     * @return self
     */
    public function modify(): self
    {
        $this->sql .= "MODIFY COLUMN ";

        return $this;
    }

    /**
     * @param string $old_column
     * 
     * @return self
     */
    public function change(string $old_column): self
    {
        $this->sql .= "CHANGE COLUMN `$old_column` ";

        return $this;
    }

    /**
     * @param string $old_table
     * @param string $new_name
     * 
     * @return self
     */
    public function rename(string $old_table, string $new_name): self
    {
        $this->sql .= "RENAME TABLE `$old_table` TO `$new_name`,";

        return $this;
    }

    /**
     * @param string $column
     * 
     * @return self
     */
    public function drop(string $column): self
    {
        $this->sql .= "DROP COLUMN $column;";

        return $this;
    }

    /**
     * @param string $foreign_key
     * 
     * @return self
     */
    public function foreign(string $foreign_key): self
    {
        $this->sql .= "FOREIGN KEY (`$foreign_key`) ";

        return $this;
    }

    /**
     * @param string $constraint
     * 
     * @return self
     */
    public function constraint(string $constraint): self
    {
        $this->sql .= "CONSTRAINT `$constraint` ";

        return $this;
    }

    /**
     * @param string $constraint
     * 
     * @return self
     */
    public function addConstraint(string $constraint): self
    {
        $this->sql .= "ADD CONSTRAINT `$constraint` ";

        return $this;
    }

    /**
     * @param string $references
     * @param string $id
     * 
     * @return self
     */
    public function references(string $references, string $id): self
    {
        $this->sql .= "REFERENCES `$references`(`$id`),";

        return $this;
    }

    /**
     * @param string $default
     * 
     * @return self
     */
    public function default(string $default): self
    {
        $this->sql = rtrim($this->sql, ",");

        if ($default == 'CURRENT_TIMESTAMP' || $default == 'current_timestamp') {
            $default = strtoupper($default);
            $this->sql .= " DEFAULT $default,";
        } else {
            $this->sql .= " DEFAULT '$default',";
        }

        return $this;
    }

    /**
     * @return self
     */
    public function unique(): self
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " UNIQUE,";

        return $this;
    }

    /**
     * @return self
     */
    public function unsigned(): self
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " UNSIGNED,";

        return $this;
    }

    /**
     * @param string $column
     * 
     * @return self
     */
    public function after(string $column): self
    {
        $comma = substr($this->sql, -1);

        if ($comma == ",") {
            $this->sql = str_replace(",", "", $this->sql);
        }

        $this->sql .= " AFTER $column;";

        return $this;
    }

    /**
     * @return self
     */
    public function first(): self
    {
        $comma = substr($this->sql, -1);

        if ($comma == ",") {
            $this->sql = str_replace(",", "", $this->sql);
        }

        $this->sql .= " FIRST";

        return $this;
    }

    /**
     * @return self
     */
    public function increment(): self
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " AUTO_INCREMENT,";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function boolean(string $field): self
    {
        $this->sql .= "`$field` BOOLEAN,";

        return $this;
    }

    /**
     * @param string $field
     * @param int $value1
     * @param int $value2
     * 
     * @return self
     */
    public function decimal(string $field, int $value1, int $value2): self
    {
        $this->sql .= "`$field` DECIMAL($value1, $value2),";

        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function char(string $field, int $size): self
    {
        $this->sql .= "`$field` CHAR($size),";

        return $this;
    }

    /**
     * @param string $field
     * @param string $size
     * 
     * @return self
     */
    public function varchar(string $field, int $size): self
    {
        $this->sql .= "`$field` VARCHAR($size),";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function tinytext(string $field): self
    {
        $this->sql .= "`$field` TINYTEXT,";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function mediumtext(string $field): self
    {
        $this->sql .= "`$field` MEDIUMTEXT,";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function longtext(string $field): self
    {
        $this->sql .= "`$field` LONGTEXT,";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function text(string $field): self
    {
        $this->sql .= "`$field` TEXT,";

        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function tinyint(string $field, int $size): self
    {
        $this->sql .= "`$field` TINYINT($size),";

        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function smallint(string $field, int $size): self
    {
        $this->sql .= "`$field` SMALLINT($size),";

        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function mediumint(string $field, int $size): self
    {
        $this->sql .= "`$field` MEDIUMINT($size),";

        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function bigint(string $field, int $size): self
    {
        $this->sql .= "`$field` BIGINT($size),";

        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * 
     * @return self
     */
    public function int(string $field, int $size = 11): self
    {
        $this->sql .= "`$field` INT($size),";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function date(string $field): self
    {
        $this->sql .= "`$field` DATE,";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function year(string $field): self
    {
        $this->sql .= "`$field` YEAR,";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function time(string $field): self
    {
        $this->sql .= "`$field` TIME,";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function datetime(string $field): self
    {
        $this->sql .= "`$field` DATETIME,";

        return $this;
    }

    /**
     * @param string $field
     * 
     * @return self
     */
    public function timestamp(string $field): self
    {
        $this->sql .= "`$field` TIMESTAMP,";

        return $this;
    }

    /**
     * @param int $offset
     * @param int $row_count
     * 
     * @return self
     */
    public function limit(int $offset, int $row_count): self
    {
        if (!is_int($offset)) {
            throw new \IntlException("Offset not INT");
        } else if (!is_int($row_count)) {
            throw new \IntlException("Row count not INT");
        }

        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " LIMIT $offset, $row_count";

        return $this;
    }

    /**
     * @param string $like
     * 
     * @return self
     */
    public function like(string $like): self
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " LIKE '" . $like . "'";

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

        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " ORDER BY " . $column . " " . $this->growing;

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
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " BETWEEN " . $first_date . " AND " . $second_date;

        return $this;
    }
}
