<?php
namespace Micro\Testing;

use Micro\Application;

/**
 * The BrowserInterface provides a mechanism for interacting with a Micro
 * Application through a clean API largely based off of Kris Wallsmiths Buzz.
 */
interface BrowserInterface
{
    /**
     * Creates a new instance of the Browser encapsulating an Application.
     */
    function __construct(Application $application);
    
    /**
     * Performs a GET request against the application with the specified URI,
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function get($uri, array $headers = array());
    
    /**
     * Performs a POST request against the application with the specified URI,
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function post($uri, array $headers = array(), $content = "");
    
    /**
     * Performs a HEAD request against the application with the specified URI.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function head($uri, array $headers = array());
    
    /**
     * Performs a PATCH request against the application with the specified URI.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function patch($uri, array $headers = array(), $content = "");
    
    /**
     * Performs a PUT request against the application with the specified URI.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function put($uri, array $headers = array(), $content = "");
    
    /**
     * Performs a DELETE request against the application with the specified URI.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function delete($uri, array $headers = array(), $content = "");
    
    /**
     * Performs an OPTIONS request against the application with the specified URI.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function options($uri, array $headers = array(), $content = "");
    
    /**
     * Returns the last request run through the system.
     * 
     * @return \Micro\Request|null
     */
    function lastRequest();
    
    /**
     * Returns the last response from the system.
     * 
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    function lastResponse();
}
