##Sojf Routing Component

###灵活的PHP路由类

- 直观的方式一看就懂，GET:/user，UserController
- 支持RESTful API路由配置，配合sojf\utils包使用
- 支持正则匹配，内容捕获到GET全局变量
- 动态控制器方法，不需要固定控制器方法
- 支持从配置文件加载路由

### 思路
> 一共四个类，
Route(定义路由信息)，
Collection(路由类集合)，
Compiler(路由编译类，生成具体到控制器及方法信息)，
Compiled(存储路由编译后信息，供其它类使用)

### Route对象

> 有三个重要参数

>> **scheme** (string)
    http请求方法和请求路径，用':'分隔。例如，请求方法:请求路径。下方例子的 GET:/user
    
>> **controller** (string)
    当前路由对应的控制器，也可以指定到这个控制器的方法，用'@'分隔。例如，控制器@方法。 方例子的 User@details
    
>> **routeName** (string)
    路由别名，默认会起一个名字，类似route1，route2，route3。用来标识这个路由


例子: 
> $route = new Route('GET:/user', 'UserController@detailsMethod', '路由名称');


也可以是所有请求方法:
> $route = new Route('ANY:/user', 'UserController@detailsMethod', '路由名称');


或者指定的请求方法，用'|'分隔。例如：POST|GET|PUT
> $route = new Route('POST|GET|PUT:/user', 'UserController@detailsMethod', 'here your route name');

使用正则格式：
> {正则表达式}
>> $route = new Route('ANY:/user/{\d+}', 'UserController@detailsMethod', 'here your route name');

如果需要捕获匹配到的内容:
> {正则表达式(捕获内容到GET全局变量)}
>> $route = new Route('ANY:/user/{\d+(userId)}', 'UserController@detailsMethod', 'here your route name');


### 注意
- 所有路由默认视图命名空间为： Src\Model
- 所有路由默认模型命名空间为： Src\View
- 所有路由默认控制器命名空间为： Src\Controller
- 路由编译时会class_exists检查控制器，不存在会抛出错误
- 如果指定路由器的方法，例如UserController@detailsMethod，detailsMethod方法不存在也会抛出错误

### 安装
``` php
composer require sojf/routing
```

### 使用方法1
``` php
require 'vendor/autoload.php';

# 使用composer自动加载
# 在vendor同级创建一个src目录，
# 这个目录是用来存放controller
/*
    "autoload": {
        "psr-4": {
            "Src\\": "src/"
        }
    }
*/

# 创建路由
$home = new Sojf\Routing\Route('ANY:/', 'IndexController@welcome', 'home');
$user = new Sojf\Routing\Route('ANY:/u/{\d+(userId)}', 'IndexController@user', 'user');

# 创建一个路由集合
$routes = new Sojf\Routing\Collection();

# 把路由添加到路由集合中
$routes->add($home);
$routes->add($user);

# 匹配符合pathInfo的路由
$match1 = $routes->match('/');
$match2 = $routes->match('/u/123');

var_dump($match1);
var_dump($match2);

# 如果匹配不到会抛出一个异常
# $match3 = $routes->match('/notFound');

# 下一步就是通过返回匹配的路由对象去解析控制器，sojf/utils包已经提供了一个好用的控制器解析类
# ...

```

### 使用方法2，需要sojf/config包配合使用
> route.yml
```yml
- [ ANY:/ , WelcomeController ]
- [ GET|POST:/u/{.+(userId)} , Buc/TestController@user ]
```

##### PHP代码
```php
require 'vendor/autoload.php';

# 创建一个路由集合
$routes = new Sojf\Routing\Collection();

# 创建配置类
$conf = new Sojf\Config\Config('path/to/route.yml');

# 批量配置路由
# 注意如果对应路控制器目录和命名空间不匹配会抛出错误提示，请自行创建
$routes->conf($conf->get('route'));

$match = $routes->match('/');

var_dump($match);

```