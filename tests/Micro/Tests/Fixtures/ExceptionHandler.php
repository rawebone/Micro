<?php
namespace Micro\Tests\Fixtures;

class ExceptionHandler extends ExampleHandler
{
    public function handle(\Micro\Request $req, \Micro\Responder $resp)
    {
        throw new \Exception("Test");
    }
}