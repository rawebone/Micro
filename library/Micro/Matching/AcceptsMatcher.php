<?php
namespace Micro\Matching;

class AcceptsMatcher implements MatcherInterface
{
    public function match(\Micro\Request $request, \Micro\ControllerInterface $controller)
    {
        return $controller->accepts($request);
    }

    public function name()
    {
        return "Controller Acceptance";
    }
}
