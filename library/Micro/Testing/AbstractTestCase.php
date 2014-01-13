<?php
namespace Micro\Testing;

/**
 * This is less preferrable to the ComposedTestCase
 */
abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Micro\Testing\Browsers\Browser
     */
    protected function getBrowser($debug = false)
    {
        $app = $this->getApplication();
        $app->debugMode = $debug;
        return new Browsers\Browser($app);
    }
    
    /**
     * @return \Micro\Testing\Browsers\TracingBrowser
     */
    protected function getTracer($debug = false)
    {
        $app = $this->getApplication();
        $app->debugMode = $debug;
        return new Browsers\TracingBrowser($app);
    }
    
    /**
     * @return \Micro\Testing\Browsers\ProfilingBrowser
     */
    protected function getProfiler($debug = false)
    {
        $app = $this->getApplication();
        $app->debugMode = $debug;
        return new Browsers\ProfilingBrowser($app);
    }
    
    /**
     * @return \Micro\Application
     */
    abstract protected function getApplication();
}
