<?php
namespace Sojf\Routing\Interfaces;


interface Compiled
{
    public function getViewNameSpace();
    public function setViewNameSpace($viewNameSpace);

    public function getModelNameSpace();
    public function setModelNameSpace($ModelNameSpace);
    
    public function getMethodIndex();
    public function setMethodIndex($methodIndex);
    
    public function getRoutePath();
    public function setRoutePath($routePath);

    public function getRoutePathRegexp();
    public function setRoutePathRegexp($routePathRegexp);

    public function getArguments();
    public function setArguments($arguments);

    public function getRequestMethods();
    public function setRequestMethods($requestMethods);

    public function getControllerMethod();
    public function setControllerMethod($controllerMethod);

    public function getController();
    public function setController($controller);
}