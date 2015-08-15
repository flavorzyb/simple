<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/15
 * Time: 下午3:08
 */

namespace Simple\Session;

use SessionHandlerInterface;

use Simple\Filesystem\Filesystem;

class FileSessionHandler implements SessionHandlerInterface
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $fileSystem   = null;

    /**
     * The path where sessions should be stored.
     *
     * @var string
     */
    protected $path         = null;

    /**
     * FileSessionHandler constructor.
     * @param Filesystem $filesystem
     * @param string $path
     */
    public function __construct(Filesystem $filesystem, $path)
    {
        $this->fileSystem   = $filesystem;
        $this->path         = $path;
    }

    /**
     * Close the session
     * @link http://php.net/manual/en/sessionhandlerinterface.close.php
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function close()
    {
        return true;
    }

    /**
     * Destroy a session
     * @link http://php.net/manual/en/sessionhandlerinterface.destroy.php
     * @param string $session_id The session ID being destroyed.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function destroy($session_id)
    {
        return $this->fileSystem->delete($this->path . DIRECTORY_SEPARATOR . $session_id);
    }

    /**
     * Cleanup old sessions
     * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
     * @param int $maxLifeTime <p>
     * Sessions that have not updated for
     * the last maxLifeTime seconds will be removed.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function gc($maxLifeTime)
    {
        $fileSystem = $this->fileSystem;
        $files      = $fileSystem->files($this->path);
        $time       = time() - intval($maxLifeTime);

        foreach ($files as $file) {
            if ($fileSystem->lastModified($file) < $time) {
                $fileSystem->delete($file);
            }
        }

        return true;
    }

    /**
     * Initialize session
     * @link http://php.net/manual/en/sessionhandlerinterface.open.php
     * @param string $save_path The path where to store/retrieve the session.
     * @param string $sessionId The session id.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function open($save_path, $sessionId)
    {
        return true;
    }


    /**
     * Read session data
     * @link http://php.net/manual/en/sessionhandlerinterface.read.php
     * @param string $sessionId The session id to read data for.
     * @return string <p>
     * Returns an encoded string of the read data.
     * If nothing was read, it must return an empty string.
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function read($sessionId)
    {
        if ($this->fileSystem->exists($path = $this->path.'/'.$sessionId))
        {
            return $this->fileSystem->get($path);
        }

        return '';
    }

    /**
     * Write session data
     * @link http://php.net/manual/en/sessionhandlerinterface.write.php
     * @param string $session_id The session id.
     * @param string $session_data <p>
     * The encoded session data. This data is the
     * result of the PHP internally encoding
     * the $_SESSION superglobal to a serialized
     * string and passing it as this parameter.
     * Please note sessions use an alternative serialization method.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function write($session_id, $session_data)
    {
        return $this->fileSystem->put($this->path.'/'.$session_id, $session_data, true) > 0;
    }
}
