<?php
namespace Sojf\Routing;


use Sojf\Routing\Exceptions\RouteException;
use Sojf\Routing\Interfaces\Compiler as CompilerInterface;
use Sojf\Routing\Interfaces\Compiled as CompiledInterface;

class Compiler implements CompilerInterface
{
    public $suffix;

    public $routePath;

    public $requestMethods;

    public $controllerClass;

    protected $compiledClass;

    /**
     * @var Compiled $compiled
     */
    protected $compiledObj;

    protected $compilerLock = false;

    public $modelNameSpace = 'App\\Model';
    public $viewNameSpace = 'App\\View';
    public $controllerNameSpace = 'App\\Controller';
    
    const REGEX_DELIMITER = '~';
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
    
    public function prepare($compiledClass)
    {
        if (!is_string($compiledClass)) {

            throw new RouteException('compiledClass must be string.');
        }

        if (!class_exists($compiledClass)) {

            throw new RouteException('compiledClass must be class.');
        }

        $interface = CompiledInterface::class;
        if (!in_array($interface, class_implements($compiledClass))) {

            throw new RouteException("compiledClass must implement: $interface");
        }

        if (!$this->routePath) {

            throw new RouteException('Route compiler need set routPath.');

        } elseif (!$this->requestMethods) {

            throw new RouteException('Route compiler need set requestMethods.');

        } elseif (!$this->controllerClass) {

            throw new RouteException('Route compiler need set controllerClass.');
        }

        if (!$this->lock()) {

            $this->compiledClass = $compiledClass;
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

            return $this->compiledObj;
        }

        if (!$this->compiledClass) {

            throw new RouteException('Route compile need call prepare method set Compiled class to save data.');
        }

        $this->compiledObj = new $this->compiledClass();

        $this->requestMethods()->routePath()
            ->controller()->MV();

        $this->compilerLock = true;

        return $this->compiledObj;
    }

    /**
     * 正则注释处理
     * @param $comment string 正则注释
     * @return string array key
     */
    protected function regCommentParser($comment)
    {
        $symbol = mb_substr($comment, 0, 1);
        switch ($symbol) {
            case '=':
                return mb_substr($comment, 1);
                break;

            case '@': // 控制器占位符
                return '__ctl__';
                break;

            default:
                throw new RouteException('Error reg comment parser');
        }
    }

    /**
     * 路由正则处理
     * @param $varStr string 路由正则
     * @return mixed|string
     */
    protected function routeRegParser($varStr)
    {
        $reg = trim($varStr, '`'); // 去掉`
        $replacePattern = '/\(\?\#.*\)/'; // 去掉正则注释
        $reg = preg_replace($replacePattern, '', $reg);

        return $reg ? $reg : '\w+'; // 没有写路由正则，使用默认正则
    }

    /**
     * core: 更新
     * @return $this
     */
    protected function routePath()
    {
        $routePath = $this->routePath;

        if (mb_strlen($routePath) === 1 && mb_strpos($routePath, '/') === 0) {
            // 根路由不处理
            $pattern = $routePath;

        } else {

            $regPattern = '/\`.+`/Ui'; // 提取路由正则
            $commentsPattern = '/\(\?\#(?<comment>.*)\)/Ui'; // 提取正则注释

            preg_match_all($regPattern, $routePath, $regs, PREG_SET_ORDER);

            $search = array();
            $replace = array();
            foreach ($regs as $item) {

                $reg = $item[0];
                preg_match($commentsPattern, $reg, $res);

                if ($res && $arrayKey = $this->regCommentParser($res['comment'])) {

                    $search[] = $reg;
                    $routePattern = $this->routeRegParser($reg);
                    $replace[] = '('. '?<' . $arrayKey . '>' . $routePattern . ')';
                }
            }

            $pattern = $search ? str_replace($search, $replace, $routePath) : $routePath;

            if ($this->suffix) {
                $suffix = $this->suffix();
                $pattern .= $suffix;
            }
        }

        $pattern = self::REGEX_DELIMITER . '^' . $pattern . '$' . self::REGEX_DELIMITER . 'i';
        $this->compiledObj->setRoutePathRegexp($pattern)->setRoutePath($this->routePath);
        return $this;
    }

    protected function suffix()
    {
        $suffix = $this->suffix;
        $suffix = explode('|', $suffix);

        $suffix = join('|\.', $suffix);
        $suffix = '(\.' . $suffix . ')?';

        return $suffix;
    }

    /**
     * core: 旧版
     * @return $this
     */
    protected function routePath1()
    {
        $routePath = $this->routePath;

        // 提取路由字串，获取路由正则
        preg_match_all("#(?P<search>{(?P<reg>.+)?(?:\((?P<var>\w+)\))?})#U", $routePath, $matchRoutePathVar, PREG_SET_ORDER);

        $arguments = array();
        $routeArg = explode('/', $routePath);

        // 路由正则解析
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

                        // 记录捕获变量在pathInfo中位置
                        $arguments[$key] = $var;
                    }

                    // 替换捕获变量为正确正则
                    $value = $reg;
                }
            });
        }

        // 动态方法位置解析
        $dynamicMethodIndex = null;
        array_walk($routeArg, function (&$value, $key) use (&$dynamicMethodIndex) {

            if ($value === self::CONTROLLER_DELIMITER) {

                $value = '[a-zA-Z]+';
                $dynamicMethodIndex = $key;
            }
        });

        $routePath = join('/', $routeArg);

        if (mb_strlen($routePath) === 1 && mb_strpos($routePath, '/') === 0) {
            // 根路由处理
            $regexp = self::REGEX_DELIMITER . '^' . $routePath . '$' .self::REGEX_DELIMITER . 's';

        } else {
            // core: 路由匹配正则
            //$regexp = self::REGEX_DELIMITER . '(^' . $routePath . '$)' .self::REGEX_DELIMITER . 's';
            $regexp = self::REGEX_DELIMITER . '(^' . $routePath . '/\w+|^' . $routePath . '$)' .self::REGEX_DELIMITER . 's';
        }

        $this->compiledObj->setRoutePathRegexp($regexp)->setRoutePath($this->routePath)->setArguments($arguments)->setDynamicMethodIndex($dynamicMethodIndex);
        return $this;
    }

    /**
     * core: 维护
     * @return $this
     */
    protected function controller()
    {
        $method = '';
        $controllerClass = $this->controllerClass;
        $nameSpace = trim($this->controllerNameSpace, '\/');

        if (mb_strpos($controllerClass, self::CONTROLLER_DELIMITER) !== false) {

            // 提取控制器字串中的方法
            list($controllerClass, $method) = explode(self::CONTROLLER_DELIMITER, $controllerClass, 2);
        }

        $controllerClass = $nameSpace . '\\' . str_replace('/', '\\', $controllerClass);

        if (!class_exists($controllerClass)) {

            throw new RouteException("Controller: {$controllerClass} Not found");
        }

        if ($method) {

            $reflector = new \ReflectionClass($controllerClass);
            if (!$reflector->hasMethod($method)) {

                throw new RouteException("Error Method: {$controllerClass}@{$method} not found");
            }
        }

        $this->compiledObj->setControllerMethod($method)->setControllerClass($controllerClass);
        return $this;
    }

    protected function MV()
    {
        $controller = str_replace('/', '\\', $this->controllerClass);

        $model = $this->modelNameSpace;
        $modelNameSpace = $model . '\\' . $controller;

        $view = $this->viewNameSpace;
        $viewNameSpace = $view . '\\' . $controller;

        $this->compiledObj->setModelNameSpace($modelNameSpace)->setViewNameSpace($viewNameSpace);
        return $this;
    }

    protected function requestMethods()
    {
        $this->compiledObj->setRequestMethods($this->requestMethods);
        return $this;
    }
}