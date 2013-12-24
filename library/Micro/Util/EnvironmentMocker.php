<?php
namespace Micro\Util;

/**
 * Used for testing.
 */
class EnvironmentMocker implements \Micro\EnvironmentInterface
{
    protected $facadesRegistered = false;
    
    public function init()
    {
        $this->registerFacades();
    }

    public function setLog(\Psr\Log\LoggerInterface $log)
    {
        \Micro\Facades\Log::facadeSwapMock($log);
    }
    
        
    public function setView(\Rawebone\ViewModel\ViewEngineInterface $engine)
    {
        \Micro\Facades\View::facadeSwap($engine);
    }
    
    protected function registerFacades()
    {
        $fr = $this->facadesRegistered;
        \Micro\Facades\Log::facadeRegister($fr);
        \Micro\Facades\View::facadeRegister($fr);
        $this->facadesRegistered = true;
    }
}
