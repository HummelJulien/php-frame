<?php

namespace App\Controllers;

use App\Models\Route;
use App\Services\RouterSingleton;
use App\Controllers\BaseController;

class RouterDebugController extends BaseController
{
    public function index()
    {
        $router = RouterSingleton::getInstance();
        $allRoutes = $router->getRoutes();

        $this->render('src/views/routerDebug.php', [
            'allRoutes' => $allRoutes,
        ]);
    }
}