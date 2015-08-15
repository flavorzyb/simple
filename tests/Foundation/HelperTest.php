<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 上午9:30
 */

namespace Simple\Foundation;


class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function testEnvFunction()
    {
        $this->assertNull(env("DEBUG"));
        putenv("TESTING=1");
        $this->assertEquals(1, env('TESTING'));

        putenv("TESTING=true");
        $this->assertEquals(true, env('TESTING'));

        putenv("TESTING=false");
        $this->assertEquals(false, env('TESTING'));

        putenv("TESTING=null");
        $this->assertEquals(null, env('TESTING'));

        putenv("TESTING=empty");
        $this->assertEquals(null, env('TESTING'));
    }
}
