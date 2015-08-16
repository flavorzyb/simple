<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 上午9:44
 */

namespace Simple\Session;

use Memcached;
use Redis;
use Exception;

use Simple\Cache\MemcachedStore;
use Simple\Config\Repository;
use Simple\Helper\Helper;

class SessionManager
{
    /**
     * @var Repository
     */
    protected $config   = null;

    protected $driver   = null;
    /**
     * construct
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config   = $config;
    }

    /**
     * create file session driver
     * throw SessionException when session save path is not exists
     *
     * @return FileSessionHandler
     * @throws SessionException
     */
    protected function createFileDriver()
    {
        $path   = $this->config['files'];

        $fileSystem = Helper::getFileSystem();

        if (!$fileSystem->isDirectory($path)) {
            throw new SessionException("session path({$path}) is not exists.");
        }

        return new FileSessionHandler($fileSystem, $path);
    }

    /**
     * create memcached session driver
     * throw SessionException when session save path is not exists
     *
     * @return FileSessionHandler
     * @throws SessionException
     */
    protected function createMemcacheDriver()
    {
        $serverArray    = $this->config['servers'];
        $persistent     = boolval($this->config['persistent']);
        $name           = trim($this->config['name']);
        $prefix         = trim($this->config['prefix']);
        $expireTime     = intval($this->config['expireTime']);

        try {
            if ($persistent && (strlen($name) > 0)) {
                $memcached  = new Memcached($name);
            } else {
                $memcached  = new Memcached();
            }

            if (!sizeof($memcached->getServerList())) {
                $memcached->addServers($serverArray);
            }

            $store = new MemcachedStore($memcached, $prefix);
            return new CacheSessionHandler($store, $expireTime);
        } catch (Exception $ex) {
            throw new SessionException($ex->getMessage(), $ex->getCode());
        }

        return null;
    }

    protected function createRedisDriver()
    {
        $serverArray    = $this->config['servers'];
        $persistent     = boolval($this->config['persistent']);
        $prefix         = trim($this->config['prefix']);
        $expireTime     = intval($this->config['expireTime']);

        try {
            $redis  = new Redis();

            if ($persistent) {
                $redis->pconnect();
            } else {
                $memcached  = new Memcached();
            }

            if (!sizeof($memcached->getServerList())) {
                $memcached->addServers($serverArray);
            }

            $store = new MemcachedStore($memcached, $prefix);
            return new CacheSessionHandler($store, $expireTime);
        } catch (Exception $ex) {
            throw new SessionException($ex->getMessage(), $ex->getCode());
        }

        return null;
    }
    /**
     * get session driver
     *
     * @return SessionHandlerInterface
     * @throws SessionException
     */
    public function getDriver()
    {
        if (null != $this->driver) {
            return $this->driver;
        }

        switch($this->config['driver']) {
            case "file":
                $this->driver   = $this->createFileDriver();
                break;
            case "memcached":
                $this->driver   = $this->createMemcacheDriver();
                break;
            case "redis":
                break;
            default:
        }

        return $this->driver;
    }
}
