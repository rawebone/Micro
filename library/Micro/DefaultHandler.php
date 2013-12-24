<?php
namespace Micro;

abstract class DefaultHandler implements HandlerInterface
{
    private $methods = array();
    private $contentTypes = array();
    private $conditions = array();
    private $accept = array();
    private $uri;
    
    public function __construct()
    {
        $this->configure();
    }
    
    public function contentTypes()
    {
        return $this->contentTypes;
    }

    public function methods()
    {
        return $this->methods;
    }

    public function uri()
    {
        return $this->uri;
    }
    
    public function conditions()
    {
        return $this->conditions;
    }
    
    public function accept()
    {
        return $this->accept;
    }
    
    /**
     * @return \Micro\DefaultHandler
     */
    protected function addCondition($name, $regex)
    {
        $this->conditions[$name] = $regex;
        return $this;
    }
    
    /**
     * @return \Micro\DefaultHandler
     */
    protected function addContentType($type)
    {
        $this->contentTypes[] = strtolower($type);
        $this->contentTypes = array_unique($this->contentTypes);
        return $this;
    }
    
    /**
     * @return \Micro\DefaultHandler
     */
    protected function addAcceptable($type)
    {
        $this->accept[] = strtolower($type);
        $this->accept = array_unique($this->accept);
        return $this;
    }
    
    /**
     * @return \Micro\DefaultHandler
     */
    protected function addMethod($method)
    {
        $this->methods[] = strtoupper($method);
        $this->methods = array_unique($this->methods);
        return $this;
    }
    
    /**
     * @return \Micro\DefaultHandler
     */
    protected function setDefaults()
    {
        $this->addMethod("get");
        $this->addContentType("text/html");
        $this->addAcceptable("*/*");
        return $this;
    }
    
    /**
     * @return \Micro\DefaultHandler
     */
    protected function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    abstract protected function configure();
}
