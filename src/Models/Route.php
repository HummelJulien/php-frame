<?php

namespace Hummel\PhpFrame\Models;

use Hummel\PhpFrame\Controllers\ControllerInterface;
use Hummel\PhpFrame\Models\Interfaces\RouteInterface;

class Route implements RouteInterface
{
    /**
     * Controller namespace and class name
     * @var $controller
     */
    protected string $controller;

    /**
     * Controller method name
     * @var string $action
     */
    protected string $action;

    /**
     * @var array $action
     */
    protected array $params;

    /**
     * @var array $params_requirements
     */
    protected array $paramsRequirements;

    /**
     * Route constructor
     * @var string $action
     */
    protected string $routeName;

    /**
     * Exemplary route: /demo/{id}
     * @var string $routeUri
     */
    protected string $routeUri;

    /**
     * @var array $methodes
     */
    protected array $methodes = [];

    /**
     * Route contructor all params is required
     * @param string $params
     * @param string $controller
     * @param string $action
     * @param string $routeName
     */
    public function __construct(string $routeUri, string $controller, string $action, string $routeName)
    {
        $this->routeUri = $routeUri;
        $this->controller = $controller;
        $this->action = $action;
        $this->routeName = $routeName;
    }

    /**
     * @return string
     */
    public function getController(): ControllerInterface
    {
        return new $this->controller;
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     * @return Route
     */
    public function setController(string $controller): Route
    {
        $this->controller = new $controller;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return Route
     */
    public function setAction(string $action): Route
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @throws \Exception
     */
    public function addParam($key, $param, $requirement = null) : Route
    {
        if (isset($requirement['type'])) {
            switch ($requirement['type']) {
                case 'int':
                    $param = (int)$param;
                    if (!is_numeric($param)) {
                        throw new \Exception('Param must be numeric');
                    }
                    break;
                case 'string':
                    $param = (string)$param;
                    if (!is_string($param)) {
                        throw new \Exception('Param must be string');
                    }
                    break;
                case 'float':
                    $param = (float)$param;
                    if (!is_float($param)) {
                        throw new \Exception('Param must be float');
                    }
                    break;
                default:
                    throw new \Exception('Param type not supported');
            }
        }
        $this->params[$key] = $param;
        return $this;
    }

    /**
     * @param array $params
     * @return Route
     */
    public function setParams(array $params): Route
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     * @return Route
     */
    public function setRouteName(string $routeName): Route
    {
        $this->routeName = $routeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getRouteUri(): string
    {
        return $this->routeUri;
    }

    /**
     * @param string $routeUri
     * @return Route
     */
    public function setRouteUri(string $routeUri): Route
    {
        $this->routeUri = $routeUri;
        return $this;
    }

    /**
     * @return array
     */
    public function getParamsRequirements(): array
    {
        return $this->paramsRequirements;
    }

    /**
     * @param string $key
     * @param array $paramsRequirements
     * @return Route
     */
    public function addParamRequirement(string $key, array $requirement) : Route
    {
        $this->paramsRequirements[$key] = $requirement;
        return $this;
    }

    /**
     * @param array $paramsRequirements
     * @return Route
     */
    public function setParamsRequirements(array $paramsRequirements): Route
    {
        $this->paramsRequirements = $paramsRequirements;
        return $this;
    }

    /**
     * @return array
     */
    public function getMethodes(): array
    {
        return $this->methodes;
    }

    /**
     * @param array $callable
     * @return Route
     */
    public function setMethodes(array $methodes): Route
    {
        $this->methodes = $methodes;
        return $this;
    }




}