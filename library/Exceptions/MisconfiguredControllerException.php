<?php
namespace Micro\Exceptions;

class MisconfiguredControllerException extends \Exception
{
    protected $handler;
    
    public function __construct(\Micro\ControllerInterface $handler)
    {
        $this->handler = $handler;
        $msg = sprintf(
                "The Controller '%s' has not been configured correctly.",
                get_class($handler)
        );
        
        parent::__construct($msg);
    }
    
    public function getHandler()
    {
        return $this->handler;
    }
}
