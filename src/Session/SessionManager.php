<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 上午9:44
 */

namespace Simple\Session;


use Simple\Config\Repository;

class SessionManager
{
    /**
     * @var Repository
     */
    protected $config   = null;

    /**
     * construct
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config   = $config;
    }

    protected function createFileDriver()
    {
        $path   = $this->config['files'];
    }
}
