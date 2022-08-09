<?php

namespace Hummel\PhpFrame\Models;

use Hummel\PhpFrame\Models\Interfaces\OrmModelInterface;
use Hummel\PhpFrame\Services\OrmSingleton;

abstract class OrmAbstract implements OrmModelInterface
{

    protected OrmSingleton $ormServices;

    public array $data = [];


    public function __construct()
    {
        $this->ormServices = OrmSingleton::getInstance();
    }

    /**
     * @param array $criteria
     * @return $this
     */
    public function getOneBy(array $criteria): self
    {
        $this->data = $this->ormServices->getOneBy($this, $criteria);
        return $this;
    }

    /**
     * @return array of objects
     */
    public function getAll(): array
    {
        return $this->ormServices->getAll($this);;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
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