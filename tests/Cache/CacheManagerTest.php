<?php
namespace Simple\Cache;


use PHPUnit\Framework\TestCase;
use Simple\Config\Repository;

class CacheManagerTest extends TestCase
{
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
