<?php
namespace Sojf\Routing\Interfaces;


interface Compiler
{
    public function compile();
    public function prepare($compiledClass);
    
    public function setModelNameSpace($modelNameSpace);
    public function setViewNameSpace($viewNameSpace);
    public function setControllerNameSpace($nameSpace);
}