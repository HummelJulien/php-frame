<?php

namespace Hummel\PhpFrame\Models;

use Hummel\PhpFrame\Models\OrmAbstract;

class User extends OrmAbstract
{
    protected string $tableName;


    public function __construct()
    {
        $this->tableName = 'users';
        parent::__construct();
    }

    public function getToken()
    {
        return $this->token;
    }

}
