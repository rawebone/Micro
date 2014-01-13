<?php
namespace Micro\Tests\Testing\Browsers;

use Micro\Application;
use Micro\Tests\TestCase;
use Micro\Testing\Browsers\Browser;
use Micro\Tests\Fixtures\ExceptionThrowingControllerFixture;
use Micro\Tests\Fixtures\ValidControllerFixture;

class BrowserTest extends TestCase
{
    public function testBadRequest()
    {
        $browser = new Browser(new Application());
        
        $resp = $browser->get("/");
        
        $this->assertEquals(404, $resp->getStatusCode());
        $this->assertEquals("Not Found", $resp->getContent());
    }
    
    public function testExceptionalRequest()
    {
        $app = new Application();
        $app->attach(new ExceptionThrowingControllerFixture());
        $browser = new Browser($app);
        
        $resp = $browser->get("/");
        
        $this->assertEquals(503, $resp->getStatusCode());
    }
    
    public function testValidRequest()
    {
        $app = new Application();
        $app->attach(new ValidControllerFixture());
        $browser = new Browser($app);
        
        $resp = $browser->get("/");
        
        $this->assertEquals(200, $resp->getStatusCode());
    }
}
