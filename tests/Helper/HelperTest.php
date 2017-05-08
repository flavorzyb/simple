<?php
namespace Simple\Helper;

use PHPUnit\Framework\TestCase;
use Simple\Filesystem\Filesystem;
use Simple\Log\Writer;

class HelperTest extends TestCase
{
    public function testGetFileSystem()
    {
        $this->assertTrue(Helper::getFileSystem() instanceof Filesystem);
        $this->assertTrue(Helper::getFileSystem() instanceof Filesystem);
    }

    public function testLogWriter()
    {
        $this->assertTrue(Helper::getLogWriter() instanceof Writer);
        $this->assertTrue(Helper::getLogWriter() instanceof Writer);
    }
}
