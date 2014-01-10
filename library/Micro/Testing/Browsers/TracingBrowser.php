<?php
namespace Micro\Testing\Browsers;

use Micro\Application;
use Micro\Testing\CollectorLogger;
use Micro\Testing\TraceResult;
use Psr\Log\NullLogger;

/**
 * The TracingBrowser wraps calls made to the application allowing for debug
 * information to be collected. This information can then be inspected by
 * calling `lastTrace()`.
 * 
 * Tracing is more expensive and so should only be used for development purposes.
 */
class TracingBrowser extends Browser
{
    /**
     * The tracer used for collecting the results.
     *
     * @var \Micro\Testing\CollectorLogger
     */
    protected $tracer;
    
    /**
     * The last trace through the system.
     *
     * @var \Micro\Testing\TraceResult
     */
    protected $lastTrace;
    
    public function __construct(Application $application)
    {
        $this->tracer = new CollectorLogger();
        parent::__construct($application);
    }
    
    /**
     * The last trace through the system.
     * 
     * @return \Micro\Testing\TraceResult
     */
    public function lastTrace()
    {
        return $this->lastTrace;
    }
    
    protected function call($uri, $method, array $headers, $content = "")
    {
        $this->tracer->info("Starting Trace");
        $this->application->tracer($this->tracer);
        $result = parent::call($uri, $method, $headers, $content);
        $this->application->tracer(new NullLogger());
        
        $this->tracer->info("Trace Completed");
        $this->lastTrace = new TraceResult($this->lastRequest, $this->lastResponse, $this->tracer->collected());
        $this->tracer->reset();
        
        return $result;
    }
}
