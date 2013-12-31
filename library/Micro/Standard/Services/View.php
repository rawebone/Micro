<?php
namespace Micro\Standard\Services;

use Micro\Util\PhpViewEngine;
use Rawebone\ViewModel\ViewEngineInterface;
use Rawebone\ServiceProvider\AbstractService;

class View extends AbstractService
{
    public static function provideInitialInstance()
    {
        return new PhpViewEngine();
    }

    public static function validate($obj)
    {
        return ($obj instanceof ViewEngineInterface);
    }

    /**
     * @return \Rawebone\ViewModel\ViewEngineInterface
     */
    public static function get()
    {
        return parent::get();
    }
}
