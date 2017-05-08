<?php
namespace Http;

use PHPUnit\Framework\TestCase;
use Simple\Http\Request;

class RequestTest extends TestCase
{
    public function testGetParams()
    {
        $this->assertEquals(12, Request::get(null, 12));

        if (isset($_GET['aaa'])) {
            unset($_GET['aaa']);
        }

        $this->assertEquals(112, Request::get("aaa", 112));
        $_GET['aaa'] = 13;
        $this->assertEquals(13, Request::get("aaa", 112));

        self::assertFalse(Request::isGetMethod());
        $_SERVER['REQUEST_METHOD'] = Request::GET;
        self::assertTrue(Request::isGetMethod());
    }

    public function testPostParams()
    {
        $this->assertEquals(12, Request::post(null, 12));

        if (isset($_POST['aaa'])) {
            unset($_POST['aaa']);
        }

        $this->assertEquals(112, Request::post("aaa", 112));
        $_POST['aaa'] = 3;
        $this->assertEquals(3, Request::post("aaa", 112));

        self::assertFalse(Request::isPostMethod());
        $_SERVER['REQUEST_METHOD'] = Request::POST;
        self::assertTrue(Request::isPostMethod());
    }

    public function testReferer()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            unset($_SERVER['HTTP_REFERER']);
        }

        self::assertEquals('', Request::refererUrl());
        $_SERVER['HTTP_REFERER'] = 'http://www.baidu.com';
        self::assertEquals('http://www.baidu.com', Request::refererUrl());
    }
}
