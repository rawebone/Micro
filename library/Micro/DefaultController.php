<?php
namespace Micro;

use \Teapot\StatusCode\Http;

/**
 * The DefaultController provides a convenient way of configuring Controllers
 * for use in projects.
 */
abstract class DefaultController implements ControllerInterface, Http
{
    protected $methods = array();
    protected $conditions = array();
    protected $uri;
    
    public function __construct()
    {
        $this->configure();
        $this->validate();
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
    
    public function accepts(Request $req)
    {
        return true;
    }
    
    /**
     * @return \Micro\DefaultController
     */
    protected function addCondition($name, $regex)
    {
        $this->conditions[$name] = $regex;
        return $this;
    }
    
    /**
     * @return \Micro\DefaultController
     */
    protected function addMethod($method)
    {
        $this->methods[] = strtoupper($method);
        $this->methods = array_unique($this->methods);
        return $this;
    }
    
    /**
     * @return \Micro\DefaultController
     */
    protected function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Ensures that the Controller is safe for use in the Application.
     * 
     * @throws \Micro\Exceptions\MisconfiguredControllerException
     * @return void
     */
    protected function validate()
    {
        if (is_null($this->uri)) {
            throw new Exceptions\MisconfiguredControllerException($this);
        }
    }
    
    /**
     * Prepares the Controller for use.
     * 
     * @return void
     */
    abstract protected function configure();
}
