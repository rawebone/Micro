<?php
namespace Micro\Matching;

use Micro\Request;
use Micro\ControllerInterface;

/**
 * A Matcher is used to perform a small unit of work in the request matching
 * process in order to improve testability.
 */
interface MatcherInterface
{
    /**
     * The name of the Matcher that should be used for tracing purposes.
     * 
     * @return string
     */
    function name();
    
    /**
     * Whether the Request meets a requirement of the Controller.
     * 
     * @return boolean
     */
    function match(Request $request, ControllerInterface $controller);
}
