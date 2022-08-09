<?php

namespace Hummel\PhpFrame\Controllers\Traits;

use Hummel\PhpFrame\Controllers\BaseController;
use Hummel\PhpFrame\Controllers\Interfaces\ControllerInterface;
use Hummel\PhpFrame\Models\Interfaces\RouteInterface;
use Hummel\PhpFrame\Models\Route;

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