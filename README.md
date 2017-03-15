# 路由类库（Routing）

> 本路由类库最终目的只是提供路由编译结果，拿到结果后可以按实际需求开发。

### 安装
``` php
composer require sojf/routing
```

## 快速例子：
```php
// 使用composer自动加载
require 'vendor/autoload.php';

use Sojf\Routing\Route;
use Sojf\Routing\Compiler;
use Sojf\Routing\Compiled;

// 设置路由
$route = new Route('NORM:/api-(?<token>\w*)', 'Blog@user');

$compiler = new Compiler(); // 实例化编译器
$compiled = new Compiled(); // 实例化编译结果存储对象

$result = $compiler
    ->setRoute($route) // 设置要编译的路由
    ->setCompiled($compiled) // 设置编译结果存储类
    ->compile(); // 执行路由编译

print_r($result);
/*
Sojf\Routing\Compiled Object
(
    [appName:protected] => blog
    [routePath:protected] => /api-(?<token>\w*)
    [routePathRegexp:protected] => ~^/api-(?<token>\w*)$~iu
    [controllerMethod:protected] => user
    [controller:protected] => app\Controller\Blog
    [viewNameSpace:protected] => app\View
    [modelNameSpace:protected] => app\Model
    [routeType:protected] => NORM
    [matchRes:protected] => Array
        (
        )

    [hasCaptureVar] => 
)
*/

// 获取路由匹配正则表达式
$reg = $result->getRoutePathRegexp();

// 假设这是当前请求路由
$requestUlr = '/api-3bbe1210b';

// 执行匹配
preg_match($reg, $requestUlr, $matches);

if ($matches) {
    echo '路由配置成功, 接着执行其它逻辑', '<br>', PHP_EOL;
    print_r($matches);
    /*
        Array
        (
            [0] => /api-3bbe1210b
            [token] => 3bbe1210b
            [1] => 3bbe1210b
        )
    */
} else {
    echo '路由匹配失败';
}
```
---

web开发，离不开http协议，http协议抽象化后可得出2个概念: **请求** **响应**。
路由处理的便是http**请求**，具体来说，路由处理http请求中的**URL**部分。
本路由类库有三个概念：**路由类型**，**路由规则**，**路由目标**。

- 路由类型：用来区分不同使用场景
- 路由规则：用来匹配判断当前请求URL
- 路由目标：当请求URL匹配成功后，需要执行的目标（通常是控制器的方法）

### 路由规则：
- **路由规则 == 正则表达式 == 请求URL**
- 作用是判断当前**请求URL**是否和路由规则匹配

### 路由规则例子:

| **当前请求URL** | **路由规则** | **匹配结果** |
| ------- | ------- | ------- |
| `/user` | `/user(/name)?` | 成功 |
| `/user/name` | `/user(/name)?` | 成功 |
| `/user/money` | `/user/name` | 失败 |
| `/user/info` | `/user/\w+` | 成功 |
| `/user/100` | `/user/\w+` | 失败 |
| `/img/2010/10/1` | `/img/\d{4}/\d{1,2}/\d{1}` | 成功 |
| `/img/year/month/day` | `/img/\d{4}/\d{1,2}/\d{1}` | 失败 |

### 捕获变量：

**如何获取当前请求URL中的值？**
由于路由规则就是正则表达式，所以可以使用正则子组，为了方便使用这个子组，
还可以给子组命名，这样便可以在正则匹配结果中获取的到，然后进行你下一步的操作。

| **当前请求URL** | **路由规则** | **正则匹配结果（数组）** |
| ------- | ------- | ------- |
| `/api-3bbe1210b` | `/api-(?<token>\w*)` | `[token] => 3bbe1210b`
| `/img/2010/10/1` | `/img/(?<year>\d{4})/(?<month>\d{1,2})/(?<day>\d{1})` | `[year] => 2010, [month] => 10, [day] => 1`

### 路由类型：

路由类型，用来区分不同使用场景。
需要说明的是，本路由类库最终目的只是提供路由编译结果，拿到结果后可以按实际需求开发，如不适合，完全可以替换掉。
下面是默认的路由类型，主要用于自己的框架，供大家参考。

| **路由类型** | **类型标识（区分大小写）** | **使用要求** | **说明** |
| ------------ | ------------ | ------------ | ------------ |
| 普通路由 | `NORM` | 必须指定控制器@方法 | URL和控制器@方法是一对一关系 |
| 动态路由 | `DM` | 必须在路由规则设置子组命名`_DM_` | URL和控制器@方法是一对多关系 |
| REST路由 | `REST` | 暂无 | http请求方法和应控制器@方法一对一关系 |

---

#### 本类库主要有四个类：
- Route（定义路由信息）
- Collection（路由信息集合）
- Compiler（路由编译类，输出编译结果对象） 
- Compiled（存储路由编译后信息，供其它类使用，进行下一步操作）

### Route类

| **参数** | **默认值** | **说明** | **格式** |
| ------------ | ------------ | ------------ | ------------ |
| scheme | 空字符串 | 路由方案，用来指定路由类型和路由规则 | 用冒号分隔，不能有空格。`路由类型：路由规则`
| controller | 空字符串 | 控制器，指定路由匹配成功后执行的目标 | `控制器@方法`
| suffix | 空字符串 |路由后缀，seo优化用。例如，url以html，shtml结尾 | 任意字符串，有多个后缀可用`|`分隔。`html|shtml`
| name | 空字符串 | 路由索引，不可以和别的name重名，用于后期查找路由。暂时不用理会，不传可以 | 任意字符串


例子1: 
```
 $route = new Sojf\Routing\Route('NORM:/user', 'UserController@detailsMethod', 'html|shtml', 'routeName');
 ```

例子2: 
```
$route = new Sojf\Routing\Route();

$route->setScheme('NORM:/api-(?<token>\w*)');
$route->setController('Blog@user');
$route->setSuffix('html|shtml');
$route->setRouteName('routeName');
```

### Collection类

如果有很多路由，可以使用Collection对象来存储路由

- 注：Collection类没有构造方法

| **方法** | **参数** | **返回值** | **说明** |
| ------------ | ------------ | ------------ | ------------ |
| conf | `array $data` | 无 | 批量添加路由对象到集合中，数组格式为 `[['scheme'=>'', 'controller'=>'', 'suffix'=>'', 'index'=>'']]`
| add | `Route $route` | 无 | 添加路由对象到集合中
| all | 无 | `array` | 返回所有路由对象 

例子: 
```
// 实例化路由编译器
$compiler = new Sojf\Routing\Compiler();

// 实例话路由编译结果存储类
$compiled = new Sojf\Routing\Compiled();

// 实例化路由集合
$collection = new Sojf\Routing\Collection();

// 路由配置
$routes = array(
    array('scheme' => 'NORM:/user', 'controller' => 'Blog@user'),
    array('scheme' => 'NORM:/user', 'controller' => 'Blog@user'),
    array('scheme' => 'NORM:/user', 'controller' => 'Blog@user')
);

// 批量添加路由到集合中
$collection->conf($routes);

// 假设这是当前请求URL
$requestUrl = '/user';

// 遍历路由集合，匹配当前请求URL
foreach ($collection as $name => $route) {

    // 设置要编译的路由对象
    $compiler->setRoute($route);

    // 设置编译结果存储对象
    $compiler->setCompiled($compiled);

    // 开始编译路由对象
    $compiled = $compiler->compile();

    // 判断当前请求是否和路由匹配
    if (!preg_match($compiled->getRoutePathRegexp(), $requestUrl, $matches)) {
        // 没有匹配到路由，继续遍历路由集合
        continue;
    } else {

        /*
         * 匹配到当前请求对应的路由
         * 保存匹配结果
         * */
        $compiled->setMatchRes($matches);

        // 业务逻辑 。。
        print_r($compiled);

        break;
    }
}
 ```

### Compiler类
路由编译器，主要生成路由匹配正则表达式。
如果不适合，可自行替换掉路由编译器。

### Compiled类
路由编译结果存储类，主要用来保存编译结果，供后续逻辑使用。
