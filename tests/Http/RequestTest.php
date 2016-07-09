<?php

namespace Http;


use Simple\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
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
    }
}