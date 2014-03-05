<?php
namespace Micro;

use Micro\Matching\MatcherInterface;
use Micro\Matching\RequestMatcher;
use Micro\Util\UrlTools;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The Dispatcher class provides the handling for routing calls from an
 * HTTP request to a Controller.
 * 
 * @property \Micro\Request $lastRequest The last Request run through dispatcher (read-only)
 * @property \Symfony\Component\HttpFoundation\Response $lastResponse The last Response returned by the dispatcher (read-only)
 * @property \Exception $lastException The last exception caught by the dispatcher (read-only)
 * @property \Micro\Matching\MatcherInterface $matcher The matcher in use by the application (read-only)
 * @property \Micro\Util\UrlTools $ut The UrlTools instance in use by the application (read-only)
 * @property \Psr\Log\LoggerInterface $tracer The current logger being used to trace internal activity. This can be set via tracer() (read-only)
 */
class Application implements TraceableInterface
{
    /**
     * Whether the dispatcher is being used in a development context.
     *
     * @var boolean
     */
    public $debugMode = false;
    
    /**
     * An array containing the Controllers registered in the application,
     * encapsulated by a RequestMatcher. Encapsulation allows for caching
     * of regex compilation so that repeat actions are faster.
     * 
     * @var \Micro\Util\RequestMatcher
     */
    protected $controllers = array();

    /**
     * The Controller that should be called when a URI cannot be matched.
     * 
     * @var \Micro\NotFoundControllerInterface.
     */
    protected $notFound;
    
    /**
     * Stores the names of properties which should be available in a read-only
     * context.
     *
     * @var array
     */
    protected $readOnly = array();
    
    /**
     * A PSR-3 Compliant Logger used for tracing a request through the 
     * application. This logger should be set via the `tracer()` method.
     *
     * @var \Psr\Log\LoggerInterface
     * @see tracer()
     */
    protected $tracer;
    
    /**
     * The object which should be used to determine whether the request
     * matches a controller.
     *
     * @var \Micro\Matching\MatcherInterface
     */
    protected $matcher;

    /**
     * A UrlTools object for working with requests.
     *
     * @var \Micro\Util\UrlTools
     */
    protected $ut;
    
    // Read-Only Properties
    protected $lastRequest;
    protected $lastResponse;
    protected $lastException;
    
    public function __construct(MatcherInterface $matcher = null, LoggerInterface $tracer = null, UrlTools $ut = null)
    {
        $this->readOnly = array("lastRequest", "lastResponse", "lastException", "ut", "matcher", "tracer");
        $this->tracer = $tracer ?: new NullLogger(); 
        $this->matcher = $matcher ?: new RequestMatcher($this->tracer);
        $this->ut = $ut ?: new UrlTools();
    }
    
    /**
     * Handles access to read-only variables.
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return (in_array($name, $this->readOnly) ? $this->$name : null);
    }
    
    /**
     * Sets the logger to be used to collect trace information and applies
     * it to the underlying application Controllers (where applicable).
     * 
     * @param \Psr\Log\LoggerInterface $log
     * @return void
     */
    public function tracer(LoggerInterface $log)
    {
        $this->tracer = $log;
        
        if ($this->matcher instanceof TraceableInterface) {
            $this->matcher->tracer($log);
        }
        
        foreach ($this->controllers as $controller) {
            if ($controller instanceof TraceableInterface) {
                $controller->tracer($log);
            }
        }
    }
    
    /**
     * Attaches a controller to the dispatcher. The controller provides the 
     * configuration for access via HTTP and is executed upon matching. If an
     * instance of the \Micro\NotFoundControllerInterface is passed, this will
     * be registered for invokation when a request is not fulfilled by normal
     * methods.
     * 
     * @param \Micro\ControllerInterface $controller
     * @return void
     */
    public function attach(ControllerInterface $controller)
    {
        if ($controller instanceof TraceableInterface) {
            $controller->tracer($this->tracer);
        }
        
        if ($controller instanceof NotFoundControllerInterface) {
            $this->notFound = $controller;
        } else {
            $this->controllers[] = $controller;
        }
    }

    /**
     * Dispatches a route based on the current request. Boolean false is returned
     * on error or no handler being found for the current request.
     * 
     * @param \Micro\Request $req Optional
     * @param \Micro\Responder $resp Optional
     * @return boolean
     */
    public function run(Request $req = null, Responder $resp = null)
    {
        $this->tracer->info("Beginning Request Cycle");
        
        $this->reset();
        list($req, $resp)  = $this->prepareRequestAndResponse($req, $resp);
        $this->lastRequest = $req;
        
        $controller = $this->findController($req);
        if ($controller instanceof NotFoundControllerInterface) {
            return $this->dispatch($req, $resp, $controller);
            
        } else if ($controller instanceof ControllerInterface) {
            $this->addParameters($controller, $req);
            return $this->dispatch($req, $resp, $controller);
            
        } else {
            return false;
        }
    }
    
    /**
     * Sends the response data associated with the last run to the client, if
     * applicable.
     * 
     * @return void
     */
    public function send()
    {
        if (!is_null($this->lastResponse)) {
            $this->lastResponse->send();
        }
    }
    
    /**
     * Prepares the Dispatchers state for a request.
     * 
     * @return void
     */
    protected function reset()
    {
        $this->lastRequest   = null;
        $this->lastResponse  = null;
        $this->lastException = null;
    }
    
    /**
     * Establishes the object instances that should be used for requests 
     * and responses.
     * 
     * @param \Micro\Request $req
     * @param \Micro\Responder $resp
     * @return array|\Micro\Request|\Micro\Response
     */
    protected function prepareRequestAndResponse(Request $req = null, Responder $resp = null)
    {
        return array(
            $req ?: Request::createFromGlobals(),
            $resp ?: new Responder()
        );
    }
    
    /**
     * Process a request on a handler, validating the return and handling errors.
     * 
     * @throws \Exception
     * @param \Micro\Request $req
     * @param \Micro\Responder $resp
     * @param \Micro\ControllerInterface $controller
     * @return boolean
     */
    protected function dispatch(Request $req, Responder $resp, ControllerInterface $controller)
    {
        try {
            if (!($return = $controller->handle($req, $resp)) instanceof Response) {
                throw new Exceptions\BadHandlerReturnException($controller);
            }
            $this->lastResponse = $return->prepare($req);
            $this->tracer->notice("Dispatch was successful");
            
        } catch (\Exception $e) {
            $this->tracer->critical("An error was encountered dispatching the request");
            $this->lastException = $e;
            $this->lastResponse  = false;
            
            if ($this->debugMode) { // Enables better testing
                throw $e;
            } 
        }
        return ($this->lastResponse !== false);
    }
    
    /**
     * Returns the appropriate Controller for the current Request.
     * 
     * @param \Micro\Request $req
     * @return \Micro\ControllerInterface|false
     */
    protected function findController(Request $req)
    {
        foreach ($this->controllers as $controller) {
            if ($this->matcher->match($req, $controller)) {
                $this->tracer->info("Found Controller " . get_class($controller));
                return $controller;
            }
        }
        
        $this->tracer->info("No matching Controller for {$req->getUri()} discovered, returning not found controller: " . ($this->notFound ? "yes" : "no"));
        return $this->notFound ?: false;
    }
    
    /**
     * Gathers custom parameters from a URL based on the conditions given by
     * the Controller.
     * 
     * @param \Micro\ControllerInterface $controller
     * @param \Micro\Request $request
     * @return void
     */
    protected function addParameters(ControllerInterface $controller, Request $request)
    {
        $regex = $this->ut->compile($controller->uri(), $controller->conditions());
        $params = $this->ut->parameters($regex, $request->getPathInfo());
        $request->request->add($params);
    }
}
