<?php
namespace Micro\Testing;

trait ComposedTestCase
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
