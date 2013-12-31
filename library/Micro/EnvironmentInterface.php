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
}
