<?php

namespace App\Controllers;

use App\Models\Interfaces\Route;
use App\Models\Interfaces\RouteInterface;

interface ControllerInterface
{
    public static function show404(string $path, array $data): void;
}