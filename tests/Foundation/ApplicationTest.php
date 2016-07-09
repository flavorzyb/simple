<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 上午12:28
 */

namespace Simple\Foundation;

use Simple\Config\Repository;
use Simple\Filesystem\Filesystem;
use Simple\Log\Writer;

/**
 * Class ApplicationTest
 * @package Simple\Foundation
 * @runTestsInSeparateProcesses
 */
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

    /**
     * @expectedException \Simple\Foundation\Exception
     */
    public function testBootstrapThrowException()
    {
        $this->app  = new Application("error_path");
        $this->app->bootStrap();
    }

    public function testPathIsMutable()
    {
        $this->assertEquals($this->basePath, $this->app->getBasePath());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'config/app.php', $this->app->configPath());
        $this->assertNull($this->app->getConfig());
        $this->assertEquals($this->basePath . DIRECTORY_SEPARATOR . 'app', $this->app->getAppPath());

        $this->assertEquals(Application::VERSION, $this->app->version());
    }

    public function testRun()
    {
        // empty for request uri
        $config = new Repository(require __DIR__ . '/../config/app.php');
        $this->app->setConfig($config);
        $this->assertEquals($config, $this->app->getConfig());
        $this->app->bootStrap();
        $this->app->run();

        // request uri = index.php
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['QUERY_STRING'] = '';
        $this->app->run();

//
        // request uri = index.php
        $_SERVER['REQUEST_URI'] = '/index.php';
        $_SERVER['QUERY_STRING'] = '';
        $this->app->run();

        // request uri = /index.php?id=11223&code=123
        $_SERVER['REQUEST_URI'] = '/index.php?id=11223&code=123';
        $_SERVER['QUERY_STRING'] = 'id=11223&code=123';
        $this->app->run();

        // request uri = /index.php/aaa/bb?id=11223&code=123
        $_SERVER['REQUEST_URI'] = '/index.php/aaa/bb?id=11223&code=123';
        $_SERVER['QUERY_STRING'] = 'id=11223&code=123';
        $this->app->run();

        $_SERVER['REQUEST_URI'] = '/aaa/bbb/11/23/44?id=87&code=89';
        $_SERVER['QUERY_STRING'] = 'id=87&code=89';

        $this->app->setSubModule("Admin");
        $this->assertEquals("Admin", $this->app->getSubModule());
        $this->app->run();
    }

    public function testBootstrapWithConfigPath()
    {
        $this->app->setConfigPath(__DIR__ . '/../config/app.php');
        $this->app->setEnvFile('Foundation/.env');
        $this->app->bootStrap();
        $this->assertEquals("local", env("APP_ENV"));

        $this->app->setEnvFile('Foundation/.env.testing');
        $this->app->overloadEnvironment();
        $this->assertEquals("testing", env("APP_ENV"));

        // not overload, keep values
        $this->app->setEnvFile('Foundation/.env');
        $this->app->loadEnvironment();
        $this->assertEquals("testing", env("APP_ENV"));
    }

    public function testBootstrapWithConfigPathTestingEnv()
    {
        $this->app->setConfigPath(__DIR__ . '/../config/app.php');
        $this->app->setEnvFile('Foundation/.env.testing');
        $this->app->bootStrap();

        $this->app->setLog(new Writer(new Filesystem()));
        $this->assertTrue($this->app->getLog() instanceof Writer);
    }

    public function testNamespaceIsMutable()
    {
        $this->app->setAppNameSpace("WeMall");
        $this->assertEquals("WeMall", $this->app->getAppNameSpace());
    }

    public function testDefaultControllerAndMethod()
    {
        $this->app->setDefaultController("Index");
        $this->assertEquals("Index", $this->app->getDefaultController());

        $this->app->setDefaultMethod("index");
        $this->assertEquals("index", $this->app->getDefaultMethod());
    }
}
