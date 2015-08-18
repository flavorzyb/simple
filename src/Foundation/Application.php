<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/14
 * Time: 上午10:00
 */

namespace Simple\Foundation;


use Simple\Config\Repository;
use Simple\Environment\DotEnv;

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
     * application name space
     *
     * @var string
     */
    protected $appNameSpace = "apps";

    /**
     * default controller name
     * @var string
     */
    protected $defaultController    = "Index";

    /**
     * default method name
     * @var string
     */
    protected $defaultMethod        = "index";

    /**
     * sub module
     * @var string
     */
    protected $subModule            = "";

    /**
     * env file path
     *
     * @var string
     */
    protected $envFile              = "";

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
     * get application name space
     * @return string
     */
    public function getAppNameSpace()
    {
        return $this->appNameSpace;
    }

    /**
     * set application name space
     * @param string $appNameSpace
     * @return void
     */
    public function setAppNameSpace($appNameSpace)
    {
        $this->appNameSpace = $appNameSpace;
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
     * get env file
     * @return string
     */
    public function getEnvFile()
    {
        return ($this->envFile ? : '.env');
    }

    /**
     * set env file
     * @param string $envFile
     */
    public function setEnvFile($envFile)
    {
        $this->envFile = $envFile;
    }

    /**
     * init Environment
     */
    protected function initEnvironment()
    {
        date_default_timezone_set($this->config['timezone']);
        mb_internal_encoding('UTF-8');

        if (is_file($this->getEnvFile())) {
            $env = new DotEnv($this->basePath, $this->getEnvFile(), true);
            $env->load();
        }
    }

    /**
     * load env from file do not override
     */
    protected function loadEnvironment()
    {
        $file = $this->basePath . DIRECTORY_SEPARATOR . $this->getEnvFile();
        if (is_file($file)) {
            $env = new DotEnv($this->basePath, $this->getEnvFile(), true);
            $env->load();
        }
    }
    /**
     * load env from file override
     */
    protected function overloadEnvironment()
    {
        $file = $this->basePath . DIRECTORY_SEPARATOR . $this->getEnvFile();
        if (is_file($file)) {
            $env = new DotEnv($this->basePath, $this->getEnvFile(), false);
            $env->load();
        }
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
     * get default controller name
     *
     * @return string
     */
    public function getDefaultController()
    {
        return $this->defaultController;
    }

    /**
     * set default controller name
     * @param string $defaultController
     */
    public function setDefaultController($defaultController)
    {
        $this->defaultController = $defaultController;
    }

    /**
     * get default method name
     *
     * @return string
     */
    public function getDefaultMethod()
    {
        return $this->defaultMethod;
    }

    /**
     * set default method
     * @param string $defaultMethod
     */
    public function setDefaultMethod($defaultMethod)
    {
        $this->defaultMethod = $defaultMethod;
    }

    /**
     * get sub module
     * @return string
     */
    public function getSubModule()
    {
        return $this->subModule;
    }

    /**
     * set sub module
     *
     * @param string $subModule
     */
    public function setSubModule($subModule)
    {
        $this->subModule = $subModule;
    }

    /**
     *
     */
    protected function parseRequestUri()
    {
        $result = ['controller'=> $this->defaultController, 'method' => $this->getDefaultMethod(), 'params' => []];

        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUri     = $_SERVER['REQUEST_URI'];
            $queryStr       = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
            $queryStrLen    =strlen($queryStr);
            if ($queryStrLen > 0) {
                $requestUri = substr($requestUri, 0, -1 - $queryStrLen);
            }

            $data   = explode('/', $requestUri);

            if (isset($data[1]) && ("index.php" != strtolower($data[1]))) {
                $result['controller']   = $data[1];
            }

            if (isset($data[2])) {
                $result['method']   = $data[2];
            }

            $size   = sizeof($data);
            $params = [];
            if ($size > 2) {
                for ($i = 3; $i < $size; $i ++) {
                    $params[] = $data[$i];
                }
            }

            $result['params']   = $params;
        }

        return $result;
    }
    /**
     * run the application
     */
    public function run()
    {
        $data       = $this->parseRequestUri();
        $controller = $data['controller'];
        $method     = $data['method'];
        $params     = $data['params'];

        $subModule  = $this->subModule;
        if ("" != $subModule) {
            $subModule = "\\" . $subModule;
        }

        $class      = sprintf("%s\\Controller%s\\%sController", $this->appNameSpace, $subModule, $controller);

        if (!(class_exists($class) && method_exists($class, $method))) {
            $this->fileNotFound();
        } else {
            $class  = new $class;
            call_user_func_array(array($class, $method), $params);
        }
    }

    public function fileNotFound()
    {
        if ("cli" != PHP_SAPI) {
            header('HTTP/1.1 404 Not Found');
            exit('404 Not Found');
        }
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
