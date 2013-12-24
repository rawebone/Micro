<?php
namespace Micro;

use Symfony\Component\HttpFoundation\Response;

class Application implements ApplicationInterface
{
    /**
     * @var \Micro\HandlerInterface
     */
    protected $handlers = array();
    
    /**
     * @var \Micro\Request
     */
    protected $lastRequest;
    
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $lastResponse;
    
    /**
     * @var \Exception
     */
    protected $lastException;
    
    public function attach(HandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    public function run(Request $req = null, Responder $resp = null)
    {
        $this->reset();
        
        list($req, $resp) = $this->makeRequestAndResponse($req, $resp);
        
        foreach ($this->handlers as $handler) {
            $matcher = new Util\RequestMatcher($handler);
            
            if ($matcher->matches($req)) {
                return $this->dispatch($req, $resp, $handler);
            }
        }
        return false;
    }
    
    public function send()
    {
        if (!is_null($this->lastResponse)) {
            $this->lastResponse->send();
        }
    }
    
    /**
     * @return \Micro\Request
     */
    public function lastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lastResponse()
    {
        return $this->lastResponse;
    }
    
    /**
     * @return \Exception
     */
    public function lastException()
    {
        return $this->lastException;
    }
    
    protected function reset()
    {
        $this->lastRequest = null;
        $this->lastResponse = null;
        $this->lastException = null;
    }
    
    protected function makeRequestAndResponse(Request $req = null, Responder $resp = null)
    {
        $this->lastRequest = $this->lastResponse = null;
        
        return array(
            $req ?: Request::createFromGlobals(),
            $resp ?: new Responder()
        );
    }
    
    protected function dispatch(Request $req, Responder $resp, HandlerInterface $handler)
    {
        $this->lastRequest = $req;
        
        try {
            $return = $handler->handle($req, $resp);
            if (!$return instanceof Response) {
                throw new Exceptions\BadHandlerReturnException($handler);
            }
            
            $this->lastResponse = $return;
            return true;
            
        } catch (\Exception $e) {
            $this->lastException = $e;
            return false;
        }
    }
}
