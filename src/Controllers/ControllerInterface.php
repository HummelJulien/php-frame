<?php

namespace Hummel\PhpFrame\Controllers;

use Hummel\PhpFrame\Models\Interfaces\Route;
use Hummel\PhpFrame\Models\Interfaces\RouteInterface;

interface ControllerInterface
{
    public static function show404(string $path, array $data): void;
}