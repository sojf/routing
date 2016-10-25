<?php
namespace Sojf\Routing;


use Sojf\Routing\Interfaces\Compiled as CompiledInterface;

class Compiled implements CompiledInterface
{
    protected $routePath;
    protected $routePathRegexp;
    protected $arguments = array();
    protected $requestMethods;
    protected $controllerMethod;
    protected $controller;
    protected $dynamicMethodIndex;
    protected $viewNameSpace;
    protected $ModelNameSpace;
    protected $regMatch = array();

    public function getViewNameSpace()
    {
        return $this->viewNameSpace;
    }

    public function setViewNameSpace($viewNameSpace)
    {
        $this->viewNameSpace = $viewNameSpace;
        return $this;
    }

    public function getModelNameSpace()
    {
        return $this->ModelNameSpace;
    }
    
    public function setModelNameSpace($ModelNameSpace)
    {
        $this->ModelNameSpace = $ModelNameSpace;
        return $this;
    }

    public function getDynamicMethodIndex()
    {
        return $this->dynamicMethodIndex;
    }

    public function setDynamicMethodIndex($dynamicMethodIndex)
    {
        $this->dynamicMethodIndex = $dynamicMethodIndex;
        return $this;
    }

    public function getRoutePath()
    {
        return $this->routePath;
    }

    public function setRoutePath($routePath)
    {
        $this->routePath = $routePath;
        return $this;
    }

    public function getRoutePathRegexp()
    {
        return $this->routePathRegexp;
    }

    public function setRoutePathRegexp($routePathRegexp)
    {
        $this->routePathRegexp = $routePathRegexp;
        return $this;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setArguments($arguments)
    {
        $this->arguments = array_replace($this->arguments, $arguments);
        return $this;
    }

    public function getRequestMethods()
    {
        return $this->requestMethods;
    }

    public function setRequestMethods($requestMethods)
    {
        $this->requestMethods = $requestMethods;
        return $this;
    }

    public function getControllerMethod()
    {
        return $this->controllerMethod;
    }

    public function setControllerMethod($controllerMethod)
    {
        $this->controllerMethod = $controllerMethod;
        return $this;
    }

    public function getControllerClass()
    {
        return $this->controller;
    }

    public function setControllerClass($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegMatch()
    {
        return $this->regMatch;
    }

    /**
     * @param mixed $regMatch
     */
    public function setRegMatch(array $regMatch)
    {
        $this->regMatch = $regMatch;
    }
}