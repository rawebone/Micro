<?php
namespace Micro;

/**
 * A Controller represents an endpoint in the Application. 
 */
interface ControllerInterface
{
    /**
     * Returns the coded URI representing the end point. Conditions in this
     * URI should be returned by `conditions()`.
     * 
     * @see conditions()
     * @return string
     */
    function uri();
    
    /**
     * Returns the methods that the end point responds to.
     * 
     * @return array
     */
    function methods();
    
    /**
     * Returns whether the request is valid for the end point. Checks on 
     * Content Types, Languages and other HTTP Headers should be performed here.
     * 
     * @return boolean
     */
    function accepts(Request $req);
    
    /**
     * Returns an array of regular expressions that should be used in the 
     * URI to validate parameters.
     * 
     * @return array
     */
    function conditions();
    
    /**
     * Called to perform the required action of the end point when the Request
     * conditions are deemed valid for it. This should return a response
     * via the use of the Responder.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function handle(Request $req, Responder $resp);
}
