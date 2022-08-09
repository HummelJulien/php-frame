<?php

namespace Hummel\PhpFrame\Controllers;

use Hummel\PhpFrame\Models\Route;
use Hummel\PhpFrame\Services\RouterSingleton;
use Hummel\PhpFrame\Controllers\BaseController;

class RouterDebugController extends BaseController
{
    public function index()
    {
        $router = RouterSingleton::getInstance();
        $allRoutes = $router->getRoutes();

        $this->render('/src/views/routerDebug.php', [
            'allRoutes' => $allRoutes,
        ]);
    }
}