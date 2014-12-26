# Micro

[![Build Status](https://travis-ci.org/rawebone/Micro.png?branch=master)](https://travis-ci.org/rawebone/Micro)

Micro is a web framework designed for professionals needing to quick build
small, reliable applications. Its goals are to:

* Be small enough so that it doesn't get in your way or force major architectural
  decisions about your application (i.e. the framework is not bigger than the picture)
* Provide the ability to fully test and profile the application layer with ease
* Provide the ability to debug the request process to see exactly why an error is occurring

> Please be aware that I am lowering my support for this project in favor of
> [Wilson](https://github.com/rawebone/Wilson) which is in part based off of this
> project but is more performant and has an overall better design. If anyone
> is interested in taking joint ownership of the project then feel free to get in
> contact, or send in PR's and I'll merge.

## Usage

The basic usage of the application is:

```php
<?php

require_once "vendor/autoload.php";

$app = new \Micro\Application();
$app->attach(\My\Controllers\Controller());
$app->run(); // Runs the application
$app->send(); // Sends the result back to the browser

```

The attached Controller must implement the `Micro\ControllerInterface`; the 
recommended approach to building controllers is via extension of the 
`Micro\DefaultController` class:

```php
<?php
namespace My\Controllers;

use Micro\Request;
use Micro\Responder;
use Micro\DefaultController;

class Controller extends DefaultController
{
    protected function configure()
    {
        $this->setUri("/hello/{name}")
             ->addMethod("GET")
             ->addCondition("name", "\w+");
    }

    public function accepts(Request $req)
    {
        // You can optionally override this method if you want to check any
        // details about the request; this can help filter out an invalid
        // request outside of handling it directly, like:
        
        return !$req->isAjax();
    }

    public function handle(Request $req, Responder $resp)
    {
        return $resp->standard("Hello, {$req->get("name")}");
    }
}

```

### Handling Errors and Bad Requests

There are two special controller interfaces which can affect the handling of
your application - `Micro\ErrorControllerInterface` and `Micro\NotFoundControllerInterface`.
You can implement these interfaces on an object and attach them, as above, to
the application instance. 

In the case that an Exception is encountered, the Error Controller will be invoked
and as expected to return a Response object. Similarly if a Controller for the
Request is not found, the Not Found Controller will be invoked to provide a
response for the Client.

Micro will not try and handle any errors or 404's beyond this provisioning.


### Testing

Micro contains a base test case for use in conjunction with PHPUnit that allows
you to run requests in your tests; you can do this by:

```php
<?php
namespace My\Tests\Functional;

// You may want to create a custom case for your project.
// If using PHP>=5.4 you can use the trait Micro\Testing\ComposableTestCase.
use Micro\Testing\AbstractTestCase;

class MyControllerTest extends AbstractTestCase
{
    protected static $app;

    protected function getApplication()
    {
        if (!self::$app) {
            self::$app = require_once "/path/to/app_bootstrap.php";
        }

        return self::$app;
    }

    public function testRequest()
    {
        $resp = $this->getBrowser()->get("/hello/barry");
        
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("Hello, barry", $resp->getContent());
    }
}

```

Micro ships with a concept called Browsers. These are classes which mimic requests
made against your application and return the results which allows you to test
your product in a simple, automated fashion. There are three browsers:

#### Standard Browser

The standard Browser is the root of the browser stack, and can be used to make
basic requests against your application.

#### Tracing Browser

Micro has the ability to trace requests as they progress through the application;
this exists to help you get to the route of a problem quickly. After every request
made by this browser, a `Micro\Testing\TraceResult` will be available by calling
`$browser->lastTrace()`. This can be printed straight to output or interrogated.

#### Profiling Browser

This provides the ability to profile a request for execution time and memory
usage- this is, primarily, for use to help measure and improve the performance
of the framework but can be useful to end users as well. It is important to
note that the execution time/memory usage of the controllers is not the only
data captured.

#### Tracing Your Controller Behaviour

As explained in the Tracing Browser section, requests can be traced 
through the system and this functionality is optionally available to controllers.

By implementing the `Micro\TraceableInterface`, you can receive a PSR-3 logger
to capture any debug information:

```php
namespace My\Controllers;

use Micro\Request;
use Micro\Responder;
use Micro\DefaultController;
use Micro\TraceableInterface;
use Psr\Log\LoggerInterface;

class Controller extends DefaultController implements TraceableInterface
{
    protected $tracer;

    // ...
    
    public function tracer(LoggerInterface $log)
    {
        $this->tracer = $log;
    }

    public function handle(Request $req, Responder $resp)
    {
        $this->tracer->critical("Help!");
    }
}

```

### General Approach

Micro, as I hope you will have seen from the above, is designed to be as simple
and testable as possible however, in the case you need to change frameworks
later on down the line, the suggestion is that the Controllers do the least
amount of work possible. For example:

```php
namespace My\Controllers;

// ...

class Controller extends DefaultController
{
    public function handle(Request $req, Responder $resp)
    {
        if ($this->myApplication()->doWork($settingA, $settingB)) {
            // Handle success
        } else {
            // Handle error
        }
    }
}

```

Is preferrable to:

```php
namespace My\Controllers;

// ...

class Controller extends DefaultController
{
    public function handle(Request $req, Responder $resp)
    {
        if ($this->doWork($settingA, $settingB)) {
            // Handle success
        } else {
            // Handle error
        }
    }

    protected function doWork($a, $b)
    {
        // Complicated API process
    }
}

```

For two reasons:

1. This allows you to separate out your business logic from your web logic; 
   the two can then change independently
2. You are then only testing for correctness of input/output from the web,
   and not the overall function of your application

This has the additional payoff of allowing you to change systems down the line
without major rewrites.

## License

[MIT License](LICENSE), go hog wild.
