<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 上午12:44
 */

namespace Simple\Session;

use Redis;

use Simple\Cache\RedisStore;

class RedisSessionHandlerTest extends SessionHandlerTest
{
    protected $redis    = null;

    protected function setUp()
    {
        parent::setUp();
        $redis  = new Redis();
        $redis->connect("127.0.0.1", 6379);

        $this->redis    = new RedisStore($redis, "session_");
        $this->setSessionHandler(new CacheSessionHandler($this->redis, 1200));
    }

    /**
     * @return CacheSessionHandler
     */
    protected function getSessionHandler()
    {
        return $this->sessionHandler;
    }

    public function testGetCache()
    {
        $this->assertEquals($this->redis, $this->getSessionHandler()->getCache());
    }
}
