<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/18
 * Time: 上午10:07
 */

namespace Environment;


use Simple\Environment\Loader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $path = __DIR__;
    protected $file = ".env";

    protected function setUp()
    {
        $_ENV['APP_ENV'] = "testing";
        $_ENV['APP_DEBUG'] = 'false';
    }

    public function testLoadWithImmutable()
    {
        $loader = new Loader($this->path . DIRECTORY_SEPARATOR . $this->file, false);
        $loader->load();
        $this->assertEquals("local", $_ENV['APP_ENV']);
        $this->assertEquals('true',    $_ENV['APP_DEBUG']);
        $this->validEnv();
    }

    public function testLoadWithNoImmutable()
    {
        $loader = new Loader($this->path . DIRECTORY_SEPARATOR . $this->file, true);
        $loader->load();
        $this->assertEquals("testing", $_ENV['APP_ENV']);
        $this->assertEquals('false', $_ENV['APP_DEBUG']);
        $this->validEnv();
    }

    protected function validEnv()
    {
        $this->assertEquals("this is test key", $_ENV['APP_KEY']);
        $this->assertEquals("127.0.0.1", $_ENV['DB_HOST']);
        $this->assertEquals(11211, $_ENV['CACHE_PORT']);
        $this->assertEquals("chinovex", $_ENV['DB_DATABASE']);
    }
}
