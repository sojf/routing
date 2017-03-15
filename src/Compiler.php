<?php
namespace Sojf\Routing;


use Sojf\Routing\Exceptions\RouteException;
use Sojf\Routing\Interfaces\Compiled;
use Sojf\Routing\Interfaces\Compiler as CompilerInterface;
use Sojf\Routing\Interfaces\Route as RouteInterface;

/**
 * 路由编译器
 * 用来编译路由信息对象
 */
class Compiler implements CompilerInterface
{
    /**
     * @var RouteInterface 路由信息对象
     */
    protected $route;

    /**
     * @var Compiled 编译结果存储对象
     */
    protected $compiled;

    public $viewNameSpace        = 'app\\View';      // 视图默认命名空间
    public $modelNameSpace       = 'app\\Model';     // 模型默认命名空间
    public $controllerNameSpace  = 'app\\Controller';// 控制器默认命名空间
    protected $default_capture_reg  = '[a-zA-Z_]+';     // 捕获变量默认正则

    // 保存已经编译过的路由，避免重新编译
    static protected $_compiled;

    // 正则边界符
    const REGEX_DELIMITER = '~';

    // 控制器@方法 分隔符
    const CONTROLLER_DELIMITER = '@';

    /**
     * 编译路由
     * @return Compiled
     */
    public function compile()
    {
        // 判断是否编译过了
        if ($compiled = $this->compiled()) {
            return $compiled;
        }

        // 执行编译
        $this
            ->check()       // 编译检查
            ->routeType()   // 路由类型，['NORM', 'REST', 'DM']
            ->routePath()   // 路由正则
            ->controller()  // 控制器和方法
            ->MV();         // 模型和视图命名空间

        // 返回编译结果
        return $this->finish();
    }

    /**
     * 编译完成
     * @return Compiled
     */
    protected function finish()
    {
        if (!isset(self::$_compiled[$this->route->getRoutePath()])) {
            self::$_compiled[$this->route->getRoutePath()] = $this->compiled;
        }

        return $this->compiled;
    }

    /**
     * 判断是否已经编译过
     * @return null
     */
    protected function compiled()
    {
        return isset(self::$_compiled[$this->route->getRoutePath()]) ? self::$_compiled[$this->route->getRoutePath()] : null;
    }

    /**
     * 检测是否正确的配置
     * @return $this
     */
    protected function check()
    {
        if (!$this->compiled) {
            throw new RouteException("not set Compiled");
        }

        if (!$this->route) {
            throw new RouteException("not set Route");
        }

        return $this;
    }

    /**
     * 设置路由类型
     * @return $this
     */
    protected function routeType()
    {
        $this->compiled->setRouteType($this->route->getRouteType());
        return $this;
    }

    /**
     * 编译路由规则成路由正则
     * @return $this
     */
    protected function routePath()
    {
        // 获取路由规则
        $routePath = $this->route->getRoutePath();


        if (mb_strlen($routePath) === 1 && mb_strpos($routePath, '/') === 0) {
            // 根路由不处理
            $pattern = $routePath;

        } else {

            // 捕获变量搜索替换数组
            $search = array();
            $replace = array();

            $regPattern = '/\`.+`/Ui';
            $commentsPattern = '/\(\?\#(?<comment>.*)\)/Ui';

            // 提取路由规则中的正则表达式
            preg_match_all($regPattern, $routePath, $regs, PREG_SET_ORDER);

            // 遍历路由规则中的正则表达式
            foreach ($regs as $item) {

                // 路由规则中的正则
                $reg = $item[0];

                // 提取正则注释
                preg_match($commentsPattern, $reg, $has_comment);

                /*
                 * 捕获变量处理：
                 *      1. 判断是否存在正则注释
                 *      2. 获取捕获变量名
                 *      3. 修正带注释的正则
                 * */
                if ($has_comment && $capture_var = $this->getCaptureVar($has_comment['comment'])) {

                    // 将被替换的老正则
                    $search[] = $reg;

                    // 去掉` 和 注释内容
                    $routePattern = $this->trimRoutePathReg($reg);

                    // 替换成正确正则
                    $replace[] = '('. '?<' . $capture_var . '>' . $routePattern . ')';
                }
            }

            // 替换路由规则
            $pattern = $search ? str_replace($search, $replace, $routePath) : $routePath;

            // 判断是否有url后缀
            if ($this->route->getSuffix()) {
                $suffix = $this->suffix();
                $pattern .= $suffix;
            }
        }

        // 拼接路由正则
        $pattern = self::REGEX_DELIMITER . '^' . $pattern . '$' . self::REGEX_DELIMITER . 'iu';

        // 设置路由正则，路由规则
        $this->compiled->setRoutePathRegexp($pattern);
        $this->compiled->setRoutePath($this->route->getRoutePath());
        return $this;
    }

    /**
     * 获取捕获变量名
     *
     *   保留关键字 __ctl__ （用来存储动态类型路由，控制器方法）
     *
     * @param $comment string 正则注释
     * @return string array key
     */
    protected function getCaptureVar($comment)
    {
        // 第一个字符
        $symbol = mb_substr($comment, 0, 1);

        switch ($symbol) {
            case '=': // 返回用户指定的变量名
                return mb_substr($comment, 1);
                break;

            case '@': // 方法占位符
                return '__ctl__';
                break;

            default:
                throw new RouteException('Error reg comment parser');
        }
    }

    /**
     * 去掉路由正则中的 ` 和 注释
     * @param $varStr string 路由正则
     * @return mixed|string
     */
    protected function trimRoutePathReg($varStr)
    {
        // 去掉`
        $reg = trim($varStr, '`');

        // 去掉正则注释
        $replacePattern = '/\(\?\#.*\)/';
        $reg = preg_replace($replacePattern, '', $reg);

        // 没有写路由正则，但是有使用捕获变量，使用默认正则
        return $reg ?: $this->default_capture_reg;
    }

    /**
     * 编译路由控制器设置
     * @return $this
     */
    protected function controller()
    {
        $method = '';
        $controller = $this->route->getController();
        $nameSpace = trim($this->controllerNameSpace, '\/');

        // 存在method
        if (mb_strpos($controller, self::CONTROLLER_DELIMITER) !== false) {
            // 提取控制器字串中的方法
            list($controller, $method) = explode(self::CONTROLLER_DELIMITER, $controller, 2);
        }

        // 拼接控制器命名空间
        $controller = $nameSpace . '\\' . str_replace('/', '\\', $controller);

        // 设置控制器，方法
        $this->compiled->setControllerClass($controller);
        $this->compiled->setControllerMethod($method);
        return $this;
    }

    /**
     * 编译模型，视图命名空间；设置应用名
     * @return $this
     */
    protected function MV()
    {
        $controller = strtolower(str_replace('/', '\\', $this->route->getController())); // 获取设置的控制器

        list($controller) = explode('@', $controller);
        list($appName) = explode('\\', $controller);

        // 设置应用名 todo 优化应用名
        $this->compiled->setAppName($appName);

        $model = $this->modelNameSpace;
        $modelNameSpace = $model;

        $view = $this->viewNameSpace;
        $viewNameSpace = $view;

        // 设置模型命名空间，视图命名空间
        $this->compiled->setModelNameSpace($modelNameSpace);
        $this->compiled->setViewNameSpace($viewNameSpace);
        return $this;
    }

    /**
     * 编译url后缀
     * @return array|string
     */
    protected function suffix()
    {
        $suffix = $this->route->getSuffix();
        $suffix = explode('|', $suffix);
        $suffix = join('|\.', $suffix);
        $suffix = '(\.' . $suffix . ')?';
        return $suffix;
    }

    /**
     * 设置要编译的路由对象
     * @param RouteInterface $route
     * @return $this
     */
    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * 获取路由对象
     * @return RouteInterface
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * 设置编译结果对象
     * @param Compiled $compiled
     * @return $this
     */
    public function setCompiled(Compiled $compiled)
    {
        $this->compiled = $compiled;
        return $this;
    }

    /**
     * 获取编译结果对象
     * @param $compiled
     * @return Compiled
     */
    public function getCompiled($compiled)
    {
        return $this->compiled;
    }
}