<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/14
 * Time: 下午9:54
 */

namespace Simple\Filesystem;

use FilesystemIterator;

class Filesystem
{
    /**
     * Determine if a file exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function get($path)
    {
        if ($this->isFile($path)) return file_get_contents($path);

        throw new FileNotFoundException("File does not exist at path {$path}");
    }

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  bool  $lock
     * @return int
     */
    public function put($path, $contents, $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * get the file's real path
     *
     * @param string $path
     * @return string
     */
    public function realPath($path)
    {
        return realpath($path);
    }

    /**
     * Prepend to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public function prepend($path, $data)
    {
        if ($this->exists($path))
        {
            return $this->put($path, $data.$this->get($path));
        }

        return $this->put($path, $data);
    }

    /**
     * Append to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public function append($path, $data)
    {
        return file_put_contents($path, $data, FILE_APPEND);
    }

    /**
     * Delete the file at a given path.
     *
     * @param  string  $path
     * @return bool
     */
    public function delete($path)
    {
        return unlink($path);
    }

    /**
     * Move a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public function move($path, $target)
    {
        return rename($path, $target);
    }

    /**
     * Copy a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public function copy($path, $target)
    {
        return copy($path, $target);
    }

    /**
     * Extract the file name from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function name($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Extract the file extension from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get the file type of a given file.
     *
     * @param  string  $path
     * @return string
     */
    public function type($path)
    {
        return filetype($path);
    }

    /**
     * Get the mime-type of a given file.
     *
     * @param  string  $path
     * @return string|false
     */
    public function mimeType($path)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int
     */
    public function size($path)
    {
        return filesize($path);
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified($path)
    {
        return filemtime($path);
    }

    /**
     * Determine if the given path is a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function isDirectory($directory)
    {
        return is_string($directory) && is_dir($directory);
    }

    /**
     * Determine if the given path is writable.
     *
     * @param  string  $path
     * @return bool
     */
    public function isWritable($path)
    {
        return is_string($path) && is_writable($path);
    }

    /**
     * Determine if the given path is readable.
     * @param $path
     * @return bool
     */
    public function isReadable($path)
    {
        return is_string($path) && is_readable($path);
    }

    /**
     * Determine if the given path is executable.
     * @param string $path
     * @return bool
     */
    public function isExecutable($path)
    {
        return is_string($path) && is_executable($path);
    }

    /**
     * Determine if the given path is a file.
     *
     * @param  string  $file
     * @return bool
     */
    public function isFile($file)
    {
        return is_string($file) && is_file($file);
    }

    /**
     * Find path names matching a given pattern.
     *
     * @param  string  $pattern
     * @param  int     $flags
     * @return array
     */
    public function glob($pattern, $flags = 0)
    {
        return glob($pattern, $flags);
    }

    /**
     * Returns parent directory's path
     *
     * @param string $path
     * @return string
     */
    public function dirName($path)
    {
        return dirname($path);
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @param  int     $mode
     * @param  bool    $recursive
     * @param  bool    $force
     * @return bool
     */
    public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false)
    {
        if ($force)
        {
            return @mkdir($path, $mode, $recursive);
        }

        return mkdir($path, $mode, $recursive);
    }

    /**
     * Recursively delete a directory.
     *
     * The directory itself may be optionally preserved.
     *
     * @param  string  $directory
     * @param  bool    $preserve
     * @return bool
     */
    public function deleteDirectory($directory, $preserve = false)
    {
        if ( ! $this->isDirectory($directory)) return false;

        $items = new FilesystemIterator($directory);

        foreach ($items as $item)
        {
            // If the item is a directory, we can just recurse into the function and
            // delete that sub-directory otherwise we'll just delete the file and
            // keep iterating through each file until the directory is cleaned.
            if ($item->isDir() && ! $item->isLink())
            {
                $this->deleteDirectory($item->getPathname());
            }

            // If the item is just a file, we can go ahead and delete it since we're
            // just looping through and waxing all of the files in this directory
            // and calling directories recursively, so we delete the real path.
            else
            {
                $this->delete($item->getPathname());
            }
        }

        if ( ! $preserve) @rmdir($directory);

        return true;
    }

    /**
     * get all files in directory
     *
     * @param string $directory
     * @param bool $recursive
     * @return array
     */
    public function files($directory, $recursive = false)
    {
        $result     = [];
        $recursive  = boolval($recursive);

        if ( ! $this->isDirectory($directory)) return [];

        $items = new FilesystemIterator($directory);

        foreach ($items as $item) {
            if ($item->isFile()) {
                $result[]   = $item->getPathname();
            } elseif ($recursive && $item->isDir() && ! $item->isLink()) {
                $result     += $this->files($item->getPathname());
            }
        }

        return $result;
    }

    /**
     * Empty the specified directory of all files and folders.
     *
     * @param  string  $directory
     * @return bool
     */
    public function cleanDirectory($directory)
    {
        return $this->deleteDirectory($directory, true);
    }
}
