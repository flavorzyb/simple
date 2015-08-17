<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/14
 * Time: 上午10:00
 */

namespace Simple\Foundation;


use Simple\Config\Repository;

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
     * the config Repository
     * @var Repository
     */
    protected $config       = null;

    /**
     * config path
     * @var string
     */
    protected $configPath   = null;

    /**
     * controller directory path
     *
     * @var string
     */
    protected $controllerPath   = null;

    /**
     * Application constructor.
     * @param string $basePath
     */
    public function __construct($basePath = null)
    {
        $path = realpath($basePath);
        if (is_dir($path)) {
            $this->setBasePath($path);
            $this->setControllerPath($this->getAppPath() . DIRECTORY_SEPARATOR . 'Controller');
        }
    }

    /**
     * set controller root directory path
     *
     * @param $path
     * @return void
     */
    public function setControllerPath($path)
    {
        $path = realpath($path);
        if (is_dir($path)) {
            $this->controllerPath = $path;
        }
    }

    /**
     * get controller root directory path
     * @return string
     */
    public function getControllerPath()
    {
        return $this->controllerPath;
    }

    /**
     * Get the path to the application "app" directory.
     * @return string
     */
    public function getAppPath()
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
     * set application config
     * @param Repository $config
     */
    public function setConfig(Repository $config)
    {
        $this->config   = $config;
    }

    /**
     * get application config
     * @return Repository
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * init config Repository
     * @throws Exception
     */
    protected function initConfig()
    {
        if (!($this->config instanceof Repository)) {
            $filePath   = $this->configPath();
            if (!is_file($filePath)) {
                throw new Exception("Application config file can not found({$filePath}).");
            }

            $this->setConfig(new Repository(require $filePath));
        }
    }

    /**
     * init Environment
     */
    protected function initEnvironment()
    {
        date_default_timezone_set($this->config['timezone']);
        mb_internal_encoding('UTF-8');
    }

    /**
     * boot strap application
     * @throws Exception
     */
    public function bootStrap()
    {
        $this->initConfig();

        $this->initEnvironment();
    }

    /**
     *
     */
    protected function parseRequestUri()
    {
        if (isset($_SERVER['REQUEST_URI'])) {

        }

        return ['controller'=> '', 'method' => '', 'params' => []];
    }
    /**
     * run the application
     */
    public function run()
    {
    }

    /**
     * @return string
     */
    public function configPath()
    {
        return $this->configPath ? : $this->basePath . DIRECTORY_SEPARATOR . 'config/app.php';
    }

    /**
     * set the config path
     * @param string $configPath
     */
    public function setConfigPath($configPath)
    {
        $configPath = realpath($configPath);
        if (is_file($configPath)) {
            $this->configPath = $configPath;
        }
    }

    /**
     * get version
     * @return string
     */
    public function version()
    {
        return self::VERSION;
    }
}
