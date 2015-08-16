<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 下午12:21
 */

namespace Simple\Helper;


use Simple\Filesystem\Filesystem;

class Helper
{
    protected static $class = [];

    /**
     * get Filesystem instance
     *
     * @return Filesystem
     */
    public static function getFileSystem()
    {
        if (isset(self::$class['fileSystem'])) {
            return self::$class['fileSystem'];
        }

        self::$class['fileSystem'] = new Filesystem();
        return self::$class['fileSystem'];
    }
}
