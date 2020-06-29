<?php

namespace Katrina;
use Katrina\Sql\CRUD as CRUD;

class Katrina extends CRUD
{
    protected $table;
    protected $columns;
    protected $columnPrimaryKey;
    public const KATRINA_VERSION = "0.2.0";

    public function __construct(string $table, string $columnPrimaryKey, array $columns = null)
    {
        $this->table = $table;
        $this->columnPrimaryKey = $columnPrimaryKey;
        $this->columns = $columns;
    }
}
