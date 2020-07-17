<?php

namespace Katrina;
use Katrina\Sql\CRUD as CRUD;

class Katrina extends CRUD
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $columnPrimaryKey;

    /**
     * @var array
     */
    protected $columns;
    
    /**
     * @var const
     */
    public const KATRINA_VERSION = "1.0.0";

    /**
     * @param string $table
     * @param string $columnPrimaryKey
     * @param array $columns = null
     */
    public function __construct(string $table, string $columnPrimaryKey, array $columns = null)
    {
        $this->table = $table;
        $this->columnPrimaryKey = $columnPrimaryKey;
        $this->columns = $columns;
    }
}
