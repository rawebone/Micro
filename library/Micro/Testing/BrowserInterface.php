<?php
namespace Micro\Testing;

use Micro\Application;

/**
 * The BrowserInterface provides a mechanism for interacting with a Micro
 * Application through a clean API largely based off of Kris Wallsmiths' Buzz.
 * 
 * It's important to note, given that this works over the top of the Symfony
 * HttpFoundation, that passed headers must be in the PHP format, i.e.
 * Content-Type becomes CONTENT_TYPE etc, Accepts becomes HTTP_ACCEPTS.
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
     * @return \Symfony\Component\HttpFoundation\Response|false
     */
    function get($uri, array $headers = array());
    
    /**
     * Performs a POST request against the application with the specified URI,
     * 
     * @return \Symfony\Component\HttpFoundation\Response|false
     */
    function post($uri, array $headers = array(), $content = "");
    
    /**
     * Performs a HEAD request against the application with the specified URI.
     * 
     * @return \Symfony\Component\HttpFoundation\Response|false
     */
    function head($uri, array $headers = array());
    
    /**
     * Performs a PATCH request against the application with the specified URI.
     * 
     * @return \Symfony\Component\HttpFoundation\Response|false
     */
    function patch($uri, array $headers = array(), $content = "");
    
    /**
     * Performs a PUT request against the application with the specified URI.
     * 
     * @return \Symfony\Component\HttpFoundation\Response|false
     */
    function put($uri, array $headers = array(), $content = "");
    
    /**
     * Performs a DELETE request against the application with the specified URI.
     * 
     * @return \Symfony\Component\HttpFoundation\Response|false
     */
    function delete($uri, array $headers = array(), $content = "");
    
    /**
     * Performs an OPTIONS request against the application with the specified URI.
     * 
     * @return \Symfony\Component\HttpFoundation\Response|false
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
