<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 下午6:00
 */

namespace Simple\Cache;

use Redis;

class RedisStoreTest extends StoreTest
{
    /**
     * @var Redis
     */
    private $redis  = null;

    protected function setUp()
    {
        $this->redis    = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->setStore(new RedisStore($this->redis, $this->prefix));
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
        $this->assertEquals($this->redis, $this->getStore()->getRedis());
    }
}
