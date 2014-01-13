<?php
namespace Micro\Tests\Testing\Browsers;

use Micro\Application;
use Micro\Tests\TestCase;
use Micro\Testing\Browsers\ProfilingBrowser;

class ProfilingBrowserTest extends TestCase
{
    public function testRequestYieldsProfile()
    {
        $app = new Application();
        $browser = new ProfilingBrowser($app);
        
        $resp = $browser->get("/");
        
        $this->assertInstanceOf("Micro\\Testing\\ProfileResult", $browser->lastProfile());
    }
}
