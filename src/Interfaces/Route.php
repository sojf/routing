<?php
namespace Sojf\Routing\Interfaces;


/**
 * 路由信息接口
 * 用来保存路由信息
 */
interface Route
{
    // 设置路由类型
    public function setRouteType($routeType);

    // 获取路由类型
    public function getRouteType();

    // 设置路由规则
    public function setRoutePath($routePath);

    // 获取路由规则
    public function getRoutePath();

    // 设置URL后缀
    public function setSuffix($suffix);

    // 过去URL后缀
    public function getSuffix();

    // 设置路由索引
    public function setRouteName($name);

    // 获取路由索引
    public function getRouteName();

    // 设置控制器
    public function setController($controllerClass);

    // 获取控制器
    public function getController();

    // 设置路由类型
    public function setTypes(array $types);

    // 获取路由类型
    public function getTypes();
}