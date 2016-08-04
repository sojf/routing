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
    protected $methodIndex;
    protected $viewNameSpace;
    protected $ModelNameSpace;

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

    public function getMethodIndex()
    {
        return $this->methodIndex;
    }

    public function setMethodIndex($methodIndex)
    {
        $this->methodIndex = $methodIndex;
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

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }
}