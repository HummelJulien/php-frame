<?php

namespace Hummel\PhpFrame\Models;

use Hummel\PhpFrame\Models\Interfaces\OrmModelInterface;
use Hummel\PhpFrame\Models\Interfaces\JoinableInterface;
use Hummel\PhpFrame\Services\OrmSingleton;

abstract class OrmAbstract implements OrmModelInterface, JoinableInterface
{

    protected OrmSingleton $ormServices;

    public array $data = [];


    public function __construct()
    {
        $this->ormServices = OrmSingleton::getInstance();
    }

    /**
     * Function hydrate model->data with data matched with @var criteria
     *
     * @param array $criteria
     * @return $this
     */
    public function getOneBy(array $criteria): OrmModelInterface
    {
        return $this->ormServices->getBy($this, $criteria);
    }

    /**
     * Function hydrate model->data with data matched with @var criteria
     *
     * @param array $criteria
     * @return $this
     */
    public function getBy(array $criteria): array
    {
        return $this->ormServices->getAll($this, $criteria, $limite = false);
    }

    /**
     * Persis object in database
     * @return self
     */
    public function save(): self
    {
        $this->ormServices->save($this);
        return $this;
    }

    /**
     * Delete object in database
     * @return self
     */
    public function delete(): self
    {
        $this->ormServices->delete($this);
        return $this;
    }

    public function withLeft(string $name_second_table, string $cond_table_primary, string $cond_table_secondary): self
    {
        $this->ormServices->with('LEFT JOIN', $name_second_table, $cond_table_primary, $cond_table_secondary);
        return $this;
    }

    public function withObject(JoinableInterface $model, string $cond_table_primary, string $cond_table_secondary): self
    {
        $this->ormServices->withObject($model, $cond_table_primary, $cond_table_secondary);
        return $this;
    }

    public function withInner(string $name_second_table, string $cond_table_primary, string $cond_table_secondary): self
    {
        $this->ormServices->with('INNER JOIN', $name_second_table, $cond_table_primary, $cond_table_secondary);
        return $this;
    }

    /**
     * Hydrate Array of Model with all entry for this
     *
     * @return array of objects
     */
    public function getAll(): array
    {
        return $this->ormServices->getAll($this);
    }

    public function getColumn(): array
    {
        return $this->column;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        if ($name === 'password') {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }
        $this->data[$name] = $value;
    }

    public function getDataRow(): ?array
    {
        return $this->data;
    }

    /**
     * Get the value of the property $name
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        if (function_exists(ucfirst($name))) {
            return call_user_func(ucfirst($name));
        }

        $trace = debug_backtrace();
        trigger_error(
            'Propriété non-définie via __get() : ' . $name .
            ' dans ' . $trace[0]['file'] .
            ' à la ligne ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }
}
