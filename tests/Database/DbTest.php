<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/17
 * Time: 上午9:04
 */

namespace Simple\Database;

use PDO;
use Simple\Config\Repository;

class DbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Repository
     */
    protected $config   = null;

    /**
     * @var Db
     */
    protected $db       = null;

    protected function setUp()
    {
        $config         = [ 'fetch'     =>PDO::FETCH_ASSOC,
                            'default'   => 'read',
                            'driver'    => 'mysql',
                            'connections'   => [
                                                'write'=>[
                                                    'host'=>'127.0.0.1',
                                                    'database'=>'simple_testing',
                                                    'username'=>'test',
                                                    'password'=>'test',
                                                    'port'    => 3306,
                                                    ],
                                                'read'=>[
                                                    'host'=>'127.0.0.1',
                                                    'database'=>'simple_testing',
                                                    'username'=>'test',
                                                    'password'=>'test',
                                                    ],
                                            ],
                            'charset'   => 'utf8',
                            'strict'=>true,
                            'timezone'=>'+8:00',
                            'collation' => 'utf8_unicode_ci',
                            'options'   =>[PDO::ATTR_TIMEOUT => 10, PDO::ATTR_PERSISTENT => true,],
                            ];

        $this->config   = new Repository($config);
        $this->db       = new Db($this->config);
    }

    public function testUnSetDefaultThrowException()
    {
        $this->config->offsetUnset('default');
        $this->setExpectedException('Simple\Database\DbException');
        $this->db   = new Db($this->config);
    }

    public function testDefaultNameErrorThrowException()
    {
        $this->config['default']    = 'error_pdo_name';
        $this->setExpectedException('Simple\Database\DbException');
        $this->db   = new Db($this->config);
    }

    public function testHostErrorThrowException()
    {
        $config = $this->config->all();

        unset($config['connections']['write']['host']);
        unset($config['connections']['read']['host']);

        $this->config   = new Repository($config);
        $this->setExpectedException('Simple\Database\DbException');
        $this->db   = new Db($this->config);
        $this->db->getDefaultPdo();
    }

    public function testDatabaseErrorThrowException()
    {
        $config = $this->config->all();

        unset($config['connections']['write']['database']);
        unset($config['connections']['read']['database']);

        $this->config   = new Repository($config);
        $this->setExpectedException('Simple\Database\DbException');
        $this->db   = new Db($this->config);
        $this->db->getDefaultPdo();
    }

    public function testUnSetConnectionsThrowException()
    {
        $this->config->offsetUnset('connections');
        $this->setExpectedException('Simple\Database\DbException');
        $this->db   = new Db($this->config);
    }

    public function testErrorDriverThrowException()
    {
        $this->config['driver'] = 'sqlite';
        $this->setExpectedException('Simple\Database\DbException');
        $this->db   = new Db($this->config);
    }

    public function testConnectionWithPort()
    {
        $config = $this->config->all();

        unset($config['connections']['write']['port']);
        unset($config['connections']['read']['port']);

        $this->config   = new Repository($config);
        $this->db   = new Db($this->config);
        $this->assertInstanceOf('\PDO', $this->db->getDefaultPdo());
        $this->assertInstanceOf('\PDO', $this->db->getPdo('read'));
        $this->assertInstanceOf('\PDO', $this->db->getPdo('write'));
    }

    public function testConnection()
    {
        $this->assertInstanceOf('\PDO', $this->db->getDefaultPdo());
        $this->assertInstanceOf('\PDO', $this->db->getPdo('read'));
        $this->assertInstanceOf('\PDO', $this->db->getPdo('write'));
        $this->assertInstanceOf('\PDO', $this->db->getPdo('error_name'));

        $this->db->useDefaultPdo();
        $this->db->usePdo('read');
        $this->db->usePdo('write');
        $this->assertInstanceOf('\PDO', $this->db->getActivePdo());

        $this->db->usePdo('error_name');
    }

    public function testDisconnect()
    {
        $this->db->getPdo('read');
        $this->db->getPdo('write');

        $this->db->usePdo('read');
        $this->assertInstanceOf('\PDO', $this->db->getActivePdo());
        $this->db->disconnect('read');
        $this->assertNull($this->db->getActivePdo());


        $this->db->usePdo('write');
        $this->assertInstanceOf('\PDO', $this->db->getActivePdo());
        $this->db->disconnect('write');
        $this->assertNull($this->db->getActivePdo());
    }


}
