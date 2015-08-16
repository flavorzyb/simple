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
use Simple\Cache\RedisStore;
use Simple\Config\Repository;
use Simple\Helper\Helper;
use Simple\Redis\RedisServer;

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
     * Sets session.* ini variables.
     *
     * For convenience we omit 'session.' from the beginning of the keys.
     * Explicitly ignores other ini keys.
     *
     * @param array $options Session ini directives array(key => value).
     *
     * @see http://php.net/session.configuration
     */
    public function setOptions(array $options)
    {
        $validOptions = array_flip(array(
            'cache_limiter', 'cookie_domain', 'cookie_httponly',
            'cookie_lifetime', 'cookie_path', 'cookie_secure',
            'entropy_file', 'entropy_length', 'gc_divisor',
            'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character',
            'hash_function', 'name', 'referer_check',
            'serialize_handler', 'use_cookies',
            'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled',
            'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name',
            'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags',
        ));

        foreach ($options as $key => $value) {
            if (isset($validOptions[$key])) {
                ini_set('session.'.$key, $value);
            }
        }

        if (isset($options['lifetime'])) {
            ini_set('session.gc_maxlifetime', intval($options['lifetime']));
        }
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
        $expireTime     = intval($this->config['lifetime']);

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
        $expireTime     = intval($this->config['lifetime']);

        if ($persistent) {
            foreach ($serverArray as $k => $v)
            {
                $v['persistent']    = true;
                $serverArray[$k]    = $v;
            }
        }

        $redisServer    = new RedisServer($serverArray);
        $redisStore     = new RedisStore($redisServer, $prefix);
        return new CacheSessionHandler($redisStore, $expireTime);
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
                $this->driver   = $this->createRedisDriver();
                break;
            default:
                throw new SessionException("Driver [{$this->config['driver']}] not supported.");
        }

        $this->setOptions($this->config->all());
        session_set_save_handler($this->driver, false);
        return $this->driver;
    }
}
