<?php
namespace Micro\Testing\Browsers;

use Micro\Application;
use Micro\Testing\ProfileResult;

class ProfilingBrowser extends Browser
{
    /**
     * The last profile of the application.
     *
     * @var \Micro\Testing\ProfileResult
     */
    protected $lastProfile;
    
       /**
     * The last profile through the application.
     * 
     * @return \Micro\Testing\ProfileResult
     */
    public function lastProfile()
    {
        return $this->lastProfile;
    }
    
    /**
     * We need to go deep with this one to get the metrics we need to ensure
     * the most accurate results. As such, this is an amended copy of the call
     * source in parent browser class.
     */
    protected function call($uri, $method, array $headers, $content = "")
    {
        $this->lastRequest = $this->lastResponse = $this->lastProfile = null;
        $this->lastRequest = $this->makeRequest($uri, $method, $headers, $content);
        
        $startTime = microtime(true);
        $startSMem = memory_get_peak_usage(true);
        
        $this->application->run($this->lastRequest);
        
        $endSMem = memory_get_peak_usage(true) - $startSMem;
        $endTime = microtime(true) - $startTime;

        $this->prepareResponse();
        
        $this->lastProfile = new ProfileResult($this->lastRequest, $this->lastResponse, $endTime, $endSMem);
        return $this->lastResponse;
    }
}
