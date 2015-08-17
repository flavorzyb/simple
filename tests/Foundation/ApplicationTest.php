<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 上午12:28
 */

namespace Simple\Foundation;

use Simple\Config\Repository;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $app        = null;
    private $basePath   = null;

    protected function setUp()
    {
        $this->basePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
        $this->app  = new Application($this->basePath);
    }

    protected function tearDown()
    {
        $this->app = null;
    }


    public function testBootstrapThrowException()
    {
        $this->app  = new Application("error_path");
        $this->setExpectedException('Simple\Foundation\Exception');
        $this->app->bootStrap();
    }

    public function testPathIsMutable()
    {
        $this->assertEquals($this->basePath, $this->app->getBasePath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'config/app.php', $this->app->configPath());
        $this->assertNull($this->app->getConfig());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'storage', $this->app->storagePath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'app', $this->app->getAppPath());

        $this->assertEquals(Application::VERSION, $this->app->version());

        $storagePath = realpath($this->basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'logs');
        $this->app->setStoragePath($storagePath);
        $this->assertEquals($storagePath, $this->app->storagePath());
    }

    public function testRun()
    {
        $config = new Repository(require __DIR__ . '/../config/app.php');
        $this->app->setConfig($config);
        $this->assertEquals($config, $this->app->getConfig());
        $this->app->bootStrap();
        $this->app->run();
    }

    public function testBootstrapWithConfigPath()
    {
        $this->app->setConfigPath(__DIR__ . '/../config/app.php');
        $this->app->bootStrap();
    }
}
