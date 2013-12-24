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
    
    protected function registerFacades()
    {
        $fr = $this->facadesRegistered;
        Facades\Log::facadeRegister($fr);
        $this->facadesRegistered = true;
    }
}
