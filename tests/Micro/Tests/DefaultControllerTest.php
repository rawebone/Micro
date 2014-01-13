<?php
namespace Micro\Tests;

use Micro\Tests\Fixtures\InvalidUriDefaultControllerFixture;
use Micro\Tests\Fixtures\ValidDefaultControllerFixture;

class DefaultControllerTest extends TestCase
{
    /**
     * @expectedException \Micro\Exceptions\MisconfiguredControllerException
     */
    public function testNoConfiguredUriCausesException()
    {
        new InvalidUriDefaultControllerFixture();
    }
    
    public function testConfiguredController()
    {
        $controller = new ValidDefaultControllerFixture();
        $this->assertEquals("/", $controller->uri());
        $this->assertEquals(array("GET"), $controller->methods());
        $this->assertEquals(array("name" => "\w+"), $controller->conditions());
    }
}
