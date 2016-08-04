<?php
namespace Sojf\Routing;


use Sojf\Routing\Exceptions\RouteException;
use Sojf\Routing\Interfaces\Route as RouteInterface;

class Route extends Compiler implements RouteInterface
{
    public $routeName;

    public static $routeCount = 1;

    const REQUEST_METHOD_DELIMITER = '|';

    const SCHEME_DELIMITER = ':';

    protected $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'ANY'];

    public function __construct($scheme = '', $controller = '', $name = '')
    {
        if ($scheme) {

            $this->setScheme($scheme);
        }

        if ($controller) {

            $this->setController($controller);
        }

        $this->setRouteName($name);
    }

    public function setRouteName($routeName = '')
    {
        if (!is_string($routeName)) {

            throw new RouteException('Route name is not string.');
        }

        if (!$routeName) {

            $this->routeName = 'route' . self::$routeCount ;
            self::$routeCount ++;
        } else {

            $this->routeName = $routeName;
        }
    }

    public function setScheme($scheme)
    {
        if (!mb_strpos($scheme, self::SCHEME_DELIMITER)) {
            
            throw new RouteException("Error scheme format: $scheme");
        }

        list($methods, $routePath) = explode(self::SCHEME_DELIMITER, $scheme, 2);

        $this->setRequestMethods($methods)->setRoutePath($routePath);

        return $this;
    }

    public function setController($controller)
    {
        if ($controller && !is_string($controller)) {

            // todo: Really need another type ? E.g. closure, object
            throw new RouteException('Controller cannot be [' . gettype($controller) . '] , need string type.');
        }

        $this->controller = str_replace('/', '\\', trim($controller));

        return $this;
    }

    protected function setRoutePath($routePath)
    {
        if (!$routePath) {

            throw new RouteException("routePath can't be null");
        }

        if (mb_strpos($routePath, '/') !== 0) {

            $routePath = '/' . $routePath;
        }

        $this->routePath = $routePath;
        return $this;
    }
    
    protected function setRequestMethods($requestMethods)
    {
        $requestMethods = trim(strtoupper($requestMethods), self::REQUEST_METHOD_DELIMITER);
        if (mb_strpos($requestMethods, self::REQUEST_METHOD_DELIMITER) !== false) {

            $requestMethods = explode(self::REQUEST_METHOD_DELIMITER, $requestMethods);
            $flag = array_diff($requestMethods, $this->verbs);
        } else {

            $flag = in_array($requestMethods, $this->verbs) ? false : true;
        }

        if ($flag) {
            throw new RouteException("Error requestMethods: $requestMethods");
        }

        $this->requestMethods = is_string($requestMethods) ? array($requestMethods) : $requestMethods;
        return $this;
    }
}
