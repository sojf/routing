<?php
namespace Sojf\Routing\Interfaces;


use Sojf\Routing\Route as addRoute;

interface Collection
{
    public function all();

    public function conf(array $conf);
    
    public function add(addRoute $route);

    public function match($pathInfo, $method, $compiled);
}