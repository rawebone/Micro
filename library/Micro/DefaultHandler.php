<?php
namespace Micro;

abstract class DefaultHandler implements HandlerInterface
{
    private $methods = array();
    private $contentTypes = array();
    private $conditions = array();
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
