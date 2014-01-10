<?php
namespace Micro\Util;

use Micro\ControllerInterface;

/**
 * Provides Slim/Rails-esque URI matching with Symfony placeholders.
 */
class UriCompiler
{
    protected $handler;
    
    public function __construct(ControllerInterface $controller)
    {
        $this->handler = $controller;
    }
    
    public function compileUri()
    {
        return "#^" . preg_replace_callback(
                "#(\{(\w+)\})+#", 
                array($this, "replace"), 
                $this->handler->uri()
        ) . "$#";
    }
    
    protected function replace(array $match)
    {
        $name  = $match[2];
        $conds = $this->handler->conditions();
        if (isset($conds[$name])) {
            $expr = $conds[$name];
        } else {
            $expr = "[^/]+";
        }
        
        return "(?<$name>$expr)";
    }
    
    public static function compile(ControllerInterface $controller)
    {
        $compiler = new static($controller);
        return $compiler->compileUri();
    }
}

