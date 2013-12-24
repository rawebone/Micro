<?php
namespace Micro\Tests\Fixtures;

use Micro\Request;
use Micro\Responder;
use Micro\HandlerInterface;

class ExampleHandler implements HandlerInterface
{
    public function conditions()
    {
        return array();
    }

    public function contentTypes()
    {
        return array("html");
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
}
