<?php
namespace Sojf\Routing;


use Sojf\Routing\Exceptions\RouteException;
use Sojf\Routing\Interfaces\Compiler as CompilerInterface;
use Sojf\Routing\Interfaces\Compiled as CompiledInterface;

class Compiler implements CompilerInterface
{
    public $routePath;

    public $requestMethods;

    public $controller;

    protected $compiled;

    protected $compilerLock = false;

    public $modelNameSpace = 'Src\\Model';
    public $viewNameSpace = 'Src\\View';
    public $controllerNameSpace = 'Src\\Controller';
    
    const REGEX_DELIMITER = '#';
    const CONTROLLER_DELIMITER = '@';
    
    public function setModelNameSpace($modelNameSpace)
    {
        $this->modelNameSpace = $modelNameSpace;
    }
    
    public function setViewNameSpace($viewNameSpace)
    {
        $this->viewNameSpace = $viewNameSpace;
    }
    
    public function setControllerNameSpace($controllerNameSpace)
    {
        $this->controllerNameSpace = $controllerNameSpace;
    }
    
    public function prepare($compiled)
    {
        if (!is_string($compiled)) {

            throw new RouteException('compiled must be string.');
        }

        if (!class_exists($compiled)) {

            throw new RouteException('compiled must be class.');
        }

        $interface = CompiledInterface::class;
        if (!in_array($interface, class_implements($compiled))) {

            throw new RouteException("compiled must implement: $interface");
        }

        if (!$this->routePath) {

            throw new RouteException('Route compiler need set routPath.');

        } elseif (!$this->requestMethods) {

            throw new RouteException('Route compiler need set requestMethods.');

        } elseif (!$this->controller) {

            throw new RouteException('Route compiler need set controller.');
        }

        if (!$this->lock()) {

            $this->compiled = $compiled;
        }

        return $this;
    }

    protected function lock()
    {
        return $this->compilerLock ?: false;
    }

    public function compile()
    {
        if ($this->lock()) {

            return $this->compiled;
        }

        if (!$this->compiled) {

            throw new RouteException('Route compile need call prepare method first.');
        }

        /** @var Compiled $compiled */
        $compiled = new $this->compiled();

        $this->requestMethods($compiled)->routePath($compiled)
            ->controller($compiled)->MV($compiled);

        $this->compilerLock = true;

        $this->compiled = $compiled;

        return $compiled;
    }

    protected function routePath(Compiled $compiled)
    {
        $routePath = $this->routePath;

        preg_match_all("#(?P<search>{(?P<reg>.+)?(?:\((?P<var>\w+)\))?})#U", $routePath, $matchRoutePathVar, PREG_SET_ORDER);

        $arguments = array();

        $routeArg = explode('/', $routePath);

        foreach ($matchRoutePathVar as $index => $item) {

            $var = isset($item['var']) ? $item['var'] : '';
            $reg = isset($item['reg']) ? $item['reg'] : '';
            $search = isset($item['search']) ? $item['search'] : '';

            if ($var && !ctype_alpha($var)) {

                $var = htmlspecialchars($var);
                throw new RouteException("Routing variables [{$var}] is illegal characters.");
            }

            array_walk($routeArg, function (&$value, $key) use($search, $var, $reg, &$arguments) {
                
                if ($search == $value) {

                    if (boolval($var)) {

                        $arguments[$key] = $var;
                    }

                    $value = $reg;
                }
            });
        }

        $methodIndex = null;
        array_walk($routeArg, function (&$value, $key) use (&$methodIndex) {

            if ($value === self::CONTROLLER_DELIMITER) {

                $value = '[a-zA-Z]+';
                $methodIndex = $key;
            }
        });

        $routePath = join('/', $routeArg);

        if (mb_strlen($routePath) === 1 && mb_strpos($routePath, '/') === 0) {

            $regexp = self::REGEX_DELIMITER . '^' . $routePath . '$' .self::REGEX_DELIMITER . 's';
        } else {

            $regexp = self::REGEX_DELIMITER . '(^' . $routePath . '/\w+|^' . $routePath . '$)' .self::REGEX_DELIMITER . 's';
        }

        $compiled->setRoutePathRegexp($regexp)->setRoutePath($this->routePath)->setArguments($arguments)->setMethodIndex($methodIndex);
        
        return $this;
    }

    protected function requestMethods(Compiled $compiled)
    {
        $compiled->setRequestMethods($this->requestMethods);
        return $this;
    }

    protected function controller(Compiled $compiled)
    {
        $controller = $this->controller;
        $method = '';

        if (mb_strpos($controller, self::CONTROLLER_DELIMITER) !== false) {

            list($controller, $method) = explode(self::CONTROLLER_DELIMITER, $controller, 2);
        }

        $nameSpace = trim($this->controllerNameSpace, '\/');
        $controller = $nameSpace . '\\' . str_replace('/', '\\', $controller);
        
        if (!class_exists($controller)) {

            throw new RouteException("Controller: [{$controller}] not found");
        }

        if ($method) {

            $reflector = new \ReflectionClass($controller);
            if (!$reflector->hasMethod($method)) {

                throw new RouteException("Error Method: {$controller}@{$method} not found");
            }
        }

        $compiled->setControllerMethod($method)->setController($controller);

        return $this;
    }

    protected function MV(Compiled $compiled)
    {
        $controller = str_replace('/', '\\', $this->controller);

        $model = $this->modelNameSpace;
        $modelNameSpace = $model . '\\' . $controller;
        
        $view = $this->viewNameSpace;
        $viewNameSpace = $view . '\\' . $controller;
        
        $compiled->setModelNameSpace($modelNameSpace)->setViewNameSpace($viewNameSpace);
        return $this;
    }
}