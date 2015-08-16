<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 下午4:17
 */

namespace Simple\Cache;

use Simple\Redis\RedisServer;

class RedisStore implements Store
{
    /**
     * The Redis instance.
     * @var RedisServer
     */
    protected $redisServer    = null;
    /**
     * A string that should be prepended to keys.
     * @var string
     */
    protected $prefix   = null;

    /**
     * @param RedisServer $redisServer
     * @param string $prefix
     */
    public function __construct(RedisServer $redisServer, $prefix = '')
    {
        $this->redisServer    = $redisServer;
        $this->prefix   = strlen($prefix) > 0 ? $prefix . '_' : '';
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        $value  = $this->redisServer->getHashClient($key)->get($key);

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
            return $this->redisServer->getHashClient($key)->setex($key, $second, $value);
        } else {
            return $this->redisServer->getHashClient($key)->set($key, $value);
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

        $result     = [];
        $redisServer    = $this->redisServer;
        if (1 == $redisServer->getServerCount()) {
            $dataArray      = $redisServer->getDefaultClient()->getMultiple($keyArray);
            foreach ($dataArray as $k => $v) {
                if (false !== $v) {
                    $result[$keyArray[$k]] = is_numeric($v) ? $v : unserialize($v);
                }
            }
        } else {
            $clientKeyArray = [];
            foreach ($keyArray as $key) {
                $name   = $redisServer->getHashClientName($key);
                // factory key array for redis
                if (isset($clientKeyArray[$name])) {
                    $clientKeyArray[$name][] = $key;
                } else {
                    $clientKeyArray[$name] = [$key];
                }
            }

            foreach ($clientKeyArray as $name => $keyArray) {
                $dataArray = $redisServer->getClient($name)->mget($keyArray);
                foreach ($dataArray as $k => $v) {
                    if (false !== $v) {
                        $result[$keyArray[$k]] = is_numeric($v) ? $v : unserialize($v);
                    }
                }
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

        $redisServer        = $this->redisServer;
        $expireTime         = intval($expireTime);
        $isDefaultClient    = (1 == $redisServer->getServerCount());
        $defaultClient      = $redisServer->getDefaultClient();

        if ($expireTime > 0) {
            foreach ($itemArray as $k => $v) {
                if ($isDefaultClient) {
                    $defaultClient->setex($k, $expireTime, is_numeric($v) ? $v : serialize($v));
                } else {
                    $redisServer->getHashClient($k)->setex($k, $expireTime, is_numeric($v) ? $v : serialize($v));
                }
            }
        } else {
            foreach ($itemArray as $k => $v) {
                if ($isDefaultClient) {
                    $defaultClient->set($k, is_numeric($v) ? $v : serialize($v));
                } else {
                    $redisServer->getHashClient($k)->set($k, is_numeric($v) ? $v : serialize($v));
                }
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
        return $this->redisServer->getHashClient($key)->incrBy($key, $value);
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
        return $this->redisServer->getHashClient($key)->decrBy($key, $value);
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
        return $this->redisServer->getHashClient($key)->set($key, $value);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function delete($key)
    {
        $this->redisServer->getHashClient($key)->delete($key);
        return true;
    }

    /**
     * Remove all items from the cache.
     */
    public function flush()
    {
        $this->redisServer->flushAll();
    }

    /**
     * get Redis instance
     * @return RedisServer
     */
    public function getRedisServer()
    {
        return $this->redisServer;
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
