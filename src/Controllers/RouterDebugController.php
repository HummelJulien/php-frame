<?php

namespace Hummel\PhpFrame\Controllers;

use Hummel\PhpFrame\Models\Route;
use Hummel\PhpFrame\Services\RouterSingleton;
use Hummel\PhpFrame\Controllers\BaseController;

class RouterDebugController extends BaseController
{
    /**
     * Function index of **RouterController**
     *
     * This get all route and call templating system with associed template
     * **developer purpose**
     *
     * @return void
     */
    public function index()
    {
        $router = RouterSingleton::getInstance();
        $allRoutes = $router->getRoutes();

        $this->render('/src/views/routerDebug.php', [
            'allRoutes' => $allRoutes,
        ]);
    }
}