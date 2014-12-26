<?php
namespace Micro\Tests\Testing;

use Micro\Request;
use Micro\Tests\TestCase;
use Micro\Testing\ProfileResult;
use Symfony\Component\HttpFoundation\Response;
use Mockery as m;

class ProfileResultTest extends TestCase
{
    public function testResult()
    {
        $req = new Request();
        $resp = new Response();
        
        $result = new ProfileResult($req, $resp, 100, 300);
        $this->assertSame($req, $result->request);
        $this->assertSame($resp, $result->response);
        $this->assertEquals(100, $result->time);
        $this->assertEquals(300, $result->memory);
    }
    
    public function testResultToString()
    {
        $req = m::mock(new Request());
        $req->shouldReceive("__toString")
            ->andReturn("Request");
        
        $resp = m::mock(new Response());
        $resp->shouldReceive("__toString")
             ->andReturn("Response");
        
        $result = new ProfileResult($req, $resp, 100, 300);
        $exepcted = "Request\n\nResponse\n\nProcess took 100 seconds and consumed 300 MB of system memory\n\n";
        $this->assertEquals($exepcted, (string)$result);
    }
}
