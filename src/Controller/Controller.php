<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/17
 * Time: 下午7:14
 */

namespace Simple\Controller;

use Smarty;

abstract class Controller
{
    /**
     * @var Smarty
     */
    protected $templateEngine = null;

    public function __construct()
    {
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

        return $smarty;
    }

    /**
     * set template engine
     *
     * @param Smarty $engine
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
}
