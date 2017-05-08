<?php
namespace Simple\Session;

use Memcached;

use Simple\Cache\MemcachedStore;

class MemcachedSessionHandlerTest extends SessionHandlerTest
{
    protected $memcacheCache    = null;

    protected function setUp()
    {
        parent::setUp();
        $memcached  = new Memcached();
        $memcached ->addServer("127.0.0.1", 11211);
        $this->memcacheCache    = new MemcachedStore($memcached, "session_");
        $this->setSessionHandler(new CacheSessionHandler($this->memcacheCache, 1200));
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
        $this->assertEquals($this->memcacheCache, $this->getSessionHandler()->getCache());
    }
}
