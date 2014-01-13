<?php
namespace Micro\Matching;

use Micro\Request;
use Micro\Util\UrlTools;
use Micro\ControllerInterface;
use Micro\TraceableInterface;
use Psr\Log\LoggerInterface;

/**
 * The RequestMatcher validates the core components of an HTTP request against
 * a Controller instance.
 */
class RequestMatcher implements TraceableInterface, MatcherInterface
{
    /**
     * The logger to be used to collect trace information.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $tracer;
    
    /**
     * The matchers that should be used to verify whether a controller meets
     * the request.
     *
     * @var \Micro\Matching\MatcherInterface
     */
    protected $matchers = array();
    
    public function __construct(LoggerInterface $tracer)
    {
        $this->tracer = $tracer;
        $this->matchers = $this->getMatchers();
    }
    
    /**
     * Performs the match against the Controller.
     * 
     * @param \Micro\Request $request
     * @param \Micro\ControllerInterface $controller
     * @return boolean
     */
    public function match(Request $request, ControllerInterface $controller)
    {
        foreach ($this->matchers as $matcher) {
            if (!$matcher->match($request, $controller)) {
                $this->tracer->notice("Matching failed on {$matcher->name()} check");
                return false;
            }
        }
        return true;
    }
    
    public function name()
    {
        return __CLASS__;
    }
    
    /**
     * Sets the tracer to be used.
     * 
     * @param \Psr\Log\LoggerInterface $log
     * @return void
     */
    public function tracer(LoggerInterface $log)
    {
        $this->tracer = $log;
    }
    
    /**
     * Returns the matchers that should be called, in the order they should be
     * called.
     * 
     * @return array|\Micro\Matching\MatcherInterface
     */
    protected function getMatchers()
    {
        return array(
            new MethodMatcher(),
            new UrlMatcher(new UrlTools()),
            new AcceptsMatcher()
        );
    }
}
