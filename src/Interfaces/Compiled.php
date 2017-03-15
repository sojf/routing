<?php
namespace Sojf\Routing\Interfaces;


/**
 * 路由编译结果接口
 */
interface Compiled
{
    // 设置应用名
    public function getAppName();

    // 获取应用名
    public function setAppName($appName);

    // 设置视图命名空间
    public function setViewNameSpace($viewNameSpace);

    // 获取视图命名空间
    public function getViewNameSpace();

    // 设置模型命名空间
    public function setModelNameSpace($ModelNameSpace);

    // 获取模型命名空间
    public function getModelNameSpace();

    // 设置路由规则
    public function setRoutePath($routePath);

    // 获取路由规则
    public function getRoutePath();

    // 设置路由类型
    public function setRouteType($routeType);

    // 获取路由类型
    public function getRouteType();

    // 设置路由正则
    public function setRoutePathRegexp($routePathRegexp);

    // 过去路由正则
    public function getRoutePathRegexp();

    // 设置控制器方法
    public function setControllerMethod($controllerMethod);

    // 获取控制器方法
    public function getControllerMethod();

    // 设置控制器类
    public function setControllerClass($controller);

    // 获取控制器类
    public function getControllerClass();

    // 设置路由正则匹配结果（用于给控制器解析器获取匹配结果）
    public function setMatchRes(array $regMatch);

    // 路由路由正则匹配结果
    public function getMatchRes();
}