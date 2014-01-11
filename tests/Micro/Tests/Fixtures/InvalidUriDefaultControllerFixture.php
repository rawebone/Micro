<?php
namespace Micro\Tests\Fixtures;

use Micro\DefaultController;

class InvalidUriDefaultControllerFixture extends DefaultController
{
    public function handle(\Micro\Request $req, \Micro\Responder $resp)
    {
        // noop
    }

    protected function configure()
    {
        // noop
    }
}
