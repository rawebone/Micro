<?php
namespace Micro\Tests\Matching;

use Micro\Matching\UrlMatcher;
use Micro\Util\UrlTools;
use Micro\Tests\TestCase;

class UrlMatcherTest extends TestCase
{
    protected $controller;
    protected $request;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->controller = $this->prophet->prophesize("Micro\\ControllerInterface");
        $this->request = $this->prophet->prophesize("Micro\\Request");
    }
    
    public function testMatch()
    {
        $matcher = new UrlMatcher(new UrlTools());
        
        $this->controller->uri()->willReturn("/abc/{id}");
        $this->controller->conditions()->willReturn(array("id" => "\d+"));
        
        $this->request->getPathInfo()->willReturn("/abc/la");
        $this->assertEquals(false, $matcher->match($this->request->reveal(), $this->controller->reveal()));
        
        $this->request->getPathInfo()->willReturn("/abc/123");
        $this->assertEquals(true, $matcher->match($this->request->reveal(), $this->controller->reveal()));
    }
}
