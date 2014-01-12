<?php
namespace Micro;

use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The Dispatcher class provides the handling for routing calls from an
 * HTTP request to a Controller.
 * 
 * @property \Micro\EnvironmentInterface $environment The Environment instance attached to the dispatcher (read-only)
 * @property \Micro\Request $lastRequest The last Request run through dispatcher (read-only)
 * @property \Symfony\Component\HttpFoundation\Response $lastResponse The last Response returned by the dispatcher (read-only)
 * @property \Exception $lastException The last exception caught by the dispatcher (read-only)
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
     * The Controller that should be called when a URI cannot be matched,
     * encapsulated by a RequestMatcher. Encapsulation allows for caching
     * of regex compilation so that repeat actions are faster.
     * 
     * @var \Micro\Util\RequestMatcher
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
    
    // Read-Only Properties
    protected $environment;
    protected $lastRequest;
    protected $lastResponse;
    protected $lastException;
    
    public function __construct(EnvironmentInterface $env)
    {
        $this->environment = $env;
        $this->readOnly = array("environment", "lastRequest", "lastResponse", "lastException");
        $this->tracer = new NullLogger(); 
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
     * it to the underlying application Controllers.
     * 
     * @param \Psr\Log\LoggerInterface $log
     * @return void
     */
    public function tracer(LoggerInterface $log)
    {
        $this->tracer = $log;
        foreach ($this->controllers as $controller) {
            if ($controller instanceof TraceableInterface) {
                $controller->tracer($log);
            }
            if ($controller->controller instanceof TraceableInterface) {
                $controller->controller->tracer($log);
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
        $matcher = new Util\RequestMatcher($controller);
        $matcher->tracer($this->tracer);
        
        if ($controller instanceof TraceableInterface) {
            $controller->tracer($this->tracer);
        }
        
        if ($controller instanceof NotFoundControllerInterface) {
            $this->notFound = $matcher;
        } else {
            $this->controllers[] = $matcher;
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
        if ($controller) {
            $req->request->add($controller->params($req));
            return $this->dispatch($req, $resp, $controller->controller);
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
            
            if ($this->debugMode) {
                throw $e;
            } 
        }
        return ($this->lastResponse !== false);
    }
    
    /**
    1 * Returns the appropriate Controller for the current Request.
     * 
     * @param \Micro\Request $req
     * @return \Micro\Util\RequestMatcher|false
     */
    protected function findController(Request $req)
    {
        foreach ($this->controllers as $controller) {
            if ($controller->matches($req)) {
                $this->tracer->info("Found Controller {$controller->name}");
                return $controller;
            }
        }
        
        $this->tracer->info("No matching end point for {$req->getUri()} discovered, returning not found controller: " . ($this->notFound ? "yes" : "no"));
        return $this->notFound ?: false;
    }
}
