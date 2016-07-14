<?php
namespace Simple\Validation;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Validator
     */
    private $validator = null;

    public function setUp()
    {
        parent::setUp();
        $this->validator = new Validator();
    }

    public function testValidateName() {
        $this->assertTrue($this->validator->validateName("flavor"));
        $this->assertFalse($this->validator->validateName(""));
        $this->assertFalse($this->validator->validateName(121));
    }

    public function testValidateEMail() {
        $this->assertTrue($this->validator->validateEmail("haker-haker@163.com"));
        $this->assertFalse($this->validator->validateEmail("haker-haker"));
    }

    public function testValidatePassword() {
        $this->assertTrue($this->validator->validatePassword("this is a password"));
        $this->assertFalse($this->validator->validatePassword("this"));
    }
}
