<?php
namespace Micro\Tests\Matching;

use Micro\Matching\MethodMatcher;
use Micro\Tests\TestCase;

class MethodMatcherTest extends TestCase
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
        $matcher = new MethodMatcher();
        
        $this->controller->methods()->willReturn(array("POST"));
        $this->request->getMethod()->willReturn("GET");
        
        $this->assertEquals(false, $matcher->match($this->request->reveal(), $this->controller->reveal()));
        
        $this->controller->methods()->willReturn(array("POST", "GET"));
        $this->assertEquals(true, $matcher->match($this->request->reveal(), $this->controller->reveal()));
        
        $this->controller->methods()->willReturn(array("GET"));
        $this->assertEquals(true, $matcher->match($this->request->reveal(), $this->controller->reveal()));
    }
}
