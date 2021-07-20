<?php

namespace Katrina;

use Katrina\Sql\CRUD as CRUD;

class Katrina extends CRUD
{
    /**
     * @var string
     */
    protected string $table;

    /**
     * @var string
     */
    protected string $columnPrimaryKey;

    /**
     * @var array
     */
    protected ?array $columns;

    /**
     * @var const
     */
    public const KATRINA_VERSION = "1.3.0";

    /**
     * @param string $table
     * @param string $columnPrimaryKey
     * @param array|null $columns
     */
    public function __construct(string $table, string $columnPrimaryKey, array $columns = null)
    {
        $this->table = $table;
        $this->columnPrimaryKey = $columnPrimaryKey;
        $this->columns = $columns;
    }
}
