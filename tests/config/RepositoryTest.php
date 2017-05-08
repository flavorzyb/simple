<?php
namespace Simple\Config;

use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    public function testOptionIsMutable()
    {
        $config = new Repository();
        $this->assertEquals([], $config->all());

        $data   = ["key" => 111, "key2" => 22222];
        $config = new Repository($data);
        $this->assertEquals($data, $config->all());

        $this->assertEquals($data['key'], $config['key']);
        $this->assertTrue($config->offsetExists("key"));
        $config['key3'] = 1231;
        $this->assertEquals(1231, $config['key3']);

        unset($config['key']);
        $this->assertFalse($config->offsetExists('key'));
    }
}
