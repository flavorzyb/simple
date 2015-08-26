<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 下午5:21
 */

namespace Simple\Redis;

use Redis;
use Exception;

use Simple\Config\Repository;

class RedisServer
{
    /**
     * default server connection name
     * used when get client by name and name is not exists.
     * @var string
     */
    protected $defaultName  = "";
    /**
     * servers connection params
     *
     * @var array
     */
    protected $serverArray  = [];

    /**
     * Redis client array
     *
     * @var array
     */
    protected $clientArray  = [];

    /**
     * hash array of server name
     * @var array
     */
    protected $hashArray    = [];

    /**
     * cache prefix string
     * @var string
     */
    protected $prefix       = "";
    /**
     * create RedisServer instance
     *
     * @param array $serverArray
     * @throws RedisException
     */
    public function __construct(array $serverArray)
    {
        if (empty($serverArray)) {
            throw new RedisException("Redis Server must contain at least one server");
        }

        foreach ($serverArray as $key => $server) {
            if (!isset($server['host'])) {
                throw new RedisException("Redis Server must contain server name");
            }

            if (!isset($server['port'])) {
                throw new RedisException("Redis Server must contain server port");
            }

            $this->serverArray[$key]    = new Repository($server);
            $this->hashArray[]          = $key;
            // init default name
            if (!$this->defaultName) {
                $this->defaultName = $key;
            }
        }
    }

    /**
     * create a Redis instance
     * @param Repository $config
     * @return Redis
     * @throws RedisException
     */
    protected function createClient(Repository $config)
    {
        $server     = trim($config['host']);
        $port       = intval($config['port']);
        $timeout    = floatval($config['timeout']);
        $persistent = boolval($config['persistent']);

        $redis      = new Redis();
        try {

            if ($persistent) {
                $result = $redis->pconnect($server, $port, $timeout);
            } else {
                $result = $redis->connect($server, $port, $timeout);
            }

            if (false == $result) {
                throw new RedisException("Redis Server can not be connection({$server}:{$port}:{$timeout})");
            }
        } catch(Exception $ex) {
            throw new RedisException($ex->getMessage(), $ex->getCode());
        }

        $redis->_prefix($this->prefix);

        return $redis;
    }

    /**
     * get Redis instance by name
     * @param string $name
     * @return Redis
     * @throws RedisException
     */
    public function getClient($name)
    {
        if (!isset($this->serverArray[$name])) {
            $name   = $this->defaultName;
        }

        if (isset($this->clientArray[$name])) {
            if ('cli' == PHP_SAPI) {
                try {
                    $this->clientArray[$name]->ping();
                } catch (\RedisException $ex) {
                    $this->clientArray[$name] = $this->createClient($this->serverArray[$name]);
                }
            }
            return $this->clientArray[$name];
        }

        $this->clientArray[$name] = $this->createClient($this->serverArray[$name]);
        return $this->clientArray[$name];
    }

    /**
     * get server name by hash key
     * @param $key
     * @return string
     */
    public function getHashClientName($key)
    {
        if (1 == $this->getServerCount()) {
            return $this->defaultName;
        }

        $count  = intval(sprintf('%u', crc32($key)));
        return $this->hashArray[$count % sizeof($this->serverArray)];
    }

    /**
     * get Redis instance by hash key
     *
     * @param string $key
     * @return Redis
     * @throws RedisException
     */
    public function getHashClient($key)
    {
        return $this->getClient($this->getHashClientName($key));
    }

    /**
     * the count of redis server
     * @return int
     */
    public function getServerCount()
    {
        return sizeof($this->serverArray);
    }

    /**
     * get default Redis client
     * @return Redis
     */
    public function getDefaultClient()
    {
        return $this->getClient($this->defaultName);
    }

    /**
     * get prefix string
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * set prefix string
     *
     * @param $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix   = trim($prefix);
    }

    /**
     * Removes all entries from all cache.
     */
    public function flushAll()
    {
        foreach (array_keys($this->serverArray) as $name) {
            $this->getClient($name)->flushAll();
        }
    }
}
