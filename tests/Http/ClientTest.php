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

        $this->client->setSslVerifyPeer(true);
        $this->assertTrue($this->client->isSslVerifyPeer());

        $this->client->setSslVerifyHost(true);
        $this->assertEquals(2, $this->client->getSslVerifyHost());

        $this->client->setSslVerifyHost(false);
        $this->assertEquals(0, $this->client->getSslVerifyHost());

        $this->client->setUseCert(true);
        $this->assertTrue($this->client->isUseCert());

        $ua = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:47.0) Gecko/20100101 Firefox/47.0";
        $this->client->setUserAgent($ua);
        $this->assertEquals($ua, $this->client->getUserAgent());

        $refere = 'http://127.0.0.1';
        $this->client->setReferer($refere);
        $this->assertEquals($refere, $this->client->getReferer());

        $caInfo = '/cert/ca.crt';
        $this->client->setCaInfo($caInfo);
        $this->assertEquals($caInfo, $this->client->getCaInfo());

        $headerArray = array(
            "POST /test.php HTTP/1.0",
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: \"run\"",
            "Content-length: 50",
            "Authorization: Basic " . base64_encode('Cache-Control: no-cache')
        );

        $this->client->setHeaderArray($headerArray);
        $this->assertEquals($headerArray, $this->client->getHeaderArray());

        $postData = array('name' => 'Foo', 'file' => '@/home/user/test.png');
        $this->client->setPostDataArray($postData);
        $this->assertEquals($postData, $this->client->getPostDataArray());

        $this->client->setPostFields("aaa=6&bb=9");
        $this->assertEquals("aaa=6&bb=9", $this->client->getPostFields());

        $this->client->setProxyHost("127.0.0.1");
        $this->assertEquals("127.0.0.1", $this->client->getProxyHost());
        $this->client->setProxyPort(8080);
        $this->assertEquals(8080, $this->client->getProxyPort());

        $this->client->useCert(Client::CERT_TYPE_DER, __DIR__ . '/apiclient_cert.pem', __DIR__ . '/apiclient_key.pem');
        $this->client->useCert(Client::CERT_TYPE_ENG, __DIR__ . '/apiclient_cert.pem', __DIR__ . '/apiclient_key.pem');
        $this->client->useCert(10, __DIR__ . '/apiclient_cert.pem', __DIR__ . '/apiclient_key.pem');
    }

    public function testGetAndReturnString()
    {
        $this->client->setUrl('http://127.0.0.1/test.php');
        $this->client->setHeader(true);
        $this->assertTrue($this->client->isHeader());
        $this->assertTrue($this->client->exec());
        $this->assertTrue(strlen($this->client->getResponse()) > 0);
        $this->assertTrue(strlen($this->client->getResponseHeader()) > 0);
        $this->assertTrue($this->client->getResponseCode() == 200);
    }

    public function testUploadImage()
    {
        $this->client->setUrl('http://127.0.0.1/test/upload_test.php');
        $this->client->setMethod(Client::METHOD_UPLOAD);
        $this->client->setPostDataArray(['file' => curl_file_create(__FILE__)]);
        $this->assertTrue($this->client->exec());
    }

    public function testPost()
    {
        $this->client->setUrl('http://127.0.0.1/test/upload_test.php');
        $this->client->setMaxRedirects(2);
        $this->client->setMethod(Client::METHOD_POST);

        $ua = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:47.0) Gecko/20100101 Firefox/47.0";
        $this->client->setUserAgent($ua);

        $refere = 'http://127.0.0.1';
        $this->client->setReferer($refere);

        $this->client->setSslVerifyHost(true);
        $this->client->setSslVerifyPeer(true);
        $caInfo = __DIR__ . '/cacert.pem';
        $this->client->setCaInfo($caInfo);

        $this->client->useCert(Client::CERT_TYPE_PEM, __DIR__ . '/apiclient_cert.pem', __DIR__ . '/apiclient_key.pem');

        $headerArray = array(
            "Accept: text/html",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
        );
        $this->client->setHeaderArray($headerArray);

        $this->client->setProxyHost("127.0.0.1");
        $this->client->setProxyPort(8080);

        $this->assertFalse($this->client->exec());

        $postData = array('name' => 'Foo', 'file' => '/home/user/test.png');
        $this->client->setPostDataArray($postData);
        $this->assertFalse($this->client->exec());
    }
}
