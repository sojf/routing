<?php
namespace Sojf\Routing;


use Sojf\Routing\Exceptions\RouteException;
use Sojf\Routing\Interfaces\Route as RouteInterface;

/**
 * 路由信息类，用来存储路由信息
 */
class Route implements RouteInterface
{
    protected $suffix;             // url后缀
    protected $routeName;          // 路由索引
    protected $routeType;          // 路由类型
    protected $routePath;          // 路由规则
    protected $controller;         // 控制器类

    public static $routeCount = 1;
    const SCHEME_DELIMITER = ':';

    /*
     * NORM 正常路由类型，路由规则对应控制器的指定方法
     * DM   动态路由类型，路由规则对应控制器的多个方法，具体哪个方法由用户输入指定
     * REST REST路由类型，根据用户请求方法对应控制器的方法
     * */
    protected $types = ['NORM', 'REST', 'DM'];

    /**
     * Route constructor.
     * @param string $scheme 路由scheme
     * @param string $controller 控制器
     * @param string $suffix url后缀
     * @param string $name  路由索引
     */
    public function __construct($scheme = '', $controller = '', $suffix = '', $name = '')
    {
        if ($scheme) $this->setScheme($scheme);
        if ($suffix) $this->setSuffix($suffix);
        if ($controller) $this->setController($controller);
        $this->setRouteName($name);
    }

    /**
     * 设置路由scheme
     * scheme 由2部分组成，routerType，routerPath
     * @param $scheme
     * @return $this
     */
    public function setScheme($scheme)
    {
        // 简单检测
        if (!mb_strpos($scheme, self::SCHEME_DELIMITER)) {
            throw new RouteException("Error scheme format: $scheme");
        }

        // 拆分scheme
        list($routeType, $routePath) = explode(self::SCHEME_DELIMITER, $scheme, 2);

        // 设置路由类型
        $this->setRouteType($routeType)->setRoutePath($routePath);
        return $this;
    }

    /**
     * 设置路由类型
     * @param $routeType
     * @return $this
     */
    public function setRouteType($routeType)
    {
        // 判断是否合法路由类型
        if (!in_array($routeType, $this->types)) {
            throw new RouteException("Error route type: $routeType");
        }
        $this->routeType = $routeType;
        return $this;
    }

    /**
     * 设置路由规则
     * @param $routePath
     * @return $this
     */
    public function setRoutePath($routePath)
    {
        // 判断是否为空
        if (!$routePath) {
            throw new RouteException("routePath can't be null");
        }

        // 如果不是以 / 开头，自动补上 /
        if (mb_strpos($routePath, '/') !== 0) {
            $routePath = '/' . $routePath;
        }

        $this->routePath = $routePath;
        return $this;
    }

    /**
     * 设置URL后缀
     * @param mixed $suffix
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * 设置路由控制器
     * @param $controllerClass
     * @return $this
     */
    public function setController($controllerClass)
    {
        if ($controllerClass && !is_string($controllerClass)) {
            throw new RouteException('Controller cannot be [' . gettype($controllerClass) . '] , need string type.');
        }
        $this->controller = str_replace('/', '\\', trim($controllerClass));
        return $this;
    }

    /**
     * 设置路由索引
     * @param string $routeName
     * @return $this
     */
    public function setRouteName($routeName = '')
    {
        if (!$routeName) {
            $this->routeName = 'route' . self::$routeCount ;
            self::$routeCount ++;

        } else {
            $this->routeName = $routeName;
        }

        return $this;
    }

    /**
     * 获取路由url后缀
     * @return mixed
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * 获取路由索引
     * @return mixed
     */
    public function getRouteName()
    {
        return $this->routeName;
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
     * 获取路由规则
     * @return mixed
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    /**
     * 获取控制器
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * 设置路由类型
     * @param array $types
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    /**
     * 获取路由类型
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }
}
