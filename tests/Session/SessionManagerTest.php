<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 上午10:14
 */

namespace Simple\Session;

use PHPUnit_Framework_TestCase;
use Simple\Config\Repository;

class SessionManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Repository
     */
    protected $config   = null;

    protected function setUp()
    {
        $this->config   = new Repository(['lifetime' => 120, 'cookie_httponly' => true]);
    }
    public function testFileDriver()
    {
        $config             = $this->config->all();
        $config['driver']   = 'file';
        $config['files']   = TESTING_TMP_PATH;
    }

    public function testMemcachedDriver()
    {
    }

    public function testRedisDriver()
    {
    }
}
