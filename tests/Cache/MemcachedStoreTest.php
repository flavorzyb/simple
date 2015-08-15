<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 下午5:01
 */

namespace Simple\Cache;

use Memcached;

class MemcachedStoreTest extends StoreTest
{
    /**
     * @var Memcached
     */
    private $memcached  = null;

    protected function setUp()
    {
        $this->memcached    = new Memcached();
        $this->memcached->addServer('127.0.0.1', 11211);
        $this->setStore(new MemcachedStore($this->memcached, $this->prefix));
    }

    /**
     * @return MemcachedStore
     */
    protected function getStore()
    {
        return $this->store;
    }

    public function testGetMemcached()
    {
        $this->assertEquals($this->memcached, $this->getStore()->getMemcached());
    }
}
