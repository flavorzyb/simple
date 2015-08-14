<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/14
 * Time: 上午10:00
 */

namespace Simple\Foundation;


class Application
{
    /**
     * version number
     *
     * @var string
     */
    const VERSION           = '1.0.0';

    /**
     * The base path
     * @var string
     */
    protected $basePath     = null;
    /**
     * the config array
     * @var array
     */
    protected $config       = array();

    /**
     * the storage path
     * @var string
     */
    protected $storagePath  = null;
    /**
     * Application constructor.
     * @param string $basePath
     */
    public function __construct($basePath = null)
    {
        $path = realpath($basePath);
        if (is_dir($path)) {
            $this->setBasePath($path);
        }
    }

    /**
     * Get the path to the application "app" directory.
     * @return string
     */
    public function appPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'app';
    }
    /**
     * get base path
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * set base path
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * boot strap application
     */
    public function bootStrap()
    {
    }

    /**
     * run the application
     */
    public function run()
    {
    }

    public function setStoragePath($path)
    {

    }

    /**
     * Get the path to the storage directory.
     */
    public function storagePath()
    {
        return $this->storagePath ? : $this->basePath.DIRECTORY_SEPARATOR.'storage';
    }
}
