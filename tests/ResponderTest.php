<?php
namespace Micro\Tests;

use Micro\Responder;

class ResponderTest extends TestCase
{
    public function testStandard()
    {
        $this->assertInstanceOf(
                $this->getClassName("Response"), 
                $this->getResponder()->standard()
        );
    }
    
    public function testJson()
    {
        $this->assertInstanceOf(
                $this->getClassName("JsonResponse"), 
                $this->getResponder()->json("blah")
        );
    }
    
    public function testRedirect()
    {
        $this->assertInstanceOf(
                $this->getClassName("RedirectResponse"), 
                $this->getResponder()->redirect("blah")
        );
    }
    
    public function testFile()
    {
        $this->assertInstanceOf(
                $this->getClassName("BinaryFileResponse"), 
                $this->getResponder()->file(__FILE__)
        );
    }
    
    public function testStreamed()
    {
        $this->assertInstanceOf(
                $this->getClassName("StreamedResponse"), 
                $this->getResponder()->streamed()
        );
    }
    
    protected function getResponder()
    {
        return new Responder();
    }
    
    protected function getClassName($class)
    {
        return "\\Symfony\\Component\\HttpFoundation\\$class";
    }
}
