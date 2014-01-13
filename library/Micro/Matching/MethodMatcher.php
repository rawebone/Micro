<?php
namespace Micro\Matching;

class MethodMatcher implements MatcherInterface
{
    public function match(\Micro\Request $request, \Micro\ControllerInterface $controller)
    {
        return in_array($request->getMethod(), $controller->methods());
    }

    public function name()
    {
        return "HTTP Method";
    }
}
