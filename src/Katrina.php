<?php

namespace Katrina;
use Katrina\Sql\CRUD as CRUD;

class Katrina extends CRUD
{
    protected $table;
    protected $columns;
    protected $columnsUpdate;
    protected $columnPrimaryKey;

    public function __construct(string $table, string $columnPrimaryKey, array $columns = null)
    {
        $this->table = $table;
        $this->columnPrimaryKey = $columnPrimaryKey;
        $this->columns = $columns;
    }

    public function colUpdate(array $columnsUpdate)
    {
        $this->columnsUpdate = $columnsUpdate;
    }
}
