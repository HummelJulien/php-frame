<?php

namespace App\Controllers\Traits;

use App\Models\Interfaces\RouteInterface;

interface HasRouteInterface
{
    public function getRoute(): RouteInterface;
    public function setRoute(RouteInterface $route): void;
}