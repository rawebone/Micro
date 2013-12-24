<?php
namespace Micro\Tests;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Micro\Application
     */
    protected function getApp()
    {
        return new \Micro\Application(new \Micro\Util\EnvironmentMocker());
    }
    
    /**
     * @return \Micro\Request
     */
    protected function getReq()
    {
        return \Micro\Request::create(
                "/", 
                "GET", 
                array(),
                array(),
                array(),
                array("CONTENT_TYPE" => "text/html")
        );
    }
}
