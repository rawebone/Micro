<?php
namespace Micro\Standard;

use Micro\EnvironmentInterface;

/**
 * This provides a wrapper for the common functionality required by an 
 * application using the core services.
 */
class Environment implements EnvironmentInterface
{
    public function init()
    {
        Services\Log::init();
        Services\View::init();
    }
}
