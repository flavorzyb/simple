<?php
namespace Simple\Controller;

use Closure;
use PHPUnit\Framework\TestCase;

class MyController extends Controller
{
}

class MyControllerMiddleware
{
    public function handle(Closure $next)
    {
        return $next();
    }
}

class ControllerTest extends TestCase
{
    public function testTemplateEngineIsMutable()
    {
        $controller = new MyController();

        $this->assertNotNull($controller->getTemplateEngine());

        $obj = new \stdClass();
        $controller->setTemplateEngine($obj);
        $this->assertEquals($obj, $controller->getTemplateEngine());

        $this->assertNull($controller->getResourcePath());
        $controller->setResourcePath(__DIR__);
        $this->assertEquals(__DIR__, $controller->getResourcePath());

        $this->assertNull($controller->getCompilePath());
        $controller->setCompilePath(__DIR__);
        $this->assertEquals(__DIR__, $controller->getCompilePath());
        self::assertEquals([], $controller->getMiddleware());

        $middlewareArray = [new MyControllerMiddleware()];
        $controller->setMiddleware($middlewareArray);
        self::assertEquals($middlewareArray, $controller->getMiddleware());
    }
}
