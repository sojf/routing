<?php
namespace Sojf\Routing\Exceptions;


/**
 * 路由异常类
 */
class RouteException extends \RuntimeException
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}