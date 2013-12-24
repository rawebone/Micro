<?php
namespace Micro\Tests\Utils;

use Micro\Util\RequestMatcher;
use Mockery as m;

class RequestMatcherTest extends \Micro\Tests\TestCase
{
    public function testFailOnBadMethod()
    {
        $h = $this->getHandlerMock();
        $h->shouldReceive("methods")
          ->andReturn(array("GET"));
        
        $r = $this->getRequestMock();
        $r->shouldReceive("getMethod")
          ->andReturn("POST");
        
        $this->runRequestMatch(false, $r, $h);
    }
    
    public function testFailOnBadContentType()
    {
        $h = $this->getHandlerMock();
        $h->shouldReceive("methods")
          ->andReturn(array("GET"));
        
        $h->shouldReceive("contentTypes")
          ->andReturn(array("text/html"));
        
        $r = $this->getRequestMock();
        $r->shouldReceive("getMethod")
          ->andReturn("GET");
        
        $r->shouldReceive("getContentType")
          ->andReturn("application/json");
        
        $this->runRequestMatch(false, $r, $h);
    }
    
    public function testFailOnBadUri()
    {
        $h = $this->getHandlerMock();
        $h->shouldReceive("methods")
          ->andReturn(array("GET"));
        
        $h->shouldReceive("contentTypes")
          ->andReturn(array("text/html"));
        
        $h->shouldReceive("uri")
          ->andReturn("/really/long/path");
        
        $r = $this->getRequestMock();
        $r->shouldReceive("getMethod")
          ->andReturn("GET");
        
        $r->shouldReceive("getContentType")
          ->andReturn("text/html");
        
        $r->shouldReceive("getPathInfo")
          ->andReturn("/");
        
        $this->runRequestMatch(false, $r, $h);
    }
    
    public function testParametersMerged()
    {
        $h = $this->getHandlerMock();
        $h->shouldReceive("methods")
          ->andReturn(array("GET"));
        
        $h->shouldReceive("contentTypes")
          ->andReturn(array("text/html"));
        
        $h->shouldReceive("uri")
          ->andReturn("/really/long/path/{id}");
        
        $h->shouldReceive("conditions")
          ->andReturn(array("id" => "\d+"));
        
        $r = $this->getRequestMock();
        $r->shouldReceive("getMethod")
          ->andReturn("GET");
        
        $r->shouldReceive("getContentType")
          ->andReturn("text/html");
        
        $r->shouldReceive("getPathInfo")
          ->andReturn("/really/long/path/13");
        
        $a = $this->getReqAttsMock();
        $a->shouldReceive("add")
          ->with(array("id" => "13"));
        
        $r->attributes = $a;
        
        $this->runRequestMatch(true, $r, $h);
    }
    
    protected function runRequestMatch($expect, $req, $handler)
    {
        $matcher = new RequestMatcher($handler);
        $this->assertEquals($expect, $matcher->matches($req));
    }
    
    /**
     * @return \Micro\HandlerInterface
     */
    protected function getHandlerMock()
    {
        return m::mock("\\Micro\\HandlerInterface");
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequestMock()
    {
        return m::mock("\\Symfony\\Component\\HttpFoundation\\Request");
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getReqAttsMock()
    {
        return m::mock("\\Symfony\\Component\\HttpFoundation\\ParameterBag");
    }
}
