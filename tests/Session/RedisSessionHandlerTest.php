<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 上午12:44
 */

namespace Simple\Session;

use Simple\Cache\RedisStore;
use Simple\Redis\RedisServer;

class RedisSessionHandlerTest extends SessionHandlerTest
{
    protected $redis    = null;

    protected function setUp()
    {
        parent::setUp();

        $redisServer = new RedisServer([['host'=> '127.0.0.1', 'port'=>6379], ['host'=> '127.0.0.1', 'port'=>6379]]);

        $this->redis    = new RedisStore($redisServer, "session_");
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
