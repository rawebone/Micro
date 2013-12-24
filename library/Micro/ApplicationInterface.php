<?php
namespace Micro;

interface ApplicationInterface
{
    function attach(HandlerInterface $handler);
    function run(Request $req = null, Responder $resp = null);
    function lastRequest();
    function lastResponse();
    function lastException();
}
