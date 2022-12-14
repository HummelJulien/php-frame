<?php

namespace Hummel\PhpFrame\Models\Interfaces;

interface OrmModelInterface
{

    public function getOneBy(array $criteria): self;
    public function getAll(): array;
    public function __set($name, $value);
    public function __get($name);
    public function getTableName(): string;
    public function getDataRow(): ?array;
}
