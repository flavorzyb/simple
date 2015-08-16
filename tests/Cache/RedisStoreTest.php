<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 下午6:00
 */

namespace Simple\Cache;

use Simple\Redis\RedisServer;

class RedisStoreTest extends StoreTest
{
    /**
     * @var RedisServer
     */
    private $redisServer  = null;

    protected function setUp()
    {
        $servers            = [['host'=> '127.0.0.1', 'port'=>6379], ['host'=> '127.0.0.1', 'port'=>6379]];
        $this->redisServer  = new RedisServer($servers);
        $this->setStore(new RedisStore($this->redisServer, $this->prefix));
    }

    /**
     * @return RedisStore
     */
    protected function getStore()
    {
        return $this->store;
    }

    public function testGetRedis()
    {
        $this->assertEquals($this->redisServer, $this->getStore()->getRedisServer());
    }


    public function testSingleServer()
    {
        $servers            = [['host'=> '127.0.0.1', 'port'=>6379]];
        $this->redisServer  = new RedisServer($servers);
        $this->setStore(new RedisStore($this->redisServer, $this->prefix));
        $this->testMuSetAndMutGet();
    }
}
