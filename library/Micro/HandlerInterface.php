<?php
namespace Micro;

interface HandlerInterface
{
    function uri();
    function methods();
    function contentTypes();
    function accept();
    function conditions();
    function handle(Request $req, Responder $resp);
}
