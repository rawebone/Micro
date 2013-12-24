<?php
namespace Micro\Tests\Utils;

use Mockery as m;
use Micro\Util\UriCompiler;

class UriCompilerTest extends \Micro\Tests\TestCase
{
    public function testBasic()
    {
        $mock = m::mock("\\Micro\\HandlerInterface");
        $mock->shouldReceive("uri")
             ->andReturn("/");
        
        $mock->shouldReceive("conditions")
             ->andReturn(array());
        
        $this->assertEquals("#^/$#", UriCompiler::compile($mock));
    }
    
    public function testWithConditions()
    {
        $mock = m::mock("\\Micro\\HandlerInterface");
        $mock->shouldReceive("uri")
             ->andReturn("/{id}/{name}");
        
        $mock->shouldReceive("conditions")
             ->andReturn(array("id" => "\d+", "name" => "\w+"));
        
        $this->assertEquals("#^/(?<id>\d+)/(?<name>\w+)$#", UriCompiler::compile($mock));
    }
}
