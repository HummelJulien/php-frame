<?php

namespace Hummel\PhpFrame\Controllers\Traits;

use Hummel\PhpFrame\Models\Interfaces\RouteInterface;
/**
 * @author Hummel julien
 * @category Hummel\PhpFrame Controller
 */
interface HasRouteInterface
{
    /**
     * @return RouteInterface
     */
    public function getRoute(): RouteInterface;

    /**
     * @param RouteInterface $route
     * @return void
     */
    public function setRoute(RouteInterface $route): void;
}