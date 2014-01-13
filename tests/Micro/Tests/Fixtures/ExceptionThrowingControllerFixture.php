<?php
namespace Micro\Tests\Fixtures;

class ExceptionThrowingControllerFixture extends ValidControllerFixture
{
    public function handle(\Micro\Request $req, \Micro\Responder $resp)
    {
        throw new \Exception("Test");
    }
}