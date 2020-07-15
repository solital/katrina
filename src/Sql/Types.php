<?php

namespace Katrina\Sql;

abstract class Types
{
    /**
     * Inserts the SQL PRIMARY KEY command
     */
    public function primary(): Types
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " PRIMARY KEY ";

        return $this;
    }

    /**
     * Inserts the SQL NOT NULL command
     */
    public function notNull(): Types
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " NOT NULL,";

        return $this;
    }

    /**
     * @param string $table Table name
     */
    public function alter(string $table): Types
    {
        $this->sql = "ALTER TABLE `$table` ";

        return $this;
    }
    
    /**
     * Adds a new column to the table
     */
    public function add(): Types
    {
        $this->sql .= "ADD COLUMN ";

        return $this;
    }

    /**
     * Modify a table column
     */
    public function modify(): Types
    {
        $this->sql .= "MODIFY COLUMN ";

        return $this;
    }

    /**
     * @param string $old_column Old table column
     */
    public function change(string $old_column): Types
    {
        $this->sql .= "CHANGE COLUMN `$old_column` ";

        return $this;
    }

    /**
     * @param string $old_table Old table name
     * @param string $new_name New table name
     */
    public function rename(string $old_table, string $new_name): Types
    {
        $this->sql .= "RENAME TABLE `$old_table` TO `$new_name`,";

        return $this;
    }

    /**
     * @param string $column Column name
     */
    public function drop(string $column): Types
    {
        $this->sql .= "DROP COLUMN $column;";

        return $this;
    }

    /**
     * @param string $foreign_key Foreign key name
     */
    public function foreign(string $foreign_key): Types
    {   
        $this->sql .= "FOREIGN KEY (`$foreign_key`) ";

        return $this;
    }

    /**
     * @param string $constraint Constraint name
     */
    public function constraint(string $constraint): Types
    {
        $this->sql .= "CONSTRAINT `$constraint` ";

        return $this;
    }

    /**
     * @param string $constraint Constraint name
     */
    public function addConstraint(string $constraint): Types
    {
        $this->sql .= "ADD CONSTRAINT `$constraint` ";

        return $this;
    }
    
    /**
     * @param string $references
     * @param string $id
     */
    public function references(string $references, string $id): Types
    {
        $this->sql .= "REFERENCES `$references`(`$id`),";

        return $this;
    }

    /**
     * @param string $default
     */
    public function default(string $default): Types
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " DEFAULT '$default',";

        return $this;
    }

    /**
     * 
     */
    public function unique(): Types
    {
        $this->sql = rtrim($this->sql, ",");    
        $this->sql .= " UNIQUE,";

        return $this;
    }

    public function unsigned(): Types
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " UNSIGNED,";

        return $this;
    }

    /**
     * @param string $column
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

    public function first(): Types
    {
        $comma = substr($this->sql, -1);

        if ($comma == ",") {
            $this->sql = str_replace(",", "", $this->sql);
        }

        $this->sql .= " FIRST";

        return $this;
    }

    public function increment(): Types
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " AUTO_INCREMENT,";
        
        return $this;
    }

    /**
     * @param string $field
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
     */
    public function decimal(string $field, int $value1, int $value2): Types
    {
        $this->sql .= "`$field` DECIMAL($value1, $value2),";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     */
    public function char(string $field, int $size): Types
    {
        $this->sql .= "`$field` CHAR($size),";
        
        return $this;
    }
    
    /**
     * @param string $field
     * @param string $size
     */
    public function varchar(string $field, int $size): Types
    {
        $this->sql .= "`$field` VARCHAR($size),";   
        
        return $this;
    }

    /**
     * @param string $field
     */
    public function tinytext(string $field): Types
    {
        $this->sql .= "`$field` TINYTEXT,";
        
        return $this;
    }

    /**
     * @param string $field
     */
    public function mediumtext(string $field): Types
    {
        $this->sql .= "`$field` MEDIUMTEXT,";
        
        return $this;
    }

    /**
     * @param string $field
     */
    public function longtext(string $field): Types
    {
        $this->sql .= "`$field` LONGTEXT,";
        
        return $this;
    }
    
    /**
     * @param string $field
     */
    public function text(string $field): Types
    {
        $this->sql .= "`$field` TEXT,";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     */
    public function tinyint(string $field, int $size): Types
    {
        $this->sql .= "`$field` TINYINT($size),";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     */
    public function smallint(string $field, int $size): Types
    {
        $this->sql .= "`$field` SMALLINT($size),";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     */
    public function mediumint(string $field, int $size): Types
    {
        $this->sql .= "`$field` MEDIUMINT($size),";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size
     */
    public function bigint(string $field, int $size): Types
    {
        $this->sql .= "`$field` BIGINT($size),";
        
        return $this;
    }

    /**
     * @param string $field
     * @param int $size = 11
     */
    public function int(string $field, int $size = 11): Types
    {
        $this->sql .= "`$field` INT($size),";
        
        return $this;
    }

    /**
     * @param string $field
     */
    public function date(string $field): Types
    {
        $this->sql .= "`$field` DATE,";
        
        return $this;
    }

    /**
     * @param string $field
     */
    public function year(string $field): Types
    {
        $this->sql .= "`$field` YEAR,";
        
        return $this;
    }

    /**
     * @param string $field
     */
    public function time(string $field): Types
    {
        $this->sql .= "`$field` TIME,";
        
        return $this;
    }

    /**
     * @param string $field
     */
    public function datetime(string $field): Types
    {
        $this->sql .= "`$field` DATETIME,";
        
        return $this;
    }

    /**
     * @param string $field
     */
    public function timestamp(string $field): Types
    {
        $this->sql .= "`$field` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),";
        
        return $this;
    }
}
