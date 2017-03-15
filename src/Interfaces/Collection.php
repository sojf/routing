<?php
namespace Sojf\Routing\Interfaces;


use Sojf\Routing\Route;

/**
 * 路由信息集合接口
 */
interface Collection
{
    // 获取集合中所有路由信息对象
    public function all();

    // 批量配置路由信息对象
    public function conf(array $data);

    // 添加单个路由信息对象
    public function add(Route $route);
}