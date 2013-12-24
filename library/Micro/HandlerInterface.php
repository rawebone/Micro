<?php
namespace Micro;

interface HandlerInterface
{
    function uri();
    function methods();
    function contentTypes();
    function conditions();
    function handle(Request $req, Responder $resp);
}
