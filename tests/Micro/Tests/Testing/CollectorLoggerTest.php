<?php
namespace Micro\Tests\Testing;

use Micro\Testing\CollectorLogger;
use Micro\Tests\TestCase;

class CollectorLoggerTest extends TestCase
{
    public function testCollection()
    {
        $log = new CollectorLogger();
        $this->assertCount(0, $log->collected());
        
        $log->alert("This is a message");
        $this->assertCount(1, $log->collected());
        
        $log->reset();
        $this->assertCount(0, $log->collected());
    }
    
    public function testCollectedInfoStructure()
    {
        $log = new CollectorLogger();
        $log->alert("This is a message");
        
        $col = $log->collected();
        $this->assertEquals(true, is_array($col[0]));
        
        $this->assertArrayHasKey("level", $col[0]);
        $this->assertArrayHasKey("message", $col[0]);
        $this->assertArrayHasKey("timestamp", $col[0]);
        $this->assertArrayHasKey("context", $col[0]);
    }
    
    public function testCollectedInfoData()
    {
        $log = new CollectorLogger();
        $log->alert("This is a message");
        
        $col = $log->collected();
        
        $this->assertEquals("alert", $col[0]["level"]);
        $this->assertEquals("This is a message", $col[0]["message"]);
        $this->assertEquals(true, is_float($col[0]["timestamp"]));
        $this->assertEquals(true, is_array($col[0]["context"]));
    }
}
