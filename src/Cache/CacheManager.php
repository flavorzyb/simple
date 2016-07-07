<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/24
 * Time: 上午11:59
 */

namespace Simple\Cache;

use Memcached;

use Simple\Redis\RedisServer;
use Simple\Config\Repository;

class CacheManager
{
    /**
     * @var Repository
     */
    protected $config   = null;

    /**
     * @var Store
     */
    protected $store   = null;
    /**
     * construct
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config   = $config;
    }

    /**
     * create memcached store
     *
     * @return \Simple\Cache\MemcachedStore | null
     * @throws CacheException
     */
    protected function createMemcachedStore()
    {
        $serverArray    = $this->config['servers'];
        $persistent     = boolval($this->config['persistent']);
        $name           = trim($this->config['server_name']);
        $prefix         = trim($this->config['prefix']);

        if ($persistent && (strlen($name) > 0)) {
            $memcached  = new Memcached($name);
        } else {
            $memcached  = new Memcached();
        }

        if (!sizeof($memcached->getServerList())) {
            $memcached->addServers($serverArray);
        }

        return new MemcachedStore($memcached, $prefix);
    }

    /**
     * create redis store
     *
     * @return RedisStore
     */
    protected function createRedisStore()
    {
        $serverArray    = $this->config['servers'];
        $persistent     = boolval($this->config['persistent']);
        $prefix         = trim($this->config['prefix']);

        if ($persistent) {
            foreach ($serverArray as $k => $v) {
                $v['persistent']    = true;
                $serverArray[$k]    = $v;
            }
        }

        $redisServer    = new RedisServer($serverArray);
        return new RedisStore($redisServer, $prefix);
    }

    /**
     * get cache store
     *
     * @return null|MemcachedStore|RedisStore|Store
     * @throws CacheException
     */
    public function getStore()
    {
        if (null != $this->store) {
            return $this->store;
        }

        switch($this->config['driver']) {
            case "memcached":
                $this->store   = $this->createMemcachedStore();
                break;
            case "redis":
                $this->store   = $this->createRedisStore();
                break;
            default:
                throw new CacheException("store [{$this->config['driver']}] not supported.");
        }

        return $this->store;
    }
}
