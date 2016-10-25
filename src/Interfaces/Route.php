<?php
namespace Sojf\Routing\Interfaces;


interface Route
{
    public function setRouteName($name);

    public function setScheme($scheme);
    
    public function setController($controllerClass);
}