<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/21
 * Time: 下午2:49
 */

namespace Simple\Http;


class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $client = null;

    protected function setUp()
    {
        $this->client   = new Client();
    }

    public function testOptionsIsMutable()
    {
        $this->assertEquals(Client::HTTP_VERSION_11, $this->client->getHttpVersion());
        $this->client->setHttpVersion('test');
        $this->assertEquals(Client::HTTP_VERSION_11, $this->client->getHttpVersion());

        $this->client->setHttpVersion(Client::HTTP_VERSION_10);
        $this->assertEquals(Client::HTTP_VERSION_10, $this->client->getHttpVersion());

        $this->client->setMaxRedirects(-12);
        $this->assertEquals(0, $this->client->getMaxRedirects());

        $this->client->setMaxRedirects(12);
        $this->assertEquals(12, $this->client->getMaxRedirects());

        $this->assertEquals(Client::METHOD_GET, $this->client->getMethod());
        $this->client->setMethod("test");
        $this->assertEquals(Client::METHOD_GET, $this->client->getMethod());
        $this->client->setMethod(Client::METHOD_POST);
        $this->assertEquals(Client::METHOD_POST, $this->client->getMethod());


        $this->assertEquals(Client::TIME_OUT, $this->client->getTimeout());
        $this->client->setTimeout(12);
        $this->assertEquals(12, $this->client->getTimeout());
        $this->client->setTimeout('-12');
        $this->assertEquals(0, $this->client->getTimeout());

        $this->assertEquals('', $this->client->getUrl());
        $this->client->setUrl('http://www.163.com');
        $this->assertEquals('http://www.163.com', $this->client->getUrl());

        $this->assertEquals(Client::RETRY_COUNT, $this->client->getRetryCount());
        $this->client->setRetryCount(10);
        $this->assertEquals(10, $this->client->getRetryCount());
        $this->client->setRetryCount(-10);
        $this->assertEquals(0, $this->client->getRetryCount());
    }

    public function testGetAndReturnString()
    {
        $this->client->setUrl('http://127.0.0.1/test.php');
        $this->client->setHeader(true);
        $this->assertTrue($this->client->exec());
        $this->assertTrue(strlen($this->client->getResponse()) > 0);
//        var_dump($this->client->getResponseCode(), $this->client->getResponseHeader(), $this->client->getResponse());
    }
}
