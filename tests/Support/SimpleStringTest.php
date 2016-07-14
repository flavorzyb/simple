<?php
namespace Simple\Support;

class SimpleStringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleString
     */
    protected $simpleString = null;

    public function setUp()
    {
        parent::setUp();
        $this->simpleString = new SimpleString();

    }

    public function testIsSameStringReturnBoolean()
    {
        $this->assertTrue($this->simpleString->isSameString('', ''));

        $this->assertFalse($this->simpleString->isSameString('2', 2));

        $this->assertFalse($this->simpleString->isSameString('this is a test string', 'this is a test string2'));
    }

    public function testGeneratorPasswordReturnString()
    {
        $string = "this is a test";
        $result = $this->simpleString->generatorPassword($string);
        $this->assertEquals(32, strlen($result));

        $this->assertNotEquals($this->simpleString->generatorPassword('this is a test'), $this->simpleString->generatorPassword('this is a test '));
    }

    public function testGeneratorAdminPasswordReturnString()
    {
        $slat = "testSlat";
        $this->simpleString->setCode($slat);
        self::assertEquals($slat, $this->simpleString->getCode());
        $string = "this is a test";
        $result = $this->simpleString->generatorAdminPassword($string);
        $this->assertEquals(32, strlen($result));

        $this->assertNotEquals($this->simpleString->generatorAdminPassword('this is a test'), $this->simpleString->generatorAdminPassword('this is a test '));
    }
}
