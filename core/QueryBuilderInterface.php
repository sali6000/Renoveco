<?php

namespace App\Core;

interface QueryBuilderInterface
{
    public function select($table);
    public function columns(array $columns);
    public function where($condition);
    public function innerJoin($table, $condition);
    public function leftJoin($table, $condition);
    public function insert($table);
    public function values(array $values);
    public function update($table);
    public function set(array $values);
    public function delete($table);
    public function getQuery();
    public function execute($params = []);
}
