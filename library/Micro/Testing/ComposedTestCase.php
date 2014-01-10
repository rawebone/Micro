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
     * @return \Micro\Application
     */
    abstract protected function getApplication();
}
