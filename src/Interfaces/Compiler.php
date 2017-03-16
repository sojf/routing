<?php
namespace Sojf\Routing\Interfaces;


/**
 * 路由编译器接口
 * 用来编译路由，并输出一个编译结果对象
 */
interface Compiler
{
    // 执行路由编译
    public function compile();

    // 设置编译结果对象
    public function setCompiled(Compiled $compiled);

    // 获取编译结果对象
    public function getCompiled();

    // 设置将要编译的路由信息对象
    public function setRoute(Route $route);

    // 获取路由信息对象
    public function getRoute();
}