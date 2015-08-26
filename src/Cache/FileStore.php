<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/26
 * Time: 下午7:25
 */

namespace Simple\Cache;


use Simple\Filesystem\Filesystem;

class FileStore implements Store
{
    /**
     * @var Filesystem
     */
    protected $fileSystem   = null;
    /**
     * The file cache directory.
     * @var string
     */
    protected $directory    = null;

    /**
     * A string that should be prepended to keys.
     * @var string
     */
    protected $prefix   = null;

    /**
     * FileStore constructor.
     * @param Filesystem $fileSystem
     * @param string $directory
     * @param string
     */
    public function __construct(Filesystem $fileSystem, $directory, $prefix)
    {
        $this->fileSystem   = $fileSystem;
        $this->directory    = $directory;
        $this->prefix       = $prefix . '_';
    }

    /**
     * get data from file
     *
     * @param string $key
     * @return array
     */
    protected function getData($key)
    {
        $path = $this->path($key);
        if (!$this->fileSystem->isFile($path)) {
            return ['data' => null, 'time' => null];
        }

        $content    = $this->fileSystem->get($path);
        $expire     = substr($content, 0, 10);

        if (time() > $expire) {
            $this->delete($key);
            return ['data' => null, 'time' => null];
        }

        $data = unserialize(substr($content, 10));


        return ['data'=>$data, 'time' => ($expire - time())];
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        $data   = $this->getData($key);
        return $data['data'];
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  int     $second
     * @return boolean
     */
    public function set($key, $value, $second)
    {
        if ($second <= 0) {
            $second = 9999999999;
        } else {
            $second = time() + $second;
        }

        $path = $this->path($key);
        $this->createCacheDirectory($path);

        return $this->fileSystem->put($path, $second . serialize($value)) > 0;
    }

    /**
     * Retrieve multiple items
     *
     * @param array $keyArray
     * @return mixed
     */
    public function mGet(array $keyArray)
    {
        $result = [];
        foreach ($keyArray as $key) {
            $v = $this->get($key);
            if (null != $v) {
                $result[$key] = $v;
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
        foreach ($itemArray as $key => $value) {
            $this->set($key, $value, $expireTime);
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
        $data   = $this->getData($key);
        $result = intval($data['data']) + $value;
        $this->set($key, $result, $data['time']);

        return $result;
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
        $data   = $this->getData($key);
        $result = intval($data['data']) - $value;
        $this->set($key, $result, $data['time']);

        return $result;
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
        return $this->set($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function delete($key)
    {
        $file = $this->path($key);

        if ($this->fileSystem->exists($file))
        {
            return $this->fileSystem->delete($file);
        }

        return false;
    }

    /**
     * Remove all items from the cache.
     * @return void
     */
    public function flush()
    {
        if ($this->fileSystem->isDirectory($this->directory))
        {
            $this->fileSystem->deleteDirectory($this->directory, true);
        }
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

    /**
     * Get the full path for the given cache key.
     *
     * @param  string  $key
     * @return string
     */
    protected function path($key)
    {
        $hash   = md5($this->prefix . $key);
        $parts  = array_slice(str_split($hash, 2), 0, 2);

        return $this->directory.'/'.implode('/', $parts).'/'.$hash;
    }

    /**
     * Create the file cache directory if necessary.
     *
     * @param  string  $path
     * @return void
     */
    protected function createCacheDirectory($path)
    {
        try
        {
            $this->fileSystem->makeDirectory(dirname($path), 0777, true, true);
        }
        catch (\Exception $e)
        {
            //
        }
    }

    /**
     * @return Filesystem
     */
    public function getFileSystem()
    {
        return $this->fileSystem;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
