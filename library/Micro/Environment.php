<?php
namespace Micro;

class Environment implements EnvironmentInterface
{
    protected $facadesRegistered = false;
    
    public function init()
    {
        $this->registerFacades();
    }
    
    public function setLog(\Psr\Log\LoggerInterface $log)
    {
        Facades\Log::facadeSwap($log);
    }
    
    public function setView(\Rawebone\ViewModel\ViewEngineInterface $engine)
    {
        Facades\View::facadeSwap($engine);
    }
    
    protected function registerFacades()
    {
        $fr = $this->facadesRegistered;
        Facades\Log::facadeRegister($fr);
        Facades\View::facadeRegister($fr);
        $this->facadesRegistered = true;
    }
}
