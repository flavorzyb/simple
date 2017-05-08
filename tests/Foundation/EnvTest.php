<?php
namespace Foundation;

use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testEnv()
    {
        putenv("DEBUG=true");
        $this->assertTrue(env('DEBUG'));

        putenv("DEBUG=(true)");
        $this->assertTrue(env('DEBUG'));

        putenv("DEBUG=false");
        $this->assertFalse(env('DEBUG'));

        putenv("DEBUG=(false)");
        $this->assertFalse(env('DEBUG'));


        putenv("DEBUG=");
        $this->assertTrue(empty(env('DEBUG')));

        putenv("DEBUG=null");
        $this->assertNull(env('DEBUG'));

        putenv("DEBUG=23");
        $this->assertEquals(23, env('DEBUG'));
    }
}
