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
        $this->init();
    }

    protected function init()
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

    protected function tearDown()
    {
        $this->init();
        $this->db->usePdo("write");
        $pdo = $this->db->getActivePdo();

        $sql = "DROP TABLE IF EXISTS `test`;";
        $pdo->exec($sql);
    }

    protected function initTable()
    {
        $this->db->usePdo("write");
        $pdo = $this->db->getActivePdo();

        $sql = "DROP TABLE IF EXISTS `test`;";
        $pdo->exec($sql);
        $sql = "CREATE TABLE IF NOT EXISTS `test` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(32) COLLATE utf8_bin NOT NULL,
                  `age` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
        $pdo->exec($sql);
        $sql = "INSERT INTO `test` (`name`, `age`) VALUES('test', '22');";
        $pdo->exec($sql);
        $sql = "INSERT INTO `test` (`name`, `age`) VALUES('test22', '26');";
        $pdo->exec($sql);
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

    public function testReconnect()
    {
        $this->db->usePdo('read');
        $this->db->disconnect('read');
        $this->assertNull($this->db->getActivePdo());
        $this->db->reconnect('read');
        $this->assertInstanceOf('\PDO', $this->db->getActivePdo());

        $this->db->usePdo('write');
        $this->db->disconnect('write');
        $this->assertNull($this->db->getActivePdo());
        $this->db->reconnect('write');
        $this->assertInstanceOf('\PDO', $this->db->getActivePdo());
    }

    public function testSelect()
    {
        $this->initTable();
        $this->db->usePdo('read');
        $sql = "SELECT * FROM `test` WHERE `name` = :name";

        // error name
        $result = $this->db->selectOne($sql, array('name' => 'test_test_error'));
        $this->assertEquals([], $result);


        $result = $this->db->selectOne($sql, array('','name' => 'test'));
        $this->assertNotEmpty($result);
        $this->assertEquals('test', $result['name']);
        $this->assertEquals(22, $result['age']);

        // error name
        $result = $this->db->select($sql, array('name' => 'test_test_error'));
        $this->assertEquals([], $result);

        $result = $this->db->select($sql, array('name' => 'test'));
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals('test', $result[0]['name']);
        $this->assertEquals(22, $result[0]['age']);

        $sql = "SELECT * FROM `test` WHERE `name` = :name AND `age` = :age";
        $result = $this->db->select($sql, array('age'=>22, 'name' => 'test'));
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals('test', $result[0]['name']);
        $this->assertEquals(22, $result[0]['age']);

        $sql = "SELECT * FROM `test` WHERE `name` = ? AND `age` = ?";
        $result = $this->db->select($sql, array('test', '22'));
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals('test', $result[0]['name']);
        $this->assertEquals(22, $result[0]['age']);
    }

    public function testSelectThrowException()
    {
        $this->initTable();
        $this->db->usePdo('read');
        $sql = "SELECT * FROM `test` WHERE `name` = ':name'";

        // throw exception
        $this->setExpectedException('Simple\Database\DbException');
        $this->db->selectOne($sql, array('name' => 'test_test_error'));
    }

    public function testInsert()
    {
        $this->initTable();
        $this->db->usePdo('write');
        $sql = "INSERT INTO `test` (`name`, `age`) VALUES(:name, :age);";

        $this->assertTrue($this->db->insert($sql, array('name' => 'test12', 'age'=>34)));
        $this->assertTrue($this->db->lastInsertId() > 0);
        $this->assertTrue($this->db->insert($sql, array('name' => 'test122', 'age'=>34)));
        $this->assertTrue($this->db->lastInsertId() > 0);
    }

    public function testUpdate()
    {
        $this->initTable();
        $this->db->usePdo('write');

        $sql        = "UPDATE `test` SET `name` = :name, `age` = :age WHERE `id` = :id";
        $rowCount   = $this->db->update($sql, ['id'=>1, 'age'=>16, 'name'=>'my_test']);
        $this->assertEquals(1, $rowCount);

        $sql = "SELECT * FROM `test` WHERE `id` = ?";
        $result = $this->db->select($sql, array(1));
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals('my_test', $result[0]['name']);
        $this->assertEquals(16, $result[0]['age']);

        $sql        = "UPDATE `test` SET `name` = :name, `age` = :age WHERE `id` = :id";
        $rowCount   = $this->db->update($sql, ['id'=>10, 'age'=>16, 'name'=>'my_test']);
        $this->assertEquals(0, $rowCount);
    }

    public function testDelete()
    {
        $this->initTable();
        $this->db->usePdo('write');
        $sql        = "DELETE FROM `test` WHERE `id` = :id";
        $rowCount   = $this->db->delete($sql, ['id' => 1]);
        $this->assertEquals(1, $rowCount);

        $sql = "SELECT * FROM `test` WHERE `id` = ?";
        $result = $this->db->select($sql, array(1));
        $this->assertEquals(0, sizeof($result));
    }

    public function testTransaction()
    {
        $this->initTable();
        $this->db->usePdo('write');

        $this->db->beginTransaction();
        $sql = "UPDATE `test` SET `name`=:name WHERE id =:id";
        $this->db->update($sql, ['id'=>1, 'name'=>'my_test']);
        $sql = "UPDATE `test` SET `name`=:name WHERE id =:id";
        $this->db->update($sql, ['id'=>2, 'name'=>'my_test_22']);
        $this->db->commit();

        $sql = "SELECT * FROM `test` WHERE `id` IN (1,2);";
        $result = $this->db->select($sql);
        $this->assertEquals(2, sizeof($result));
        $this->assertEquals('my_test', $result[0]['name']);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('my_test_22', $result[1]['name']);
        $this->assertEquals(2, $result[1]['id']);

        $this->db->beginTransaction();
        $sql = "UPDATE `test` SET `name`=:name WHERE id =:id";
        $this->db->update($sql, ['id'=>1, 'name'=>'my_test_333']);
        $sql = "UPDATE `test` SET `name`=:name WHERE id =:id";
        $this->db->update($sql, ['id'=>2, 'name'=>'my_test_44']);
        $this->db->rollBack();

        $sql = "SELECT * FROM `test` WHERE `id` IN (1,2);";
        $result = $this->db->select($sql);
        $this->assertEquals(2, sizeof($result));
        $this->assertEquals('my_test', $result[0]['name']);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('my_test_22', $result[1]['name']);
        $this->assertEquals(2, $result[1]['id']);
    }
}
