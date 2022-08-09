<?php

namespace Hummel\PhpFrame\Managers;

use Hummel\PhpFrame\Models\Interfaces\RouteInterface;

class RouteManager
{
    /**
     * Entry point for route matching by URI
     *
     * @param string $uri
     * @return bool
     */
    public function hasMatch(RouteInterface $route, string $uri) : bool
    {
        if (!empty($route->getMethodes())) {
            if (!in_array(strtolower($_SERVER['REQUEST_METHOD']), array_map('strtolower', $route->getMethodes()))) {
                return false;
            }
        }
        if ((strpos($route->getRouteUri(),'}') && strpos($route->getRouteUri(),'{')) !== false) {

            return $this->hasMatchWithParameter($route, $uri);
        }
        return $route->getRouteUri() === $uri;
    }

    protected function hasMatchWithParameter(RouteInterface $route, string $uri) : bool {
        $params = explode('/', $route->getRouteUri());
        $uri = explode('/', $uri);

        if (count($params) != count($uri)) {
            return false;
        }
        foreach ($params as $key => $value) {
            $value_debug = strpos($value,'{') && strpos($value,'}');
            if (preg_match('/{([a-z-A-Z-0-9-]+)}/', $value, $matches)) {
                if (array_key_exists($matches[1], $route->getParamsRequirements())) {
                    $this->validateParam($uri[$key], $route->getParamsRequirements()[$matches[1]]);
                }
                $route->addParam($matches[1], $uri[$key], $route->getParamsRequirements()[$matches[1]]);
                $params[$key] = $uri[$key];
            }
        }
        return $params == $uri;
    }

    protected function validateParam(string $param, array $requirement)
    {
        if (isset($requirement['type']) && $requirement['type'] == 'int') {
            if (!is_numeric($param)) {
                throw new \Exception('Param must be numeric');
            }
        }
        if (isset($requirement['regex']) && !preg_match($requirement['regex'], $param)) {
            throw new \Exception('Param not match regex');
        }
        return true;
    }
}