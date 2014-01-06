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
        return new Client($this->getApplication());
    }
    
    /**
     * @return \Micro\ApplicationInterface
     */
    abstract protected function getApplication();
}
