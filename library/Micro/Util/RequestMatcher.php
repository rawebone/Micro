<?php
namespace Micro\Util;

use Micro\HandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class RequestMatcher implements RequestMatcherInterface
{
    /**
     * @var \Micro\HandlerInterface
     */
    protected $handler;
    
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }
    
    public function matches(Request $request)
    {
        $h = $this->handler;
        
        if (!in_array($request->getMethod(), $h->methods())) {
            return false;
        }
        
        if (!in_array($request->getContentType(), $h->contentTypes())) {
            return false;
        }
        
        if (!$this->matchUri($request, $h)) {
            return false;
        }
        
        return true;
    }
    
    protected function matchUri(Request $req, HandlerInterface $handler)
    {
        $uri = UriCompiler::compile($handler);
        
        $matches = array();
        if (preg_match($uri, rawurldecode($req->getPathInfo()), $matches)) {
            $req->attributes->add($this->decode($matches));
            return true;
        }
        
        return false;
    }
    
    protected function decode(array $params)
    {
        $result = array();
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $result[$key] = urldecode($value);
            }
        }
        return $result;
    }
}
