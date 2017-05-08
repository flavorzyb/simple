<?php
namespace Environment;

use PHPUnit\Framework\TestCase;
use Simple\Environment\Loader;

class LoaderTest extends TestCase
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
        $_SERVER['DB_HOST_TEST'] = '127.0.0.1';
        $loader = new Loader($this->path . DIRECTORY_SEPARATOR . $this->file, false);
        $loader->load();
        $this->assertEquals("local", $_ENV['APP_ENV']);
        $this->assertEquals('true',    $_ENV['APP_DEBUG']);
        $this->validEnv();
        $this->assertEquals("127.0.0.1", $loader->getEnvironmentVariable('DB_HOST_TEST'));
    }

    public function testLoadWithNoImmutable()
    {
        $loader = new Loader($this->path . DIRECTORY_SEPARATOR . $this->file, true);
        $loader->load();
        $this->assertEquals("testing", $_ENV['APP_ENV']);
        $this->assertEquals('false', $_ENV['APP_DEBUG']);
        $this->validEnv();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadWithException()
    {
        $loader = new Loader("error_file.env", true);
        $loader->load();
    }

    protected function validEnv()
    {
        $this->assertEquals("this is test key", $_ENV['APP_KEY']);
        $this->assertEquals("127.0.0.1", $_ENV['DB_HOST']);
        $this->assertEquals(11211, $_ENV['CACHE_PORT']);
        $this->assertEquals("chinovex", $_ENV['DB_DATABASE']);
    }
}
