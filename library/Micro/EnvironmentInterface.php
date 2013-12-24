<?php
namespace Micro;

interface EnvironmentInterface
{
    /**
     * Registers the facades required.
     * 
     * @return void
     */
    function init();
    
    /**
     * Sets the instance of the logger that should be used (default is NullLogger).
     */
    function setLog(\Psr\Log\LoggerInterface $log);
}
