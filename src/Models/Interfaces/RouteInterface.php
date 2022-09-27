<?php

namespace Hummel\PhpFrame\Models\Interfaces;

use Hummel\PhpFrame\Controllers\ControllerInterface;
use Hummel\PhpFrame\Models\Route;

interface RouteInterface
{
    public function getRouteUri(): string;
    public function getController(): ControllerInterface;
    public function getAction(): string;
    public function getRouteName(): string;
    public function getParams(): array;
    public function addParam($key, $param, $requirement = null) : self;
    public function getParamsRequirements(): array;
    public function getMethodes(): array;
}
