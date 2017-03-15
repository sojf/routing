<?php
namespace Sojf\Routing;


use Sojf\Routing\Interfaces\Collection as CollectionInterface;
use Sojf\Routing\Exceptions\RouteException;

/**
 * 路由集合类
 * 用来保存路由信息对象
 */
class Collection implements \IteratorAggregate, CollectionInterface
{
    // 路由对象数组
    private $routes = array();

    /**
     * 迭代器
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * 批量添加路由对象到集合中
     * @param array $data
     */
    public function conf(array $data)
    {
        foreach ($data as $item) {

            // 路由设置
            $scheme = isset($item['scheme']) ? $item['scheme'] : '';

            // 执行控制器
            $controller = isset($item['controller']) ? $item['controller'] : '';

            // 路由索引
            $routeName = isset($item['index']) ? $item['index'] : '';

            // URL后缀
            $suffix = isset($item['suffix']) ? $item['suffix'] : '';

            if (!$scheme) {
                throw new RouteException('not found scheme in config file');
            }

            if (!$controller) {
                throw new RouteException('not found controller in config file');
            }

            // 创建路由对象
            $route = new Route($scheme, $controller, $routeName, $suffix);

            // 添加路由对象到集合中
            $this->add($route);
        }
    }

    /**
     * 添加路由对象
     * @param Route $route
     */
    public function add(Route $route)
    {
        unset($this->routes[$route->getRouteName()]);
        $this->routes[$route->getRouteName()] = $route;
    }

    /**
     * 返回所有路由对象
     * @return array
     */
    public function all()
    {
        return $this->routes;
    }
}