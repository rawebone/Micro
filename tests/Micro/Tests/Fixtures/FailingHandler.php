<?php
namespace Micro\Tests\Fixtures;

class FailingHandler extends ExampleHandler
{
    public function handle(\Micro\Request $req, \Micro\Responder $resp)
    {
        return null;
    }
}
