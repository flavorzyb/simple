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
        $this->prefix = strlen($prefix) > 0 ? $prefix . '_' : '';
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        $value = $this->memcached->get($this->prefix.$key);

        if ($this->memcached->getResultCode() == 0)
        {
            return $value;
        }

        return null;
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  int     $second
     * @return bool
     */
    public function set($key, $value, $second)
    {
        return $this->memcached->set($this->prefix.$key, $value, $second);
    }

    /**
     * Retrieve multiple items
     *
     * @param array $keyArray
     * @return mixed
     */
    public function mGet(array $keyArray)
    {
        if (empty($keyArray)) return [];
        $value  =   $this->memcached->getMulti($keyArray);
        if ($this->memcached->getResultCode() == 0)
        {
            return $value;
        }

        // @codeCoverageIgnoreStart
        return null;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param array $itemArray
     * @param int $expireTime
     * @return boolean
     */
    public function mSet(array $itemArray, $expireTime)
    {
        if (empty($itemArray)) return false;
        return $this->memcached->setMulti($itemArray, $expireTime);
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
        return $this->memcached->increment($this->prefix.$key, $value);
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
        return $this->memcached->decrement($this->prefix.$key, $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return boolean
     */
    public function forever($key, $value)
    {
        return $this->memcached->set($this->prefix.$key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->memcached->delete($this->prefix.$key);
    }

    /**
     * Get the underlying Memcached connection.
     *
     * @return \Memcached
     */
    public function getMemcached()
    {
        return $this->memcached;
    }

    /**
     * Remove all items from the cache.
     */
    public function flush()
    {
        $this->memcached->flush();
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
}
