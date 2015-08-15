<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 下午4:16
 */

namespace Simple\Cache;

use Memcached;

class MemcachedStore implements Store
{
    /**
     * The Memcached instance.
     * @var Memcached
     */
    protected $memcached;

    /**
     * A string that should be prepended to keys.
     * @var string
     */
    protected $prefix;

    /**
     * Create a new Memcached store.
     *
     * @param Memcached $memcached
     * @param string $prefix
     */
    public function __construct(Memcached $memcached, $prefix = '')
    {
        $this->memcached = $memcached;
        $this->prefix = strlen($prefix) > 0 ? $prefix.':' : '';
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {

    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  int     $second
     */
    public function set($key, $value, $second)
    {
    }

    /**
     * Retrieve multiple items
     *
     * @param array $keyArray
     * @return mixed
     */
    public function mGet(array $keyArray)
    {
    }

    /**
     * @param array $itemArray
     * @param int $expireTime
     */
    public function mSet(array $itemArray, $expireTime)
    {
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed   $value
     */
    public function forever($key, $value)
    {
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function delete($key)
    {
    }

    /**
     * Remove all items from the cache.
     */
    public function flush()
    {
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
    }
}
