<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 上午10:14
 */

namespace Simple\Session;

use Simple\Config\Repository;
use Simple\Filesystem\Filesystem;

class SessionManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Repository
     */
    protected $config   = null;

    protected function setUp()
    {
        $this->config   = new Repository(['lifetime' => 1440, 'cookie_httponly' => true]);
    }

    public function testFileDriver()
    {
        $config             = $this->config->all();
        $config['driver']   = 'file';
        $config['files']   = TESTING_TMP_PATH;

        // init session directory
        $fileSystem = new Filesystem();
        if (!$fileSystem->isDirectory($config['files'])) {
            $fileSystem->makeDirectory($config['files'], 0755, true);
        }

        $manager = new SessionManager(new Repository($config));
        $this->assertTrue($manager->getDriver() instanceof FileSessionHandler);

        $fileSystem->deleteDirectory($config['files']);

        // test when session directory is not exists
        $this->setExpectedException('Simple\Session\SessionException');
        $manager = new SessionManager(new Repository($config));
        $manager->getDriver();
    }

    public function testMemcachedDriver()
    {
        $config                 = $this->config->all();
        $config['driver']       = 'memcached';
        $config['persistent']   = true;
        $config['prefix']       = "session_";
        $config['expireTime']   = 1200;
        $config['server_name']  = "session_memcached_server";
        $config['servers']   = [['host'=>'127.0.0.1', 'port'=>11211],['host'=>'127.0.0.1', 'port'=>11211]];

        $manager = new SessionManager(new Repository($config));

        $this->assertTrue($manager->getDriver() instanceof CacheSessionHandler);
        $manager->init();
        $this->assertInstanceOf('Simple\Cache\MemcachedStore', $manager->getDriver()->getCache());

        // test persistent
        $manager = new SessionManager(new Repository($config));
        $this->assertTrue($manager->getDriver() instanceof CacheSessionHandler);
        $this->assertInstanceOf('Simple\Cache\MemcachedStore', $manager->getDriver()->getCache());

        // test no persistent
        unset($config['persistent']);
        $manager = new SessionManager(new Repository($config));
        $this->assertTrue($manager->getDriver() instanceof CacheSessionHandler);
        $this->assertInstanceOf('Simple\Cache\MemcachedStore', $manager->getDriver()->getCache());

        // test for exception
        unset($config['servers']);
        $manager = new SessionManager(new Repository($config));
        $this->setExpectedException('Simple\Session\SessionException');
        $this->assertTrue($manager->getDriver() instanceof CacheSessionHandler);
        $this->assertInstanceOf('Simple\Cache\MemcachedStore', $manager->getDriver()->getCache());
    }

    public function testRedisDriver()
    {
        $config                 = $this->config->all();
        $config['driver']       = 'redis';
        $config['persistent']   = true;
        $config['prefix']       = "session_";
        $config['expireTime']   = 1200;
        $config['server_name']         = "session_redis_server";
        $config['servers']   = [['host'=>'127.0.0.1', 'port'=>6379],['host'=>'127.0.0.1', 'port'=>6379]];


        $manager = new SessionManager(new Repository($config));

        $this->assertInstanceOf('\SessionHandlerInterface', $manager->getDriver());
        $this->assertInstanceOf('Simple\Cache\RedisStore', $manager->getDriver()->getCache());
        $manager->init();
    }

    public function testUnSupportDriver()
    {
        $config                 = $this->config->all();
        $config['driver']       = 'UnSupport';
        $config['persistent']   = true;
        $config['prefix']       = "session_";
        $config['expireTime']   = 1200;
        $config['server_name']  = "session_redis_server";
        $config['servers']      = [['host'=>'127.0.0.1', 'port'=>6379],['host'=>'127.0.0.1', 'port'=>6379]];


        $manager = new SessionManager(new Repository($config));
        $this->setExpectedException('Simple\Session\SessionException');
        $this->assertInstanceOf('\SessionHandlerInterface', $manager->getDriver());
    }
}
