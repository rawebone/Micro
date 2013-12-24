<?php
namespace Micro\Facades;

use Rawebone\Facade\FacadableInterface;
use Rawebone\Facade\AbstractFacade;

class View extends AbstractFacade implements FacadableInterface
{
    public static function facadeInitialInstance()
    {
        return new \Micro\Util\PhpViewEngine();
    }

    public static function facadeAlias()
    {
        return "View";
    }

    public static function facadeValidateSwap($inst)
    {
        if (!$inst instanceof \Rawebone\ViewModel\ViewEngineInterface) {
            static::facadeInvalidate("Not a valid view engine");
        }
    }
}
