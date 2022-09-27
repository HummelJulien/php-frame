<?php

namespace Hummel\PhpFrame\Controllers\Traits;
/**
 * @author Hummel julien
 * @category Hummel\PhpFrame Controller
 */
interface RenderInterface
{
    public function render(string $path, array $data = []) : void;
}