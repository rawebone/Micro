<?php
namespace Micro\Util;

use \Symfony\Component\HttpFoundation\Request;

/**
 * This enables us to fire off requests to our application and verify the output
 * from within our test cases. This is built as a trait to enable it to fall
 * into any testing system; users need only give it a way to access
 * the application by implementing the getApplication() method.
 */
trait TestCase
{
    public function get($uri, array $headers = array())
    {
        return $this->browse("GET", $uri, $headers);
    }
    
    protected function browse($method, $uri, array $headers = array())
    {
        $req = Request::create($uri, strtoupper($method), $headers);
        return $this->getApplication()->run($req);
    }
    
    /**
     * @return \Micro\ApplicationInterface
     */
    abstract protected function getApplication();
}
