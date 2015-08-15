<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 下午11:30
 */

namespace Simple\Session;

use SessionHandlerInterface;

abstract class SessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SessionHandlerInterface
     */
    protected $sessionHandler   = null;

    protected $savePath         = null;
    protected $sessionId        = null;
    protected function setUp()
    {
        $this->savePath     = __DIR__ . DIRECTORY_SEPARATOR . '../tmp/' . mt_rand();
        $this->sessionId    = md5(time() . mt_rand());
    }

    protected function setSessionHandler(SessionHandlerInterface $handlerInterface)
    {
        $this->sessionHandler   = $handlerInterface;
    }

    public function testSession()
    {
        $data   = ['uid'=>'12132213'];

        $this->assertTrue($this->sessionHandler->open($this->savePath, $this->sessionId));
        $this->assertEquals("", $this->sessionHandler->read($this->sessionId));
        $this->assertTrue($this->sessionHandler->write($this->sessionId, serialize($data)));
        $this->assertEquals(serialize($data), $this->sessionHandler->read($this->sessionId));
        $this->assertTrue($this->sessionHandler->close());
        $this->assertTrue($this->sessionHandler->destroy($this->sessionId));

        $this->assertTrue($this->sessionHandler->gc(100));
    }
}
