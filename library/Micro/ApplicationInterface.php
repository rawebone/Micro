<?php
namespace Micro;

interface ApplicationInterface
{
    function __construct(EnvironmentInterface $env);
    function attach(HandlerInterface $handler);
    function environment();
    function lastRequest();
    function lastResponse();
    function lastException();
    function run(Request $req = null, Responder $resp = null);
}
