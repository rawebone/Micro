<?php
namespace Micro\Matching;

use Micro\Util\UrlTools;

class UrlMatcher implements MatcherInterface
{
    /**
     * A UrlTools instance for use in matching.
     *
     * @var \Micro\Util\UrlTools
     */
    protected $ut;
    
    public function __construct(UrlTools $ut)
    {
        $this->ut = $ut;
    }
    
    public function match(\Micro\Request $request, \Micro\ControllerInterface $controller)
    {
        $ut = $this->ut;
        $regex = $ut->compile($controller->uri(), $controller->conditions());
        
        return $ut->match($regex, $request->getPathInfo());
    }

    public function name()
    {
        return "URI";
    }
}
