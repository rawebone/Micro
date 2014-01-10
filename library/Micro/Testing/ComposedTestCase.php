<?php
namespace Micro\Testing;

trait ComposedTestCase
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
