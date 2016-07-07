<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/24
 * Time: 下午3:21
 */

namespace Simple\Cache;


use Simple\Config\Repository;

class CacheManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testMemcachedStore()
    {
        $config = [ 'driver'        =>'memcached',
                    'server_name'   => 'mc_server',
                    'persistent'    =>true,
                    'servers'       => [['host'=>'127.0.0.1', 'port'=>11211],['host'=>'127.0.0.1', 'port'=>11211]],
                    'prefix'        => 'mc',
                ];

        $config = new Repository($config);
        $cacheManager = new CacheManager($config);
        $this->assertInstanceOf('\Simple\Cache\MemcachedStore', $cacheManager->getStore());
        $this->assertTrue($cacheManager->getStore()->set('key', 'value', 100));
        $this->assertEquals('value', $cacheManager->getStore()->get('key'));
    }

    public function testRedisStore()
    {
        $config = [ 'driver'        =>'redis',
                    'server_name'   => 'redis_server',
                    'persistent'    =>true,
                    'servers'       => [['host'=>'127.0.0.1', 'port'=>6379],['host'=>'127.0.0.1', 'port'=>6379]],
                    'prefix'        => 'redis',
                ];

        $config = new Repository($config);
        $cacheManager = new CacheManager($config);
        $this->assertInstanceOf('\Simple\Cache\RedisStore', $cacheManager->getStore());

        $this->assertTrue($cacheManager->getStore()->set('key', 'value', 100));
        $this->assertEquals('value', $cacheManager->getStore()->get('key'));
    }

    public function testMemcachedStoreWithNoName()
    {
        $config = [ 'driver'        =>'memcached',
            'persistent'    =>true,
            'servers'       => [['host'=>'127.0.0.1', 'port'=>11211],['host'=>'127.0.0.1', 'port'=>11211]],
            'prefix'        => 'mc',
        ];

        $config = new Repository($config);
        $cacheManager = new CacheManager($config);
        $this->assertInstanceOf('\Simple\Cache\MemcachedStore', $cacheManager->getStore());
        $this->assertTrue($cacheManager->getStore()->set('key', 'value', 100));
        $this->assertEquals('value', $cacheManager->getStore()->get('key'));
    }

    /**
     * @expectedException \Simple\Cache\CacheException
     */
    public function testErrorStore()
    {
        $config = [ 'driver'        =>'errorStore',
            'persistent'    =>true,
        ];

        $config = new Repository($config);
        $cacheManager = new CacheManager($config);
        $this->assertInstanceOf('\Simple\Cache\RedisStore', $cacheManager->getStore());
    }
}
