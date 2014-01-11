<?php
namespace Micro\Testing;

use Micro\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Encapsulates the result of Profiling a request through the Application.
 * 
 * @property \Micro\Request $request
 * @property \Symfony\Component\HttpFoundation\Response $response
 * @property float $time The amount of time in seconds taken by the request
 * @property float $memory The peak amount of system memory consumed by the process
 */
class ProfileResult
{
    protected $time;
    protected $memory;
    protected $request;
    protected $response;
    
    public function __construct(Request $request, Response $response, $seconds, $memory)
    {
        $this->response = $response;
        $this->request = $request;
        $this->time = $seconds;
        $this->memory = $memory;
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    public function __toString()
    {
        return sprintf(
                "%s\n\n%s\n\n%s\n\n", 
                (string)$this->request, 
                (string)$this->response, 
                "Process took $this->time seconds and consumed $this->memory MB of system memory"
        );
    }
}
