<?php
namespace Micro\Tests\Testing;

use Micro\Testing\Client as Subject;
use Micro\Tests\TestCase;
use Mockery as m;
use \Symfony\Component\HttpFoundation\Response;

class ClientTest extends TestCase
{
    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage Insulation is not supported by Micro
     */
    public function testInsulationThrowsException()
    {
        $client = new Subject(m::mock("Micro\\ApplicationInterface"));
        $client->insulate();
    }
    
    public function testGetApp()
    {
        $app = m::mock("Micro\\ApplicationInterface");
        $client = new Subject($app);
        $this->assertSame($app, $client->getApp());
    }
    
    public function testRequestResponse()
    {
        $app = m::mock("Micro\\ApplicationInterface");
        $app->shouldReceive("run")
            ->withAnyArgs()
            ->andReturn(true);
        
        $app->shouldReceive("lastResponse")
            ->withNoArgs()
            ->andReturn(Response::create());
        
        $client   = new Subject($app);
        $response = $client->request("GET", "/");
    }
    
}
