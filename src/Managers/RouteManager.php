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
        $getParams = null;
        if (!empty($route->getMethodes())) {
            if (!in_array(strtolower($_SERVER['REQUEST_METHOD']), array_map('strtolower', $route->getMethodes()))) {
                return false;
            }
        }
        if (strpos($uri,'?')) {
            $uri = explode('?', $uri);
            $route = $this->parseAndAddGetParams($uri[1], $route);
            $uri = $uri[0];
        }
        if (isset($_POST) && !empty($_POST)) {
            $route = $this->parseAndAddPostParams($route);
        }
        if ((strpos($route->getRouteUri(),'}') && strpos($route->getRouteUri(),'{')) !== false) {

            return $this->hasMatchWithParameter($route, $uri);
        }

        return $route->getRouteUri() === $uri;
    }

    /**
     * Function for URI with captured params
     *
     * @param RouteInterface $route
     * @param string $uri
     * @return bool
     * @throws \Exception
     */
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

    /**
     * Function that check validation params
     *
     * @param string $param
     * @param array $requirement
     * @return bool
     * @throws \Exception
     */
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

    protected function parseAndAddGetParams(string $params, RouteInterface $route): RouteInterface
    {
        $params = explode('&', $params);
        if (!is_array($params)) {
            $assosParam = explode($param, '=');
            if (!is_array($params)) {
                $route->addParam($assosParam[0], urldecode($assosParam[1]));
            }
            return $route;
        }

        foreach ($params as $param) {
            $assosParam = explode('=', $param);
            $route->addParam($assosParam[0], urldecode($assosParam[1]));
        }
        return $route;
    }


    protected function parseAndAddPostParams(RouteInterface $route): RouteInterface
    {
        $posts = $_POST;
        if (is_array($posts)) {
            foreach ($posts as $key => $value) {
                $route->addParam($key, $value);
            }
        }
        return $route;
    }
}
