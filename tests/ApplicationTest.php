<?php
namespace Micro\Tests;

use Prophecy\Argument;
use Micro\Application;
use Micro\Request;

class ApplicationTest extends TestCase
{
    public function testDefaultDependencies()
    {
        $app = new Application();
        
        $this->assertInstanceOf("Psr\\Log\\LoggerInterface", $app->tracer);
        $this->assertInstanceOf("Micro\\Util\\UrlTools", $app->ut);
        $this->assertInstanceOf("Micro\\Matching\\MatcherInterface", $app->matcher);
    }
    
    public function testOverrideMatcher()
    {
        $matcher = $this->prophet->prophesize("Micro\\Matching\\MatcherInterface")->reveal();
        $app = new Application($matcher);
        
        $this->assertSame($matcher, $app->matcher);
    }
    
    public function testOverriderTracer()
    {
        $tracer = $this->prophet->prophesize("Psr\\Log\\LoggerInterface")->reveal();
        $app = new Application(null, $tracer);
        
        $this->assertSame($tracer, $app->tracer);
    }
    
    public function testOverrideUrlTools()
    {
        $ut = $this->prophet->prophesize("Micro\\Util\\UrlTools")->reveal();
        $app = new Application(null, null, $ut);
        
        $this->assertSame($ut, $app->ut);
    }
    
    public function testSetTracer()
    {
        $log = new \Psr\Log\NullLogger();
        $matcher = $this->prophet->prophesize("Micro\\Matching\\MatcherInterface");
        $matcher->willImplement("Micro\\TraceableInterface");
        $matcher->tracer($log)->shouldBeCalled();
        
        $controller = $this->prophet->prophesize("Micro\\ControllerInterface");
        $controller->willImplement("Micro\\TraceableInterface");
        $controller->tracer($log)->shouldBeCalled();
        
        $app = new Application($matcher->reveal());
        $app->attach($controller->reveal());
        
        $app->tracer($log);
        
        $this->assertSame($log, $app->tracer);
    }
    
    public function testRunWithNoControllersIsFalse()
    {
        $app = new Application();
        $this->assertEquals(false, $app->run());
    }
    
    public function testRunWithValidController()
    {
        $app = new Application();
        $app->attach(new Fixtures\ValidControllerFixture());
        
        $req = Request::create("/");
        $this->assertEquals(true, $app->run($req));
        
        $this->assertSame($req, $app->lastRequest);
        $this->assertInstanceOf("Symfony\\Component\\HttpFoundation\\Response", $app->lastResponse);
    }
    
    public function testRunWithExceptionalController()
    {
        $app = new Application();
        $app->attach(new Fixtures\ExceptionThrowingControllerFixture());
        
        $req = Request::create("/");
        $this->assertEquals(false, $app->run($req));
        $this->assertInstanceOf("\Exception", $app->lastException);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Test
     */
    public function testRunWithExceptionalControllerDebug()
    {
        $app = new Application();
        $app->debugMode = true;
        $app->attach(new Fixtures\ExceptionThrowingControllerFixture());
        
        $req = Request::create("/");
        $this->assertEquals(false, $app->run($req));
    }
    
    public function testRunWithExceptionalControllerAndErrorController()
    {
        $error = $this->getProphet()->prophesize('Micro\ErrorControllerInterface');
        $error->handle(
                Argument::type('Symfony\Component\HttpFoundation\Request'),
                Argument::type('Micro\Responder'),
                Argument::type('Exception')
        )->shouldBeCalled()->willReturn(100);
        
        $app = new Application();
        $app->attach(new Fixtures\ExceptionThrowingControllerFixture());
        $app->attach($error->reveal());
        
        $req = Request::create("/");
        $this->assertEquals(true, $app->run($req));
        $this->assertInstanceOf("\Exception", $app->lastException);
        $this->assertEquals(100, $app->lastResponse);
    }
    
    public function testRunWithBadControllerReturn()
    {
        $app = new Application();
        $app->attach(new Fixtures\BadResponseControllerFixture());
        
        $req = Request::create("/");
        $this->assertEquals(false, $app->run($req));
        $this->assertInstanceOf("Micro\\Exceptions\\BadHandlerReturnException", $app->lastException);
    }

    /**
     * @expectedException \Micro\Exceptions\BadHandlerReturnException
     */
    public function testNotFoundCalledOnBadRequest()
    {
        $notFound = $this->prophet->prophesize("Micro\\NotFoundControllerInterface");
        
        $app = new Application();
        $app->debugMode = true;
        $app->attach($notFound->reveal());
        
        $req = Request::create("/");
        $app->run($req);
    }
}
