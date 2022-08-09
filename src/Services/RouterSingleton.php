<?php

namespace Hummel\PhpFrame\Services;

use Hummel\PhpFrame\Models\Interfaces\RouteInterface;
use Hummel\PhpFrame\Models\Route;
use Hummel\PhpFrame\Controllers\BaseController;
use Hummel\PhpFrame\Managers\RouteManager;

class RouterSingleton
{

    private static ?RouterSingleton $instance = null;

    protected RouteManager $routeManager;

    /**
     * @var Route[] $routes
     */
    protected $routes = [];

    public static function getInstance(): RouterSingleton
    {
        if (self::$instance === null) {
            self::$instance = new RouterSingleton();
        }
        return self::$instance;
    }

    /**
     * Entry points for routing in the application
     * @param string $uri
     * @return void
     */
    public function routerAction(string $uri) : void
    {
        try {
            $route = $this->getRouteByUri($uri);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        isset($route) ? $this->callControllerAction($route): BaseController::show404();
    }

    /**
     * Construct and insert all routes in the router
     * @throws \Exception
     */
    private function __construct()
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'].'/../config/routes.yaml')) {
            $routes = yaml_parse_file($_SERVER['DOCUMENT_ROOT'].'/../config/routes.yaml');
        } else {
            $routes = yaml_parse_file($_SERVER['DOCUMENT_ROOT'].'/../vendor/hummel/php-frame/src/resources/config/routes.yaml');
        }


        if ($routes === false || !is_array($routes)) {
            throw new \Exception('Routes file is not valid');
        }

        foreach ($routes as $key => $route) {
            $this->routes[$key] = new Route($route['routeUri'], $route['controller'], $route['action'], $key);

            if (isset($route['parameters']) && is_array($route['parameters'])) {
                foreach ($route['parameters'] as $keyParams => $parameter) {
                    $this->routes[$key]->addParamRequirement((string)$keyParams, $parameter);
                }
            }

            if (isset($route['methode'])) {
                $callable = explode('/', $route['methode']);
                $this->routes[$key]->setMethodes($callable);
            }
        }
        $this->routeManager = new RouteManager();
    }

    /**
     * Provide route instance to Controller and call action that is defined in the route
     * @param Route $route
     * @return void
     */
    protected function callControllerAction(RouteInterface $route)
    {
        $controller = $route->getController();
        $controller->setRoute($route);
        $controller->{$route->getAction()}();
    }

    /**
     * @param string $uri
     * @return Route
     * @throws \Exception
     */
    protected function getRouteByUri(string $uri) : RouteInterface
    {
        foreach ($this->routes as $route) {
            if ($this->routeManager->hasMatch($route, $uri)) {
                return $route;
            }
        }
        throw new \Exception('Route not found');
    }

    /**
     * @return RouteInterface[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param RouteInterface[] $routes
     * @return self
     */
    public function setRoutes(array $routes): self
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * @param Route $route
     * @return void
     */
    public function addRoute(RouteInterface $route)
    {
        $this->routes[$route->getRouteName()] = $route;
    }

}