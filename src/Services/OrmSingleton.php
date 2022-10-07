<?php

namespace Hummel\PhpFrame\Services;

use Hummel\PhpFrame\Models\Interfaces\JoinableInterface;
use Hummel\PhpFrame\Models\Interfaces\OrmModelInterface;
use Hummel\PhpFrame\Services\PdoAbstract;

class OrmSingleton extends PdoAbstract
{
    private static ?OrmSingleton $instance = null;

    protected array $select_column_alias = [];

    protected array $modelJoinable = [];

    /**
     * Provide Singleton
     *
     * @return \Hummel\PhpFrame\Services\PdoAbstract
     */
    public static function getInstance(): PdoAbstract
    {
        if (self::$instance === null) {
            self::$instance = new OrmSingleton();
        }
        return self::$instance;
    }

    /**
     * Get all methode of Hummel ORM
     *
     * @param OrmModelInterface $model
     * @return array
     */
    public function getAll(OrmModelInterface $model, $criteria = [], $limite = true): array
    {
        $this->addSelectColumn($model);
        if (!empty($criteria)) {
            $allElements = $this->getTableArrayByCriteria($model->getTableName(), $criteria, $this->select_column_alias, $limite);
        } else {
            $allElements = $this->getAllRequest($model->getTableName(), $this->select_column_alias);
        }


        if (empty($allElements) || !is_array($allElements)) {
            $this->modelJoinable = [];
            $this->select_column_alias = [];
            return [];
        }

        foreach ($allElements as $key => $value) {
            $elm = new $model;
            foreach ($this->modelJoinable as $inner_model) {
                $elm->data[$inner_model->getTableName()] = new $inner_model;
            }
            foreach ($value as $attribute => $v) {
                $attribute = explode('_999_' ,$attribute);
                if ($attribute[0] === $model->getTableName()) {
                    $elm->data[$attribute[1]] = $v;
                }
                if (array_key_exists($attribute[0], $elm->data)) {
                    $elm->data[$attribute[0]]->data[$attribute[1]] = $v;
                }
            }
            $allElements[$key] = $elm;
        }
        $this->modelJoinable = [];
        $this->select_column_alias = [];
        return $allElements;
    }

    /**
     * Get One By @var criteria
     *
     * @param OrmModelInterface $model
     * @param $criteria
     * @return array
     */
    public function getBy(OrmModelInterface $model, $criteria, $limite = true): OrmModelInterface
    {
        $this->addSelectColumn($model);
        $returnable = $this->getTableArrayByCriteria($model->getTableName(), $criteria, $this->select_column_alias, $limite);
        $elm = new $model;
        foreach ($returnable as $key => $value) {
            foreach ($this->modelJoinable as $inner_model) {
                $elm->{$inner_model->getTableName()} = new $inner_model;
            }
            foreach ($value as $attribute => $v) {
                $attribute = explode('_999_' ,$attribute);
                if ($attribute[0] === $model->getTableName()) {
                    $elm->data[$attribute[1]] = $v;
                }
                if (array_key_exists($attribute[0], $elm->data)) {
                    $elm->data[$attribute[0]]->data[$attribute[1]] = $v;
                }
            }

        }

        $this->modelJoinable = [];
        $this->select_column_alias = [];
        return $elm;
    }

    /**
     * Save model
     *
     * @param OrmModelInterface $model
     * @param $criteria
     * @return array
     */
    public function save(OrmModelInterface $model): void
    {
        if (isset($model->id)) {
            $this->updateEntry($model->getTableName(), $model->data);
        } else {
            $this->createEntry($model->getTableName(), $model->data);
        }
    }

    /**
     * delete model
     *
     * @param OrmModelInterface $model
     * @param $criteria
     * @return array
     */
    public function delete(OrmModelInterface $model): void
    {
        $this->deleteEntry($model->getTableName(), $model->data);
    }

    public function withObject(JoinableInterface $model, string $cond_table_primary, string $cond_table_secondary): void
    {
        if (!empty($model->getColumn())) {
            $this->addSelectColumn($model);
        }
        array_push($this->modelJoinable, $model);
        $this->with('LEFT JOIN', $model->getTableName(), $cond_table_primary, $cond_table_secondary);
    }

    protected function  addSelectColumn(JoinableInterface $model) {
        foreach ($model->getColumn() as $column) {
            $this->select_column_alias = array_merge($this->select_column_alias, [
                $model->getTableName().'.'.$column      =>      $model->getTableName().'_999_'.$column]);
        }
    }

    /**
     * Join prepar for request
     *
     * @param string $type_of_join
     * @param string $name_second_table
     * @param string $cond_table_primary
     * @param string $cond_table_secondary
     * @return void
     */
    public function with(string $type_of_join, string $name_second_table, string $cond_table_primary, string $cond_table_secondary): void
    {
        $this->addJoinString($type_of_join, $name_second_table, $cond_table_primary, $cond_table_secondary);
    }


}
