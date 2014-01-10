<?php
namespace Micro;

use Psr\Log\LoggerInterface;

/**
 * Classes implementing this interface possess the ability to log their 
 * operation for debugging purposes.
 */
interface TraceableInterface
{
    /**
     * Sets the instance of the logger that should be used to trace operations.
     * 
     * @return void
     */
    function tracer(LoggerInterface $log);
}
