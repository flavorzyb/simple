<?php
namespace Simple\MiddleWare;

use PHPUnit\Framework\TestCase;
use Simple\Support\CSRFToken;

class VerifyCsrfTokenTest extends TestCase
{
    /**
     * @var VerifyCsrfToken
     */
    protected $verifyCsrfToken = null;
    /**
     * @var CSRFToken
     */
    protected $token = null;
    protected function setUp()
    {
        parent::setUp();
        $this->token = new CSRFToken();
        $this->token->cleanCSRFString();
        $this->verifyCsrfToken = new VerifyCsrfToken($this->token);
    }

    public function testHandleMethodGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = $this->verifyCsrfToken->handle(function () {return 10;});
        self::assertEquals(10, $result);
    }

    /**
     * @expectedException \Simple\MiddleWare\TokenMismatchException
     */
    public function testHandleMethodPostVerifyFail()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->verifyCsrfToken->handle(function () {return 10;});
    }

    /**
     * @expectedException \Simple\MiddleWare\TokenMismatchException
     */
    public function testHandleMethodPostVerifyFailErrorToken()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $_POST['_token'] = [123, 222];
        $this->verifyCsrfToken->handle(function () {return 10;});
    }

    public function testHandleMethodPostVerifySuccess()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->token->registerCSRFString();
        $tokenString =$this->token->getCSRFString();
        $_POST['_token'] = $tokenString;
        $result = $this->verifyCsrfToken->handle(function () {return 10;});
        self::assertEquals(10, $result);
    }
}
