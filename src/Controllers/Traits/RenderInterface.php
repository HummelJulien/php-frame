<?php

namespace App\Controllers\Traits;

interface RenderInterface
{
    public function render(string $path, array $data = []) : void;
}