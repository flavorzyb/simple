<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 下午5:28
 */

namespace Simple\Redis;

use Mockery as m;
use Simple\Config\Repository;

class RedisServerTest extends \PHPUnit_Framework_TestCase
{
    protected function setAndGet(array $servers)
    {
        $redisServer = new RedisServer($servers);
        $redisServer->setPrefix("rs_");
        $this->assertEquals('rs_', $redisServer->getPrefix());

        $redisServer->getClient('server1')->set("key", 111);
        $this->assertEquals(111, $redisServer->getClient('server1')->get("key"));

        $redisServer->getClient('server2')->set("key", 111);
        $this->assertEquals(111, $redisServer->getClient('server2')->get("key"));

        $this->assertEquals(111, $redisServer->getClient('server2_no_exists')->get("key"));

        $redisServer = new RedisServer($servers);
        $this->assertEquals(111, $redisServer->getHashClient("key")->get("key"));
    }

    public function testSetAndGet()
    {
        $servers = [
            'server1' => ['host'=> '127.0.0.1', 'port'=>6379, 'timeout'=>100],
            'server2' => ['host'=> '127.0.0.1', 'port'=>6379, 'timeout'=>100]
        ];

        $this->setAndGet($servers);

        $servers = [
            'server1' => ['host'=> '127.0.0.1', 'port'=>6379],
            'server2' => ['host'=> '127.0.0.1', 'port'=>6379]
        ];

        $this->setAndGet($servers);

        $servers = [
            ['host'=> '127.0.0.1', 'port'=>6379],
            ['host'=> '127.0.0.1', 'port'=>6379]
        ];

        $this->setAndGet($servers);

        $servers = [
            ['host'=> '127.0.0.1', 'port'=>6379],
        ];

        $this->setAndGet($servers);
    }

    public function testSetAndGetWithPersistent()
    {
        $servers = [
            'server1' => ['host'=> '127.0.0.1', 'port'=>6379, 'timeout'=>100, 'persistent'=>true],
            'server2' => ['host'=> '127.0.0.1', 'port'=>6379, 'timeout'=>100, 'persistent'=>true]
                ];

        $this->setAndGet($servers);

        $redisServer = new RedisServer($servers);
        $this->assertEquals(2, $redisServer->getServerCount());
        $this->assertInstanceOf('\Redis', $redisServer->getDefaultClient());
    }

    /**
     * @expectedException \Simple\Redis\RedisException
     */
    public function testInitThrowException()
    {
        $servers = [
            'server1' => ['host'=> '127.0.0.1', 'port'=>6309, 'timeout'=> 1],
        ];

        $this->setAndGet($servers);
    }

    /**
     * @expectedException \Simple\Redis\RedisException
     */
    public function testMissServerName()
    {
        $servers = [
            'server1' => ['server_name'=> '127.0.0.1', 'port'=>6309],
        ];

        $this->setAndGet($servers);
    }

    /**
     * @expectedException \Simple\Redis\RedisException
     */
    public function testMissServerPort()
    {
        $servers = [
            'server1' => ['host'=> '127.0.0.1', 'port_int'=>6309],
        ];

        $this->setAndGet($servers);
    }

    /**
     * @expectedException \Simple\Redis\RedisException
     */
    public function testEmptyThrowException()
    {
        $servers = [
        ];

        $this->setAndGet($servers);
    }

    public function testSetAndGetWithMock()
    {
        $servers = [
            'server1' => ['host' => '127.0.0.1', 'port' => 6379, 'timeout' => 100, 'password' => '111222'],
            'server2' => ['host' => '127.0.0.1', 'port' => 6379, 'timeout' => 100, 'password' => '111222']
        ];

        $redisServer = new RedisServerMock($servers);
        $redisServer->setPrefix("rs_");
        $this->assertEquals('rs_', $redisServer->getPrefix());

        $redisServer->getClient('server1')->set("key", 111);
        $this->assertEquals(111, $redisServer->getClient('server1')->get("key"));
    }
}

class RedisServerMock extends RedisServer {

    protected function createClient(Repository $config)
    {
        $result = m::mock('\Redis');
        $result->shouldReceive('ping')->andThrow(new \RedisException());
        $result->shouldReceive('set')->andReturn(true);
        $result->shouldReceive('auth')->andReturn(true);
        $result->shouldReceive('get')->andReturn(111);
        return $result;
    }

}