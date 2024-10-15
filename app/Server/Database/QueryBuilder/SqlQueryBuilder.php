<?php

declare(strict_types=1);

namespace App\Server\Database\QueryBuilder;

interface SqlQueryBuilder
{
    public function select(string $table, array $fields): SqlQueryBuilder;
    public function insert(string $table, array $fields): SqlQueryBuilder;
    public function where(string $field, string $value, string $operator = '='): SqlQueryBuilder;
    public function delete(string $table): SqlQueryBuilder;
    public function update(string $table, array $fields): SqlQueryBuilder;
    public function leftJoin(string $table, string $join): SqlQueryBuilder;
    public function getSql(): string;
}
