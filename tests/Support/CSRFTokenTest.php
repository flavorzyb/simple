<?php
namespace Simple\Support;

class CSRFTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testCSRFString()
    {
        $token = new CSRFToken();
        $token->cleanCSRFString();
        $this->assertEquals('', $token->getCSRFString());
        $token->registerCSRFString();
        $str = $token->getCSRFString();
        $this->assertTrue(strlen($str) > 0);
        $this->assertTrue($token->matchCSRFString($str));
        $token->cleanCSRFString();

        // change token string
        $token->setTokenKey("yyy-mmm-dd");
        self::assertEquals("yyy-mmm-dd", $token->getTokenKey());
        $token->cleanCSRFString();
        $this->assertEquals('', $token->getCSRFString());
        $token->registerCSRFString();
        $str = $token->getCSRFString();
        $this->assertTrue(strlen($str) > 0);
        $this->assertTrue($token->matchCSRFString($str));
        $token->cleanCSRFString();
    }
}
