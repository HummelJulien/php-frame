<?php

namespace Hummel\PhpFrame\Services;

use Hummel\PhpFrame\Models\Interfaces\OrmModelInterface;
use Hummel\PhpFrame\Services\PdoAbstract;

class OrmSingleton extends PdoAbstract
{
    private static ?OrmSingleton $instance = null;

    public static function getInstance(): PdoAbstract
    {
        if (self::$instance === null) {
            self::$instance = new OrmSingleton();
        }
        return self::$instance;
    }
    public function getAll(OrmModelInterface $model): array
    {
        $allElements = $this->getAllRequest($model->getTableName());
        if (empty($allElements) || !is_array($allElements)) {
            return [];
        }
        foreach ($allElements as $key => $value) {
            $elm = new $model;
            foreach ($value as $attribute => $v) {
                $elm->data[$attribute] = $v;
            }
            $allElements[$key] = $elm;
        }
        return $allElements;
    }

    public function getOneBy(OrmModelInterface $model, $criteria): array
    {
        $returnable = $this->getTableArrayByCriteria($model->getTableName(), $criteria);
        if (count($returnable) > 1) {
            return $returnable;
        }
        if (empty($returnable)) {
            return [];
        }
        return $returnable[0];
    }


}