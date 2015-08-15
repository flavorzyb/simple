<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 上午10:27
 */

namespace Simple\Log;

use Simple\Filesystem\Filesystem;

class Writer
{
    /**
     * Log Type DEBUG
     */
    const TYPE_DEBUG    = 100;
    /**
     * Log Type INFO
     */
    const TYPE_INFO     = 200;
    /**
     * Log Type NOTICE
     */
    const TYPE_NOTICE   = 300;
    /**
     * Log Type WARNING
     */
    const TYPE_WARNING  = 400;
    /**
     * Log Type ERROR
     */
    const TYPE_ERROR    = 500;
    /**
     * Log Type API
     */
    const TYPE_API      = 600;

    // 1GB
    const MAX_FILE_SIZE = 1024000000;

    /**
     * @var string
     */
    private $dirPath    = null;

    /**
     * @var Filesystem
     */
    private $filesystem = null;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem   = $filesystem;
    }

    /**
     * get the log dir path
     *
     * @return string
     */
    public function dirPath()
    {
        return $this->dirPath;
    }

    /**
     * set the log dir path
     *
     * @param string $dirPath
     */
    public function setDirPath($dirPath)
    {
        $dirPath    = trim($dirPath);
        if ($this->filesystem->isDirectory($dirPath)) {
            $this->dirPath = $this->filesystem->realPath($dirPath);
        }
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * write log to file
     *
     * @param int $type
     * @param string $msg
     * @return boolean
     */
    private function log($type, $msg)
    {
        $msg    = trim($msg);
        if ('' == $msg) {
            return false;
        }

        $filesystem = $this->filesystem;
        if (!$filesystem->isDirectory($this->dirPath)) {
            return false;
        }

        $type       = intval($type);
        $subPath    = sprintf("%s%s%s%s", date('Y'), DIRECTORY_SEPARATOR, date('m'), DIRECTORY_SEPARATOR);
        $file       = $this->dirPath . DIRECTORY_SEPARATOR . $subPath;
        $str        = "";
        switch($type) {
            case self::TYPE_DEBUG:
                $file   .= "debug";
                $str     = "[debug]";
                break;
            case self::TYPE_INFO:
                $file   .= "info";
                $str     = "[info]";
                break;
            case self::TYPE_NOTICE:
                $file   .= "notice";
                $str     = "[notice]";
                break;
            case self::TYPE_WARNING:
                $file   .= "warning";
                $str     = "[warning]";
                break;
            case self::TYPE_ERROR:
                $file   .= "error";
                $str     = "[error]";
                break;
            case self::TYPE_API:
                $file   .= "api";
                $str     = "[api]";
                break;
            default:
                return false;
        }

        $file       .= '_' . date('Y_m_d');
        $target     = $file . '_' . date('His') . '.log';
        $file       .= '.log';

        // check log file size
        if ($filesystem->isFile($file) && ($filesystem->size($file) > self::MAX_FILE_SIZE)) {
            $filesystem->move($file, $target);
        }

        // Determine if log dir exists
        $dirPath    = $filesystem->dirName($file);
        if (!$filesystem->isDirectory($dirPath)) {
            $filesystem->makeDirectory($dirPath, 0755, true, true);
        }

        $str        .= date('Y-m-d H:i:s') . '|' . $msg . "\n";

        return $filesystem->put($file, $str, true) > 0;
    }

    /**
     * write debug log
     *
     * @param string $msg
     * @return bool
     */
    public function debug($msg)
    {
        return $this->log(self::TYPE_DEBUG, $msg);
    }

    /**
     * write info log
     *
     * @param string $msg
     * @return bool
     */
    public function info($msg)
    {
        return $this->log(self::TYPE_INFO, $msg);
    }

    /**
     * write notice log
     *
     * @param string $msg
     * @return bool
     */
    public function notice($msg)
    {
        return $this->log(self::TYPE_NOTICE, $msg);
    }

    /**
     * write warning log
     *
     * @param string $msg
     * @return bool
     */
    public function warning($msg)
    {
        return $this->log(self::TYPE_WARNING, $msg);
    }

    /**
     * write error log
     *
     * @param string $msg
     * @return bool
     */
    public function error($msg)
    {
        return $this->log(self::TYPE_ERROR, $msg);
    }

    /**
     * write api log
     *
     * @param string $msg
     * @return bool
     */
    public function api($msg)
    {
        return $this->log(self::TYPE_API, $msg);
    }
}
