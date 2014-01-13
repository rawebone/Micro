<?php
namespace Micro\Tests\Fixtures;

use Micro\Request;
use Micro\Responder;
use Micro\ControllerInterface;

class ValidControllerFixture implements ControllerInterface
{
    public function conditions()
    {
        return array();
    }

    public function handle(Request $req, Responder $resp)
    {
        return $resp->standard("Done");
    }

    public function methods()
    {
        return array("GET");
    }

    public function uri()
    {
        return "/";
    }
    
    public function accepts(Request $req)
    {
        return true;
    }
}
