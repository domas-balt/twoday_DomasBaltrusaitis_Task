<?php

declare(strict_types=1);

namespace App\Entities;

class Query
{
    private string $base;
    private string $type;
    private array $where;
    private array $values;
    private string $leftJoin;

    public function __construct()
    {
        $this->base = '';
        $this->type = '';
        $this->leftJoin = '';
        $this->where = [];
        $this->values = [];
    }

    public function getBase(): string
    {
        return $this->base;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWhere(): array
    {
        return $this->where;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getLeftJoin(): string
    {
        return $this->leftJoin;
    }

    public function setBase(string $base): void
    {
        $this->base = $base;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setWhere(string $where): void
    {
        $this->where[] = $where;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function setLeftJoin(string $leftJoin): void
    {
        $this->leftJoin = $leftJoin;
    }
}
