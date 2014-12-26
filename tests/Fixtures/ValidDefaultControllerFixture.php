<?php
namespace Micro\Tests\Fixtures;

use Micro\DefaultController;

class ValidDefaultControllerFixture extends DefaultController
{
    public function handle(\Micro\Request $req, \Micro\Responder $resp)
    {
        // noop
    }

    protected function configure()
    {
        $this->setUri("/")
             ->addCondition("name", "\w+")
             ->addMethod("GET");
    }
}
