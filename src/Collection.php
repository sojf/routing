<?php
namespace Sojf\Routing;


use Sojf\Routing\Interfaces\Collection as CollectionInterface;
use Sojf\Routing\Exceptions\RouteException;

class Collection implements \IteratorAggregate, CollectionInterface
{
    private $routes = array();
    
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    public function conf(array $data, array $conf = array())
    {
        foreach ($data as $item) {

            $scheme = isset($item['scheme']) ? $item['scheme'] : '';
            $controller = isset($item['controller']) ? $item['controller'] : '';
            $routeName = isset($item['index']) ? $item['index'] : '';
            $suffix = isset($item['suffix']) ? $item['suffix'] : isset($conf['suffix']) ? $conf['suffix'] : '';

            if (!$scheme) {
                
                throw new RouteException('not found scheme in config file');
            }

            if (!$controller) {
                
                throw new RouteException('not found controller in config file');
            }

            $route = new Route($scheme, $controller, $routeName, $suffix);

//            $route->setMVC($m, $v, $c);
            $route->setSuffix($suffix);

            $this->add($route);
        }
    }
    
    public function add(Route $route)
    {
        unset($this->routes[$route->routeName]);
        $this->routes[$route->routeName] = $route;
    }

    public function all()
    {
        return $this->routes;
    }
    
    public function match($pathInfo, $method = 'GET', $compiledClass = Compiled::class)
    {
        foreach ($this as $name => $route) {

            /** @var Route $route */
            /** @var Compiled $compiled */
            $compiled = $route->prepare($compiledClass)->compile();

            $routePathRegexp = $compiled->getRoutePathRegexp();

            if (!preg_match($routePathRegexp, $pathInfo, $matches)) {
                continue;
            } else {
                $compiled->setRegMatch($matches);
            }

            // check HTTP method requirement
            if ($requiredMethods = $compiled->getRequestMethods()) {
                // HEAD and GET are equivalent as per RFC
                if ('HEAD' === $method) {
                    $method = 'GET';
                }

                if (!in_array($method, $requiredMethods)) {

                    if (!in_array('ANY', $requiredMethods)) {

                        continue;
                    }
                }
            }

            return $compiled;
        }

        $pathInfo = htmlspecialchars($pathInfo);
        throw new RouteException("Not found route：$pathInfo");
    }
}