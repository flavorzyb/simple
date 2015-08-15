<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 下午4:17
 */

namespace Simple\Cache;

use Redis;

class RedisStore implements Store
{
    /**
     * The Redis instance.
     * @var Redis
     */
    protected $redis    = null;
    /**
     * A string that should be prepended to keys.
     * @var string
     */
    protected $prefix   = null;

    /**
     * @param Redis $redis
     * @param string $prefix
     */
    public function __construct(Redis $redis, $prefix = '')
    {
        $this->redis    = $redis;
        $this->prefix   = strlen($prefix) > 0 ? $prefix . '_' : '';
        $this->redis->_prefix($this->prefix);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        $value  = $this->redis->get($key);

        if (false !== $value) {
            return is_numeric($value) ? $value : unserialize($value);
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
        $second = intval($second);

        if (!is_numeric($value)) {
            $value  = serialize($value);
        }

        if ($second > 0) {
            return $this->redis->setex($key, $second, $value);
        } else {
            return $this->redis->set($key, $value);
        }
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

        $keyArray = array_values($keyArray);

        $dataArray  = $this->redis->getMultiple($keyArray);
        $result     = [];

        foreach ($dataArray as $k => $v) {
            if (false !== $v) {
                $result[$keyArray[$k]] = is_numeric($v) ? $v : unserialize($v);
            }
        }

        return $result;
    }

    /**
     * @param array $itemArray
     * @param int $expireTime
     * @return boolean
     */
    public function mSet(array $itemArray, $expireTime)
    {
        if (empty($itemArray)) return false;

        $expireTime = intval($expireTime);
        if ($expireTime > 0) {
            foreach ($itemArray as $k => $v) {
                $this->redis->setex($k, $expireTime, is_numeric($v) ? $v : serialize($v));
            }
        } else {
            foreach ($itemArray as $k => $v) {
                $this->redis->set($k, is_numeric($v) ? $v : serialize($v));
            }
        }

        return true;
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
        return $this->redis->incrBy($key, $value);
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
        return $this->redis->decrBy($key, $value);
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
        return $this->redis->set($key, $value);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function delete($key)
    {
        $this->redis->delete($key);
        return true;
    }

    /**
     * Remove all items from the cache.
     */
    public function flush()
    {
        $this->redis->flushAll();
    }

    /**
     * get Redis instance
     * @return Redis
     */
    public function getRedis()
    {
        return $this->redis;
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
