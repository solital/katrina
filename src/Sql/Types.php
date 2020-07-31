<?php

namespace Katrina\Sql;

abstract class Types
{
    /**
     * Inserts the SQL PRIMARY KEY command
     * @return Types
     */
    public function primary(): Types
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " PRIMARY KEY ";

        return $this;
    }

    /**
     * Inserts the SQL NOT NULL command
     * @return Types
     */
    public function notNull(): Types
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " NOT NULL,";

        return $this;
    }

    /**
     * @param string $table Table name
     * @return Types
     */
    public function alter(string $table): Types
    {
        $this->sql = "ALTER TABLE `$table` ";

        return $this;
    }
    
    /**
     * Adds a new column to the table
     * @return Types
     */
    public function add(): Types
    {
        $this->sql .= "ADD COLUMN ";

        return $this;
    }

    /**
     * Modify a table column
     * @return Types
     */
    public function modify(): Types
    {
        $this->sql .= "MODIFY COLUMN ";

        return $this;
    }

    /**
     * @param string $old_column Old table column
     * @return Types
     */
    public function change(string $old_column): Types
    {
        $this->sql .= "CHANGE COLUMN `$old_column` ";

        return $this;
    }

    /**
     * @param string $old_table Old table name
     * @param string $new_name New table name
     * @return Types
     */
    public function rename(string $old_table, string $new_name): Types
    {
        $this->sql .= "RENAME TABLE `$old_table` TO `$new_name`,";

        return $this;
    }

    /**
     * @param string $column Column name
     * @return Types
     */
    public function drop(string $column): Types
    {
        $this->sql .= "DROP COLUMN $column;";

        return $this;
    }

    /**
     * @param string $foreign_key Foreign key name
     * @return Types
     */
    public function foreign(string $foreign_key): Types
    {   
        $this->sql .= "FOREIGN KEY (`$foreign_key`) ";

        return $this;
    }

    /**
     * @param string $constraint Constraint name
     * @return Types
     */
    public function constraint(string $constraint): Types
    {
        $this->sql .= "CONSTRAINT `$constraint` ";

        return $this;
    }

    /**
     * @param string $constraint Constraint name
     * @return Types
     */
    public function addConstraint(string $constraint): Types
    {
        $this->sql .= "ADD CONSTRAINT `$constraint` ";

        return $this;
    }
    
    /**
     * @param string $references
     * @param string $id
     * @return Types
     */
    public function references(string $references, string $id): Types
    {
        $this->sql .= "REFERENCES `$references`(`$id`),";

        return $this;
    }

    /**
     * @param string $default
     * @return Types
     */
    public function default(string $default): Types
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
     * @return Types
     */
    public function unique(): Types
    {
        $this->sql = rtrim($this->sql, ",");    
        $this->sql .= " UNIQUE,";

        return $this;
    }

    /**
     * @return Types
     */
    public function unsigned(): Types
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " UNSIGNED,";

        return $this;
    }

    /**
     * @param string $column
     * @return Types
     */
    public function after(string $column): Types
    {
        $comma = substr($this->sql, -1);

        if ($comma == ",") {
            $this->sql = str_replace(",", "", $this->sql);
        }

        $this->sql .= " AFTER $column;";

        return $this;
    }

    /**
     * @return Types
     */
    public function first(): Types
    {
        $comma = substr($this->sql, -1);

        if ($comma == ",") {
            $this->sql = str_replace(",", "", $this->sql);
        }

        $this->sql .= " FIRST";

        return $this;
    }

    /**
     * @return Types
     */
    public function increment(): Types
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " AUTO_INCREMENT,";
        
        return $this;
    }

    /**
     * @param string $field
     * @return Types
     */
    public function boolean(string $field): Types
    {
        $this->sql .= "`$field` BOOLEAN,";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $value1
     * @param int $value2
     * @return Types
     */
    public function decimal(string $field, int $value1, int $value2): Types
    {
        $this->sql .= "`$field` DECIMAL($value1, $value2),";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * @return Types
     */
    public function char(string $field, int $size): Types
    {
        $this->sql .= "`$field` CHAR($size),";
        
        return $this;
    }
    
    /**
     * @param string $field
     * @param string $size
     * @return Types
     */
    public function varchar(string $field, int $size): Types
    {
        $this->sql .= "`$field` VARCHAR($size),";   
        
        return $this;
    }

    /**
     * @param string $field
     * @return Types
     */
    public function tinytext(string $field): Types
    {
        $this->sql .= "`$field` TINYTEXT,";
        
        return $this;
    }

    /**
     * @param string $field
     * @return Types
     */
    public function mediumtext(string $field): Types
    {
        $this->sql .= "`$field` MEDIUMTEXT,";
        
        return $this;
    }

    /**
     * @param string $field
     * @return Types
     */
    public function longtext(string $field): Types
    {
        $this->sql .= "`$field` LONGTEXT,";
        
        return $this;
    }
    
    /**
     * @param string $field
     * @return Types
     */
    public function text(string $field): Types
    {
        $this->sql .= "`$field` TEXT,";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * @return Types
     */
    public function tinyint(string $field, int $size): Types
    {
        $this->sql .= "`$field` TINYINT($size),";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * @return Types
     */
    public function smallint(string $field, int $size): Types
    {
        $this->sql .= "`$field` SMALLINT($size),";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * @return Types
     */
    public function mediumint(string $field, int $size): Types
    {
        $this->sql .= "`$field` MEDIUMINT($size),";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     * @return Types
     */
    public function bigint(string $field, int $size): Types
    {
        $this->sql .= "`$field` BIGINT($size),";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size = 11
     * @return Types
     */
    public function int(string $field, int $size = 11): Types
    {
        $this->sql .= "`$field` INT($size),";
        
        return $this;
    }

    /**
     * @param string $field
     * @return Types
     */
    public function date(string $field): Types
    {
        $this->sql .= "`$field` DATE,";
        
        return $this;
    }

    /**
     * @param string $field
     * @return Types
     */
    public function year(string $field): Types
    {
        $this->sql .= "`$field` YEAR,";
        
        return $this;
    }

    /**
     * @param string $field
     * @return Types
     */
    public function time(string $field): Types
    {
        $this->sql .= "`$field` TIME,";
        
        return $this;
    }

    /**
     * @param string $field
     * @return Types
     */
    public function datetime(string $field): Types
    {
        $this->sql .= "`$field` DATETIME,";
        
        return $this;
    }

    /**
     * @param string $field
     * @return Types
     */
    public function timestamp(string $field): Types
    {
        $this->sql .= "`$field` TIMESTAMP,";
        
        return $this;
    }
}
