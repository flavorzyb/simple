<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/17
 * Time: 下午7:30
 */

namespace Simple\Controller;

class MyController extends Controller
{
}

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testTemplateEngineIsMutable()
    {
        $controller = new MyController();

        $this->assertNotNull($controller->getTemplateEngine());

        $obj = new \stdClass();
        $controller->setTemplateEngine($obj);
        $this->assertEquals($obj, $controller->getTemplateEngine());
    }
}
