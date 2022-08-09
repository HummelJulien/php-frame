<?php

namespace App\Models;

use App\Models\OrmAbstract;

class User extends OrmAbstract
{
    protected string $tableName;

    protected $token;

    public function __construct()
    {
        $this->tableName = 'users';
        parent::__construct();
        $this->data['token'] = session_id();
    }

    public function getToken()
    {
        return $this->token;
    }

}