<?php
namespace Micro\Testing;

use Micro\Application;
use Micro\Request;
use Micro\Responder;
use Symfony\Component\HttpFoundation\Response;

class Browser implements BrowserInterface
{
    /**
     * The encapsulated Application instance.
     *
     * @var \Micro\Application
     */
    protected $application;
    
    /**
     * The last request through the system.
     *
     * @var \Micro\Request
     */
    protected $lastRequest;
    
    /**
     * The last response through the system.
     *
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $lastResponse;
    
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function delete($uri, array $headers = array(), $content = "")
    {
        return $this->call($uri, __FUNCTION__, $headers, $content);
    }

    public function get($uri, array $headers = array())
    {
        return $this->call($uri, __FUNCTION__, $headers, "");
    }

    public function head($uri, array $headers = array())
    {
        return $this->call($uri, __FUNCTION__, $headers, "");
    }

    public function lastRequest()
    {
        return $this->lastRequest;
    }

    public function lastResponse()
    {
        return $this->lastResponse;
    }

    public function options($uri, array $headers = array(), $content = "")
    {
        return $this->call($uri, __FUNCTION__, $headers, $content);
    }

    public function patch($uri, array $headers = array(), $content = "")
    {
        return $this->call($uri, __FUNCTION__, $headers, $content);
    }

    public function post($uri, array $headers = array(), $content = "")
    {
        return $this->call($uri, __FUNCTION__, $headers, $content);
    }

    public function put($uri, array $headers = array(), $content = "")
    {
        return $this->call($uri, __FUNCTION__, $headers, $content);
    }
    
    protected function call($uri, $method, array $headers, $content = "")
    {
        $this->lastRequest = $this->lastResponse = null;
        $this->lastRequest = $this->makeRequest($uri, $method, $headers, $content);
        
        $this->application->run($this->lastRequest);
        
        $this->prepareResponse();
        return $this->lastResponse;
    }
    
    protected function makeRequest($uri, $method, array $headers, $content)
    {
        return Request::create(
                $uri, 
                strtoupper($method), 
                array(), 
                array(), 
                array(), 
                $headers, 
                $content
        );
    }
    
    protected function prepareResponse()
    {
        if ($this->application->lastResponse instanceof Response) {
            $this->lastResponse = $this->application->lastResponse;
            return;
        }
        
        $responder = new Responder();
        
        if ($this->application->lastException instanceof \Exception) {
            $this->lastResponse = $responder->standard((string)$this->application->lastException, 503);
            return;
        }
        
        $this->lastResponse = $responder->standard("Unknown Application State", 503);
    }
}
