<?php
namespace Micro\Standard\Services;

use Rawebone\ServiceProvider\AbstractService;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Log extends AbstractService
{
    public static function provideInitialInstance()
    {
        return new NullLogger();
    }

    public static function validate($obj)
    {
        return ($obj instanceof LoggerInterface);
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public static function get()
    {
        return parent::get();
    }
}
