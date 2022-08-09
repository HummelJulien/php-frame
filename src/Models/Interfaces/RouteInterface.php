<?php

namespace Hummel\PhpFrame\Models\Interfaces;

use Hummel\PhpFrame\Controllers\ControllerInterface;

interface RouteInterface
{
    public function getRouteUri(): string;
    public function getController(): ControllerInterface;
    public function getAction(): string;
    public function getRouteName(): string;
    public function getParams(): array;
    public function getParamsRequirements(): array;
    public function getMethodes(): array;
}