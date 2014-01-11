<?php
namespace Micro\Testing;

use Micro\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Encapsulates the result of a Trace through the Application.
 * 
 * @property \Micro\Request $request
 * @property \Symfony\Component\HttpFoundation\Response $response
 * @property array $logs
 */
class TraceResult
{
    protected $response;
    protected $request;
    protected $logs;
    
    public function __construct(Request $request, Response $response, array $logs)
    {
        $this->response = $response;
        $this->request = $request;
        $this->logs = $logs;
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
                $this->getLogs()
        );
    }
    
    protected function getLogs()
    {
        $str = "";
        foreach ($this->logs as $entry) {
            $str .= sprintf(
                    "%s %f\t%s\n",
                    "({$entry["level"]})",
                    $entry["timestamp"],
                    $entry["message"]
            );
        }
        return $str;
    }
}
