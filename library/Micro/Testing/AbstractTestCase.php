<?php
namespace Micro\Testing;

/**
 * This is less preferrable to the ComposedTestCase
 */
abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Micro\Testing\Client
     */
    protected function getClient()
    {
        $app = $this->getApplication();
        $app->debugMode = true;
        return new Client($app);
    }
    
    /**
     * @return \Micro\Application
     */
    abstract protected function getApplication();
}
