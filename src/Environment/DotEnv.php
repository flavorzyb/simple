<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/18
 * Time: 上午9:20
 */

namespace Simple\Environment;


class DotEnv
{
    protected $filePath = null;

    /**
     * construct
     * @param string $path
     * @param string $file
     */
    public function __construct($path, $file = ".env")
    {
        $this->filePath = $this->getFilePath($path, $file);
    }

    public function load()
    {
    }

    /**
     * Returns the full path to the file.
     *
     * @param string $path
     * @param string $file
     * @return string
     */
    protected function getFilePath($path, $file)
    {
        if (!is_string($file)) {
            $file   = ".env";
        }

        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
    }
}
