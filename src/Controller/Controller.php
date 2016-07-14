<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/17
 * Time: 下午7:14
 */

namespace Simple\Controller;

use Smarty;
use BadMethodCallException;


abstract class Controller
{
    /**
     * @var Smarty
     */
    protected $templateEngine   = null;

    /**
     * view resource path
     * @var string
     */
    protected $resourcePath     = null;

    /**
     * view compile path
     * @var string
     */
    protected $compilePath   = null;

    /**
     * The middleware registered on the controller.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * @param null $resourcePath
     */
    public function __construct($resourcePath = null)
    {
        $this->resourcePath = $resourcePath;
    }

    /**
     * set resource path
     * @param string $resourcePath
     * @return void
     */
    public function setResourcePath($resourcePath)
    {
        $this->resourcePath = $resourcePath;
    }

    /**
     * get resource path
     *
     * @return string
     */
    public function getResourcePath()
    {
        return $this->resourcePath;
    }

    /**
     * set resource compile path
     *
     * @param string $path
     * @return void
     */
    public function setCompilePath($path)
    {
        $this->compilePath  = $path;
    }

    /**
     * get resource compile path
     * @return string
     */
    public function getCompilePath()
    {
        return $this->compilePath;
    }
    /**
     * get default template engine
     *
     * @return Smarty
     */
    protected function getDefaultTemplateEngine()
    {
        $smarty = new Smarty();
        $smarty->debugging          = false;
        $smarty->caching            = false;
        $smarty->left_delimiter     = '{|';
        $smarty->right_delimiter    = '|}';
        $smarty->setTemplateDir($this->resourcePath);

        return $smarty;
    }

    /**
     * set template engine
     *
     * @param mixed $engine
     * @return void
     */
    public function setTemplateEngine($engine)
    {
        $this->templateEngine   = $engine;
    }

    /**
     * set template engine
     *
     * @return Smarty
     */
    public function getTemplateEngine()
    {
        if (null == $this->templateEngine) {
            $this->setTemplateEngine($this->getDefaultTemplateEngine());
        }

        return $this->templateEngine;
    }

    /**
     * Get the middleware assigned to the controller.
     *
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * set the middleware
     * @param array $middlewareArray
     */
    public function setMiddleware(array $middlewareArray)
    {
        $this->middleware = $middlewareArray;
    }
}
