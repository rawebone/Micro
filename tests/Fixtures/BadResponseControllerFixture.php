<?php
namespace Micro\Tests\Fixtures;

class BadResponseControllerFixture extends ValidControllerFixture
{
    public function handle(\Micro\Request $req, \Micro\Responder $resp)
    {
        return null;
    }
}
