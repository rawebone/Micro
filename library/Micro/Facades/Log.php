<?php
namespace Micro\Facades;

use Rawebone\Facade\FacadableInterface;
use Rawebone\Facade\AbstractFacade;

class Log extends AbstractFacade implements FacadableInterface
{
    public static function facadeInitialInstance()
    {
        return new \Psr\Log\NullLogger();
    }

    public static function facadeAlias()
    {
        return "Log";
    }

    public static function facadeValidateSwap($inst)
    {
        if (!$inst instanceof \Psr\Log\LoggerInterface) {
            static::facadeInvalidate("Not a valid logger");
        }
    }
}
