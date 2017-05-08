<?php
namespace Environment;

use PHPUnit\Framework\TestCase;
use Simple\Environment\DotEnv;

class DotEnvTest extends TestCase
{
    public function testOptions() {
        $env = new DotEnv(__DIR__, ".env");
        $data = $env->load();
        $this->assertTrue(sizeof($data) == 8);

        $data = $env->overLoad();
        $this->assertTrue(sizeof($data) == 8);

        $env = new DotEnv(__DIR__, 222);
        $data = $env->load();
        $this->assertTrue(sizeof($data) == 8);
    }
}
