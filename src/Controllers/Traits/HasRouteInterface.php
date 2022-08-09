<?php

namespace Hummel\PhpFrame\Controllers\Traits;

use Hummel\PhpFrame\Models\Interfaces\RouteInterface;

interface HasRouteInterface
{
    public function getRoute(): RouteInterface;
    public function setRoute(RouteInterface $route): void;
}