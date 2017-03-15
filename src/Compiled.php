<?php
namespace Sojf\Routing;


use Sojf\Routing\Interfaces\Compiled as CompiledInterface;

/**
 * 编译结果类
 * 存储路由编译后的结果
 */
class Compiled implements CompiledInterface
{
    protected $appName;             // 应用名
    protected $routePath;           // 路由规则
    protected $routePathRegexp;     // 路由正则
    protected $controllerMethod;    // 控制器方法
    protected $controller;          // 控制器
    protected $viewNameSpace;       // 视图命名空间
    protected $modelNameSpace;      // 模型命名空间
    protected $routeType;           // 路由类型
    protected $matchRes = array();  // 路由正则匹配结果
    public $hasCaptureVar = false;  // 是否有捕获变量

    /**
     * 设置路由类型
     * @param $routeType
     * @return $this
     */
    public function setRouteType($routeType)
    {
        $this->routeType = $routeType;
        return $this;
    }

    /**
     * 获取路由类型
     * @return mixed
     */
    public function getRouteType()
    {
        return $this->routeType;
    }

    /**
     * 设置路由正则
     * @param $routePathRegexp
     * @return $this
     */
    public function setRoutePathRegexp($routePathRegexp)
    {
        $this->routePathRegexp = $routePathRegexp;
        return $this;
    }

    /**
     * 获取路由正则
     * @return mixed
     */
    public function getRoutePathRegexp()
    {
        return $this->routePathRegexp;
    }

    /**
     * 设置路由规则
     * @param $routePath
     * @return $this
     */
    public function setRoutePath($routePath)
    {
        $this->routePath = $routePath;
        return $this;
    }

    /**
     * 获取路由规则
     * @return mixed
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    /**
     * 设置控制类
     * @param $controller
     * @return $this
     */
    public function setControllerClass($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * 获取控制器类
     * @return mixed
     */
    public function getControllerClass()
    {
        return $this->controller;
    }

    /**
     * 设置控制器方法
     * @param $controllerMethod
     * @return $this
     */
    public function setControllerMethod($controllerMethod)
    {
        $this->controllerMethod = $controllerMethod;
        return $this;
    }

    /**
     * 获取控制器方法
     * @return mixed
     */
    public function getControllerMethod()
    {
        return $this->controllerMethod;
    }

    /**
     * 设置应用名
     * @param $appName
     * @return $this
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
        return $this;
    }

    /**
     * 获取应用名
     * @return mixed
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * 设置模型命名空间
     * @param $modelNameSpace
     * @return $this
     */
    public function setModelNameSpace($modelNameSpace)
    {
        $this->modelNameSpace = $modelNameSpace;
        return $this;
    }

    /**
     * 获取模型命名空间
     * @return mixed
     */
    public function getModelNameSpace()
    {
        return $this->modelNameSpace;
    }

    /**
     * 设置视图命名空间
     * @param $viewNameSpace
     * @return $this
     */
    public function setViewNameSpace($viewNameSpace)
    {
        $this->viewNameSpace = $viewNameSpace;
        return $this;
    }

    /**
     * 获取视图命名空间
     * @return mixed
     */
    public function getViewNameSpace()
    {
        return $this->viewNameSpace;
    }

    /**
     * 设置路由正则匹配结果，用于给控制器解析器获取匹配里面的捕获变量
     * @param array $matchRes
     * @return $this
     */
    public function setMatchRes(array $matchRes)
    {
        $this->matchRes = $matchRes;
        return $this;
    }

    /**
     * 获取路由正则匹配结果，用于给控制器解析器获取匹配里面的捕获变量
     * @return array
     */
    public function getMatchRes()
    {
        return $this->matchRes;
    }
}