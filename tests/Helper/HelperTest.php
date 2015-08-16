<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 下午12:25
 */

namespace Simple\Helper;


use Simple\Filesystem\Filesystem;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFileSystem()
    {
        $this->assertTrue(Helper::getFileSystem() instanceof Filesystem);
        $this->assertTrue(Helper::getFileSystem() instanceof Filesystem);
    }
}
