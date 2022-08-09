<?php

namespace App\Controllers\Traits;

use App\Controllers\BaseController;
use App\Controllers\Interfaces\ControllerInterface;
use App\Models\Interfaces\RouteInterface;
use App\Models\Route;

trait HasRouteTrait
{
    protected RouteInterface $route;

    /**
     * @return Route
     */
    public function getRoute(): RouteInterface
    {
        return $this->route;
    }

    /**
     * @param Route $route
     * @return BaseController
     */
    public function setRoute(RouteInterface $route): void
    {
        $this->route = $route;
    }

}