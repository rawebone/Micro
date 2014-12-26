<?php
namespace Micro\Exceptions;

class BadHandlerReturnException extends \Exception
{
    protected $handler;
    
    public function __construct(\Micro\ControllerInterface $handler)
    {
        $this->handler = $handler;
        $msg = sprintf(
                "Handler for URI '%s' produced an invalid return, should be a Response",
                $handler->uri()
        );
        
        parent::__construct($msg);
    }
    
    public function getHandler()
    {
        return $this->handler;
    }
}
