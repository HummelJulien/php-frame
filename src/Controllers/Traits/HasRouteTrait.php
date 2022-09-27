<?php

namespace Hummel\PhpFrame\Controllers\Traits;

use Hummel\PhpFrame\Controllers\BaseController;
use Hummel\PhpFrame\Controllers\Interfaces\ControllerInterface;
use Hummel\PhpFrame\Models\Interfaces\RouteInterface;
use Hummel\PhpFrame\Models\Route;
/**
 * @author Hummel julien
 * @category Hummel\PhpFrame Controller
 */
trait HasRouteTrait
{
    /**
     * @var RouteInterface
     */
    protected RouteInterface $route;

    /**
     * Return @var RouteInterface
     *
     * @return RouteInterface
     */
    public function getRoute(): RouteInterface
    {
        return $this->route;
    }

    /**
     * Set @var RouteInterface
     *
     * @param Route $route
     * @return BaseController
     */
    public function setRoute(RouteInterface $route): void
    {
        $this->route = $route;
    }

}