<?php
namespace Micro\Tests;

use Prophecy\Prophet;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * The prophecy instance to be used for mocking purposes.
     *
     * @var \Prophecy\Prophet
     */
    protected $prophet;
    
    public function setUp()
    {
        $this->prophet = $this->getProphet();
    }
    
    public function tearDown()
    {
        $this->prophet->checkPredictions();
    }
    
    protected function getProphet()
    {
        return new Prophet();
    }
}
