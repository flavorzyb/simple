<?php
namespace Foundation;

use Closure;
use Simple\Foundation\Pipeline;

class TestMiddleware
{
    public function MHandler(Closure $next)
    {
        if ($_SERVER['aaa'] == 1) {
            return false;
        }

        return $next();
    }
}

class TestController {
    private $middleware = null;

    public function __construct()
    {
        $this->middleware = new TestMiddleware();
    }

    public function getMiddleWare()
    {
        return $this->middleware;
    }

    public function index()
    {
        print "aaaaa\n";
        return true;
    }
}

class PipelineTest extends \PHPUnit_Framework_TestCase
{
    public function testThen()
    {
        $pipeLine = new Pipeline();
        $pipeLine->via("MHandler");
        $controller = new TestController();
        $middleware = [$controller->getMiddleWare()];
        $_SERVER['aaa'] = 2;
        $result = $pipeLine->through($middleware)->then(function () use ($controller){ return $controller->index();});
        $this->assertTrue($result);

        $_SERVER['aaa'] = 1;
        $result = $pipeLine->through($middleware)->then(function () use ($controller){ return $controller->index();});
        $this->assertFalse($result);
    }
}
