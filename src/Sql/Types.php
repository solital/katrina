<?php

namespace Katrina\Sql;
use Katrina\Connection\DB as DB;
use PDO;

abstract class Types
{
    public function primary()
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " PRIMARY KEY ";

        return $this;
    }

    public function notNull()
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " NOT NULL,";

        return $this;
    }

    public function alter(string $table)
    {
        $this->sql = "ALTER TABLE `$table` ";

        return $this;
    }
    
    public function add()
    {
        $this->sql .= "ADD COLUMN ";

        return $this;
    }

    public function modify()
    {
        $this->sql .= "MODIFY COLUMN ";

        return $this;
    }

    public function change(string $old_column)
    {
        $this->sql .= "CHANGE COLUMN `$old_column` ";

        return $this;
    }

    public function rename(string $old_table, string $new_name)
    {
        $this->sql .= "RENAME TABLE `$old_table` TO `$new_name`,";

        return $this;
    }

    public function drop(string $column)
    {
        $this->sql .= "DROP COLUMN $column;";

        return $this;
    }

    public function foreign(string $foreign_key)
    {   
        $this->sql .= "FOREIGN KEY (`$foreign_key`) ";

        return $this;
    }

    public function constraint(string $constraint)
    {
        $this->sql .= "CONSTRAINT `$constraint` ";

        return $this;
    }

    public function addConstraint(string $constraint)
    {
        $this->sql .= "ADD CONSTRAINT `$constraint` ";

        return $this;
    }
    
    public function references(string $references, string $id)
    {
        $this->sql .= "REFERENCES `$references`(`$id`),";

        return $this;
    }

    public function default(string $default)
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " DEFAULT '$default',";

        return $this;
    }

    public function unique()
    {
        $this->sql = rtrim($this->sql, ",");    
        $this->sql .= " UNIQUE,";

        return $this;
    }

    public function unsigned()
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " UNSIGNED,";

        return $this;
    }

    public function after(string $column)
    {
        $comma = substr($this->sql, -1);

        if ($comma == ",") {
            $this->sql = str_replace(",", "", $this->sql);
        }

        $this->sql .= " AFTER $column;";

        return $this;
    }

    public function first()
    {
        $comma = substr($this->sql, -1);

        if ($comma == ",") {
            $this->sql = str_replace(",", "", $this->sql);
        }

        $this->sql .= " FIRST";

        return $this;
    }

    public function increment()
    {
        $this->sql = rtrim($this->sql, ",");
        $this->sql .= " AUTO_INCREMENT,";
        
        return $this;
    }

    public function boolean(string $field)
    {
        $this->sql .= "`$field` BOOLEAN,";
        
        return $this;
    }

    public function decimal(string $field, int $value1, int $value2)
    {
        $this->sql .= "`$field` DECIMAL($value1, $value2),";
        
        return $this;
    }

    public function char(string $field, int $size)
    {
        $this->sql .= "`$field` CHAR($size),";
        
        return $this;
    }
    
    public function varchar(string $field, int $size)
    {
        $this->sql .= "`$field` VARCHAR($size),";   
        
        return $this;
    }

    public function tinytext(string $field)
    {
        $this->sql .= "`$field` TINYTEXT,";
        
        return $this;
    }

    public function mediumtext(string $field)
    {
        $this->sql .= "`$field` MEDIUMTEXT,";
        
        return $this;
    }

    public function longtext(string $field)
    {
        $this->sql .= "`$field` LONGTEXT,";
        
        return $this;
    }
    
    public function text(string $field)
    {
        $this->sql .= "`$field` TEXT,";
        
        return $this;
    }

    public function tinyint(string $field, int $size)
    {
        $this->sql .= "`$field` TINYINT($size),";
        
        return $this;
    }

    public function smallint(string $field, int $size)
    {
        $this->sql .= "`$field` SMALLINT($size),";
        
        return $this;
    }

    public function mediumint(string $field, int $size)
    {
        $this->sql .= "`$field` MEDIUMINT($size),";
        
        return $this;
    }

    public function bigint(string $field, int $size)
    {
        $this->sql .= "`$field` BIGINT($size),";
        
        return $this;
    }

    public function int(string $field, int $size = 11)
    {
        $this->sql .= "`$field` INT($size),";
        
        return $this;
    }

    public function date(string $field)
    {
        $this->sql .= "`$field` DATE,";
        
        return $this;
    }

    public function year(string $field)
    {
        $this->sql .= "`$field` YEAR,";
        
        return $this;
    }

    public function time(string $field)
    {
        $this->sql .= "`$field` TIME,";
        
        return $this;
    }

    public function datetime(string $field)
    {
        $this->sql .= "`$field` DATETIME,";
        
        return $this;
    }

    public function timestamp(string $field)
    {
        $this->sql .= "`$field` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),";
        
        return $this;
    }
}
