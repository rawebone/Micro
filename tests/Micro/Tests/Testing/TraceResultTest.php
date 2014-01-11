<?php
namespace Micro\Tests\Testing;

use Micro\Request;
use Micro\Tests\TestCase;
use Micro\Testing\TraceResult;
use Symfony\Component\HttpFoundation\Response;
use Mockery as m;

class TraceResultTest extends TestCase
{
    public function testResult()
    {
        $req = new Request();
        $resp = new Response();
        
        $result = new TraceResult($req, $resp, array());
        $this->assertSame($req, $result->request);
        $this->assertSame($resp, $result->response);
        $this->assertEquals(array(), $result->logs);
    }
    
    public function testResultToString()
    {
        $req = m::mock(new Request());
        $req->shouldReceive("__toString")
            ->andReturn("Request");
        
        $resp = m::mock(new Response());
        $resp->shouldReceive("__toString")
             ->andReturn("Response");
        
        $logs = array(
            array(
                "level" => "level",
                "message" => "message",
                "timestamp" => 10.0,
                "context" => array()
            )
        );
        
        $result = new TraceResult($req, $resp, $logs);
        $exepcted = "Request\n\nResponse\n\n(level) 10.000000\tmessage\n\n\n";
        $this->assertEquals($exepcted, (string)$result);
    }
}
