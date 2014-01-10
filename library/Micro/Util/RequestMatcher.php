<?php
namespace Micro\Util;

use Micro\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * The RequestMatcher validates the core components of an HTTP request against
 * a Controller instance.
 */
class RequestMatcher implements RequestMatcherInterface
{
    /**
     * @var \Micro\ControllerInterface
     */
    public $controller;
    
    /**
     * Stores the compiled URI regex.
     *
     * @var string
     */
    public $compiledUri;
    
    public function __construct(ControllerInterface $controller)
    {
        $this->controller = $controller;
    }
    
    /**
     * Performs the match against the Controller.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return boolean
     */
    public function matches(Request $request)
    {
        foreach ($this->getChecks() as $check) {
            if (!$this->{"match$check"}($request)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Returns the parameters from the URI for the given Request. This should
     * be called *after* the Request has been confirmed to match Controller.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function params(Request $request)
    {
        $matches = array();
        $match = preg_match($this->getUri(), rawurldecode($request->getPathInfo()), $matches);
        
        if ($match !== false && $match > 0) {
            return $this->decodeParameters($matches);
        }
    }
    
    /**
     * Matches whether the HTTP method is valid for the Controller.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return boolean
     */
    protected function matchMethod(Request $req)
    {
        return in_array($req->getMethod(), $this->controller->methods());
    }
    
    /**
     * Matches whether the URI is valid for the Controller.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return boolean
     */
    protected function matchUri(Request $req)
    {
        $match = preg_match($this->getUri(), rawurldecode($req->getPathInfo()));
        return ($match !== false && $match > 0);
    }
    
    /**
     * Matches whether the Controller will accept the Request.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return boolean
     */
    protected function matchAccepts(Request $req)
    {
        return $this->controller->accepts($req);
    }
    
    /**
     * Process the array for strings parameters which have been matched and
     * ensure they are valid for consumption by the controller.
     * 
     * @param array $params
     * @return array
     */
    protected function decodeParameters(array $params)
    {
        $result = array();
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $result[$key] = urldecode($value);
            }
        }
        return $result;
    }
    
    /**
     * Returns the Compiled URI for the Controller.
     * 
     * @return string
     */
    protected function getUri()
    {
        if (!$this->compiledUri) {
            $this->compiledUri = UriCompiler::compile($this->controller);
        }
        
        return $this->compiledUri;
    }
    
    /**
     * Returns the names of methods that should be called to validate a request.
     * 
     * @return array
     */
    protected function getChecks()
    {
        return array("Method", "Uri", "Accepts");
    }
}
