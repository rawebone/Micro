<?php
namespace Micro\Tests\Testing\Browsers;

use Micro\Application;
use Micro\Tests\TestCase;
use Micro\Testing\Browsers\TracingBrowser;

class TracingBrowserTest extends TestCase
{
    public function testRequestYieldsTrace()
    {
        $app = new Application();
        $browser = new TracingBrowser($app);
        
        $resp = $browser->get("/");
        
        $this->assertInstanceOf("Micro\\Testing\\TraceResult", $browser->lastTrace());
    }
}
