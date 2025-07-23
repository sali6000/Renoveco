<?php

namespace App\Core;

use App\Core\QueryBuilderInterface;

abstract class BaseDAO
{
    protected $queryBuilder;

    public function __construct(QueryBuilderInterface $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
}
