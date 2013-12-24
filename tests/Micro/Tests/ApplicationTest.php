<?php
namespace Micro\Tests;

class ApplicationTest extends TestCase
{
    public function testExampleHandling()
    {
        $app = $this->getApp();
        $app->attach(new Fixtures\ExampleHandler);
        $this->assertEquals(true, $app->run($this->getReq()));
    }
    
    public function testFailedHandling()
    {
        $app = $this->getApp();
        $app->attach(new Fixtures\FailingHandler());
        $this->assertEquals(false, $app->run($this->getReq()));
        $this->assertInstanceOf("\\Micro\\Exceptions\\BadHandlerReturnException", $app->lastException());
        $this->assertInstanceOf("\\Micro\\Request", $app->lastRequest());
        $this->assertEquals(null, $app->lastResponse());
    }
    
    public function testExceptionHandling()
    {
        $app = $this->getApp();
        $app->attach(new Fixtures\ExceptionHandler());
        $this->assertEquals(false, $app->run($this->getReq()));
        $this->assertInstanceOf("\\Exception", $app->lastException());
        $this->assertInstanceOf("\\Micro\\Request", $app->lastRequest());
        $this->assertEquals(null, $app->lastResponse());
    }
}
