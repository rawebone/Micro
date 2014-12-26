<?php
namespace Micro\Tests\Matching;

use Micro\Matching\AcceptsMatcher;
use Micro\Tests\TestCase;

class AcceptsMatcherTest extends TestCase
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
        $matcher = new AcceptsMatcher();
        
        $this->controller->accepts($this->request->reveal())->willReturn(false);
        
        $this->assertEquals(false, $matcher->match($this->request->reveal(), $this->controller->reveal()));
        
        $this->controller->accepts($this->request->reveal())->willReturn(true);
        $this->assertEquals(true, $matcher->match($this->request->reveal(), $this->controller->reveal()));
    }
}
