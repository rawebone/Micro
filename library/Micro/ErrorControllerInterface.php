<?php
namespace Micro;

interface ErrorControllerInterface extends ControllerInterface
{
    function handle(Request $req, Responder $resp, \Exception $ex = null);
}
