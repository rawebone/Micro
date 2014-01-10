<?php
namespace Micro\Testing;

use Psr\Log\AbstractLogger;

/**
 * This logger stores the information recorded into memory so that it can be
 * interrogated by tracing.
 */
class CollectorLogger extends AbstractLogger
{
    protected $collected = array();
    
    public function log($level, $message, array $context = array())
    {
        $stamp = microtime(true);
        $this->collected[] = compact("level", "message", "stamp", "context");
    }
    
    /**
     * Allows for the collector to be used again for another run.
     * 
     * @return void
     */
    public function reset()
    {
        $this->collected = array();
    }
    
    /**
     * Returns the collected information; elements returned are:
     * 
     * * `level` - the impact of the event
     * * `message` - what happened
     * * `timestamp` - a floating-point value recording the time of the event
     * * `context` - an array containing any data relevant for the event
     * 
     * @return array
     */
    public function collected()
    {
        return $this->collected;
    }
}
