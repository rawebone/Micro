<?php
namespace Micro\Tests\Utils;

use Mockery as m;
use Micro\Util\UriCompiler;
use Micro\Tests\TestCase;

class UriCompilerTest extends TestCase
{
    public function testBasic()
    {
        $mock = m::mock("\\Micro\\ControllerInterface");
        $mock->shouldReceive("uri")
             ->andReturn("/");
        
        $mock->shouldReceive("conditions")
             ->andReturn(array());
        
        $this->assertEquals("#^/$#", UriCompiler::compile($mock));
    }
    
    public function testWithConditions()
    {
        $mock = m::mock("\\Micro\\ControllerInterface");
        $mock->shouldReceive("uri")
             ->andReturn("/{id}/{name}");
        
        $mock->shouldReceive("conditions")
             ->andReturn(array("id" => "\d+", "name" => "\w+"));
        
        $this->assertEquals("#^/(?<id>\d+)/(?<name>\w+)$#", UriCompiler::compile($mock));
    }
    
    public function testWithParametersNoConditions()
    {
        $mock = m::mock("\\Micro\\ControllerInterface");
        $mock->shouldReceive("uri")
             ->andReturn("/{id}/{name}");
        
        $mock->shouldReceive("conditions")
             ->andReturn(array());
        
        $this->assertEquals("#^/(?<id>[^/]+)/(?<name>[^/]+)$#", UriCompiler::compile($mock));
    }
}
